<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pembungkus API RajaOngkir (dikelola Komerce) untuk pencarian lokasi tujuan,
 * hitung ongkir, dan pelacakan resi. Dokumentasi: https://rajaongkir.com/docs
 *
 * Catatan desain:
 * - API key hanya dipakai di server; browser pembeli tidak pernah melihatnya.
 * - Ongkir diminta SATU KURIR PER REQUEST (bentuk yang terdokumentasi), lalu
 *   dijalankan paralel. Kalau satu kurir gagal, kurir lain tetap tampil.
 * - Hasil di-cache (lihat config/shipping.php) supaya kuota API hemat.
 */
class ShippingService
{
    /** Apakah fitur ongkir otomatis sudah dikonfigurasi di .env? */
    public function isConfigured(): bool
    {
        return filled(config('shipping.api_key')) && filled(config('shipping.origin_id'));
    }

    /** Apakah API key sudah ada? Cukup untuk cari lokasi & lacak resi (tanpa asal pengiriman). */
    public function hasApiKey(): bool
    {
        return filled(config('shipping.api_key'));
    }

    /** Nama tampilan kurir, mis. "jnt" -> "J&T Express". */
    public static function courierName(?string $code): string
    {
        $code = self::normalizeCourier($code);

        return config("shipping.courier_names.$code") ?? strtoupper($code);
    }

    /**
     * Ubah isian kurir (dari checkout atau diketik admin, mis. "J&T", "JNE")
     * menjadi kode API ("jnt", "jne"). Teks yang tidak dikenal dikembalikan
     * huruf kecil apa adanya, dan tidak akan ada di daftar courier_names.
     */
    public static function normalizeCourier(?string $courier): string
    {
        $courier = strtolower(trim((string) $courier));

        return [
            'j&t' => 'jnt',
            'j&t express' => 'jnt',
            'jnt express' => 'jnt',
            'jne express' => 'jne',
            'si cepat' => 'sicepat',
            'pos indonesia' => 'pos',
            'anter aja' => 'anteraja',
        ][$courier] ?? $courier;
    }

    /** Apakah kurir ini bisa dilacak otomatis (kodenya dikenal di config)? */
    public function canTrackCourier(?string $courier): bool
    {
        return $this->hasApiKey()
            && array_key_exists(self::normalizeCourier($courier), config('shipping.courier_names'));
    }

    /**
     * Total berat (gram) isi keranjang. Dihitung dari berat produk di database,
     * bukan dari data yang dikirim browser.
     *
     * @param  array<string, array{product_id: int|string, quantity: int|string}>  $cart
     */
    public function cartWeight(array $cart): int
    {
        $ids = collect($cart)->pluck('product_id')->filter()->unique()->all();
        $products = Product::whereIn('id', $ids)->get()->keyBy('id');

        $total = 0;
        foreach ($cart as $item) {
            $product = $products->get($item['product_id'] ?? null);
            $unit = $product ? $product->shippingWeight() : (int) config('shipping.default_weight');
            $total += $unit * max(1, (int) ($item['quantity'] ?? 1));
        }

        return max(1, $total);
    }

    /*
    |--------------------------------------------------------------------------
    | Pencarian lokasi tujuan
    |--------------------------------------------------------------------------
    */

    /**
     * Cari kecamatan/kelurahan/kode pos.
     *
     * @return list<array{id:int,label:string,province:string,city:string,district:string,subdistrict:string,zip:string}>
     *
     * @throws ShippingException
     */
    public function searchDestinations(string $query, int $limit = 10): array
    {
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        $cacheKey = 'shipping:search:'.md5(mb_strtolower($query).'|'.$limit);

        $results = Cache::remember($cacheKey, config('shipping.cache.destinations'), function () use ($query, $limit) {
            $response = $this->send('mencari lokasi tujuan', fn (PendingRequest $http) => $http->get(
                $this->url('destination/domestic-destination'),
                ['search' => $query, 'limit' => $limit, 'offset' => 0],
            ));

            $rows = $this->dataOrFail($response, 'mencari lokasi tujuan', allowNotFound: true) ?? [];

            return collect($rows)->map(fn ($row) => [
                'id' => (int) ($row['id'] ?? 0),
                'label' => (string) ($row['label'] ?? ''),
                'province' => (string) ($row['province_name'] ?? ''),
                'city' => (string) ($row['city_name'] ?? ''),
                'district' => (string) ($row['district_name'] ?? ''),
                'subdistrict' => (string) ($row['subdistrict_name'] ?? ''),
                'zip' => (string) ($row['zip_code'] ?? ''),
            ])->filter(fn ($row) => $row['id'] > 0)->values()->all();
        });

        // Simpan tiap lokasi berdasarkan ID-nya. Saat checkout, server membaca
        // alamat wilayah dari sini (destinationById), BUKAN dari isian form,
        // supaya pembeli tidak bisa memilih tujuan murah tapi menulis alamat lain.
        foreach ($results as $row) {
            Cache::put($this->destinationKey($row['id']), $row, config('shipping.cache.destinations'));
        }

        return $results;
    }

    /**
     * Lokasi yang sebelumnya dipilih lewat pencarian, atau null kalau sudah
     * kedaluwarsa di cache (pembeli diminta memilih ulang).
     *
     * @return array{id:int,label:string,province:string,city:string,district:string,subdistrict:string,zip:string}|null
     */
    public function destinationById(int $id): ?array
    {
        return Cache::get($this->destinationKey($id));
    }

    /*
    |--------------------------------------------------------------------------
    | Hitung ongkir
    |--------------------------------------------------------------------------
    */

    /**
     * Semua opsi pengiriman (semua kurir yang diaktifkan), termurah dulu.
     *
     * @return list<array{courier:string,courier_name:string,service:string,description:string,cost:int,etd:string}>
     *
     * @throws ShippingException kalau SEMUA kurir gagal dipanggil
     */
    public function calculateCosts(int $destinationId, int $weightGrams): array
    {
        $couriers = config('shipping.couriers');
        $weightGrams = max(1, $weightGrams);

        if (empty($couriers)) {
            return [];
        }

        $origin = (int) config('shipping.origin_id');
        $cacheKey = 'shipping:cost:'.md5(implode('|', [$origin, $destinationId, $weightGrams, implode(',', $couriers)]));

        $partial = false;

        $options = Cache::remember($cacheKey, config('shipping.cache.costs'), function () use ($couriers, $origin, $destinationId, $weightGrams, &$partial) {
            $apiKey = $this->apiKey();
            $timeout = (int) config('shipping.timeout');
            $url = $this->url('calculate/domestic-cost');

            $responses = Http::pool(fn (Pool $pool) => collect($couriers)->map(
                fn (string $courier) => $pool->as($courier)
                    ->withHeaders(['key' => $apiKey])
                    ->acceptJson()
                    ->asForm()
                    ->timeout($timeout)
                    ->post($url, [
                        'origin' => $origin,
                        'destination' => $destinationId,
                        'weight' => $weightGrams,
                        'courier' => $courier,
                    ])
            )->all());

            $options = [];
            $failures = 0;

            foreach ($couriers as $courier) {
                $response = $responses[$courier] ?? null;

                if (! $response instanceof Response) {
                    $failures++;
                    Log::warning('Ongkir: koneksi ke API gagal', ['courier' => $courier]);
                    continue;
                }

                // 400 = rute/kurir ini tidak melayani tujuan tersebut: bukan error,
                // cukup tidak ditampilkan.
                if ($response->status() === 400 || $response->status() === 404) {
                    continue;
                }

                if (! $response->successful()) {
                    $failures++;
                    Log::warning('Ongkir: API menolak permintaan', [
                        'courier' => $courier,
                        'status' => $response->status(),
                        'message' => $response->json('meta.message'),
                    ]);
                    continue;
                }

                foreach ((array) $response->json('data', []) as $row) {
                    $cost = $row['cost'] ?? null;

                    if (! is_numeric($cost) || (int) $cost < 0) {
                        continue;
                    }

                    $code = strtolower((string) ($row['code'] ?? $courier));

                    $options[] = [
                        'courier' => $code,
                        'courier_name' => (string) ($row['name'] ?? self::courierName($code)),
                        'service' => (string) ($row['service'] ?? ''),
                        'description' => (string) ($row['description'] ?? ''),
                        'cost' => (int) $cost,
                        'etd' => (string) ($row['etd'] ?? ''),
                    ];
                }
            }

            if ($failures === count($couriers)) {
                throw new ShippingException('Layanan ongkir sedang tidak bisa dihubungi. Coba lagi beberapa saat lagi.');
            }

            // Ada kurir yang gagal sesaat (timeout, dsb): tampilkan yang berhasil,
            // tapi jangan simpan di cache supaya kurir itu muncul lagi di percobaan berikutnya.
            $partial = $failures > 0;

            usort($options, fn ($a, $b) => $a['cost'] <=> $b['cost']);

            return $options;
        });

        if ($partial) {
            Cache::forget($cacheKey);
        }

        return $options;
    }

    /**
     * Ambil satu opsi tertentu (kurir + layanan) untuk menentukan ongkir yang
     * SAH di server saat checkout, berapa pun yang dikirim browser.
     *
     * @return array{courier:string,courier_name:string,service:string,description:string,cost:int,etd:string}|null
     *
     * @throws ShippingException
     */
    public function quote(int $destinationId, int $weightGrams, string $courier, string $service): ?array
    {
        $courier = self::normalizeCourier($courier);
        $service = trim($service);

        foreach ($this->calculateCosts($destinationId, $weightGrams) as $option) {
            if ($option['courier'] === $courier && strcasecmp($option['service'], $service) === 0) {
                return $option;
            }
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Lacak resi
    |--------------------------------------------------------------------------
    */

    /**
     * Riwayat perjalanan paket.
     *
     * @param  string|null  $phone  Nomor HP penerima; sebagian kurir (mis. JNE) meminta 5 digit terakhirnya.
     * @return array{delivered:bool,status:string,courier:string,waybill:string,receiver:string,origin:string,destination:string,history:list<array{date:string,time:string,description:string,city:string}>}|null
     *         null kalau resi belum terdaftar di kurir
     *
     * @throws ShippingException
     */
    public function track(string $courier, string $awb, ?string $phone = null): ?array
    {
        $courier = self::normalizeCourier($courier);
        $awb = trim($awb);
        $last5 = $phone ? substr(preg_replace('/\D+/', '', $phone), -5) : '';

        if ($courier === '' || $awb === '') {
            return null;
        }

        $cacheKey = 'shipping:track:'.md5("$courier|$awb|$last5");

        // Hasil "belum ditemukan" juga di-cache sebentar supaya tombol Lacak yang
        // ditekan berulang tidak menghabiskan kuota.
        $cached = Cache::remember($cacheKey, config('shipping.cache.tracking'), function () use ($courier, $awb, $last5) {
            // Dokumentasi menampilkan awb/courier di query string tetapi
            // mendaftarkannya sebagai field body; dikirim di keduanya supaya aman.
            $params = ['awb' => $awb, 'courier' => $courier];
            if ($last5 !== '') {
                $params['last_phone_number'] = $last5;
            }

            $response = $this->send('melacak resi', fn (PendingRequest $http) => $http
                ->asForm()
                ->withQueryParameters($params)
                ->post($this->url('track/waybill'), $params));

            $data = $this->dataOrFail($response, 'melacak resi', allowNotFound: true);

            if ($data === null) {
                return ['found' => false];
            }

            return ['found' => true, 'result' => [
                'delivered' => (bool) ($data['delivered'] ?? false),
                'status' => (string) ($data['summary']['status'] ?? ($data['delivery_status']['status'] ?? '')),
                'courier' => (string) ($data['summary']['courier_name'] ?? self::courierName($courier)),
                'waybill' => (string) ($data['summary']['waybill_number'] ?? $awb),
                'receiver' => (string) ($data['summary']['receiver_name'] ?? ''),
                'origin' => (string) ($data['summary']['origin'] ?? ''),
                'destination' => (string) ($data['summary']['destination'] ?? ''),
                // Dari terbaru ke terlama, seperti yang diharapkan pembeli.
                'history' => collect($data['manifest'] ?? [])
                    ->map(fn ($m) => [
                        'date' => (string) ($m['manifest_date'] ?? ''),
                        'time' => (string) ($m['manifest_time'] ?? ''),
                        'description' => (string) ($m['manifest_description'] ?? ''),
                        'city' => (string) ($m['city_name'] ?? ''),
                    ])
                    ->sortByDesc(fn ($m) => $m['date'].' '.$m['time'])
                    ->values()
                    ->all(),
            ]];
        });

        return $cached['found'] ? $cached['result'] : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Internal
    |--------------------------------------------------------------------------
    */

    private function apiKey(): string
    {
        $key = (string) config('shipping.api_key');

        if ($key === '') {
            throw new ShippingException('Layanan ongkir belum dikonfigurasi.');
        }

        return $key;
    }

    private function url(string $path): string
    {
        return rtrim((string) config('shipping.base_url'), '/').'/'.ltrim($path, '/');
    }

    private function destinationKey(int $id): string
    {
        return "shipping:destination:$id";
    }

    private function client(): PendingRequest
    {
        return Http::withHeaders(['key' => $this->apiKey()])
            ->acceptJson()
            ->timeout((int) config('shipping.timeout'));
    }

    /**
     * Jalankan satu request. Timeout / koneksi putus diubah jadi
     * ShippingException supaya pembeli melihat pesan ramah, bukan error 500.
     */
    private function send(string $action, callable $request): Response
    {
        try {
            return $request($this->client());
        } catch (ConnectionException $e) {
            Log::warning("Ongkir: koneksi gagal saat $action", ['error' => $e->getMessage()]);

            throw new ShippingException("Gagal $action: layanan ongkir tidak merespons.");
        }
    }

    /**
     * Ambil `data` dari respons, atau lempar ShippingException yang ramah.
     * Dengan $allowNotFound, status 404 dianggap "tidak ada hasil" (null).
     */
    private function dataOrFail(Response $response, string $action, bool $allowNotFound = false): mixed
    {
        if ($allowNotFound && $response->status() === 404) {
            return null;
        }

        if ($response->status() === 401 || $response->status() === 403) {
            Log::error('Ongkir: API key ditolak (cek RAJAONGKIR_API_KEY di .env)', ['status' => $response->status()]);
            throw new ShippingException("Gagal $action: layanan ongkir menolak kunci akses toko.");
        }

        if (! $response->successful()) {
            Log::warning("Ongkir: gagal $action", [
                'status' => $response->status(),
                'message' => $response->json('meta.message'),
            ]);
            throw new ShippingException("Gagal $action. Coba lagi beberapa saat lagi.");
        }

        return $response->json('data');
    }
}
