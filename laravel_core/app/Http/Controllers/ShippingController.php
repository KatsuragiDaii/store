<?php

namespace App\Http\Controllers;

use App\Services\ShippingException;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint JSON untuk halaman checkout. Semua panggilan ke API ongkir lewat
 * sini supaya API key tidak pernah sampai ke browser.
 */
class ShippingController extends Controller
{
    // GET /shipping/destinations?q=kramat jati
    public function destinations(Request $request, ShippingService $shipping): JsonResponse
    {
        if (! $shipping->hasApiKey()) {
            return response()->json(['message' => 'Layanan ongkir belum diaktifkan oleh toko.'], 503);
        }

        $validated = $request->validate([
            'q' => 'required|string|min:3|max:100',
        ], [
            'q.min' => 'Ketik minimal 3 huruf.',
        ]);

        try {
            return response()->json(['data' => $shipping->searchDestinations($validated['q'])]);
        } catch (ShippingException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }

    // POST /shipping/cost  { destination_id }
    // Berat paket dihitung server dari keranjang di session, bukan dari browser.
    public function cost(Request $request, ShippingService $shipping): JsonResponse
    {
        if (! $shipping->isConfigured()) {
            return response()->json(['message' => 'Layanan ongkir belum diaktifkan oleh toko.'], 503);
        }

        $validated = $request->validate([
            'destination_id' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['message' => 'Keranjang kamu kosong.'], 422);
        }

        $destination = $shipping->destinationById((int) $validated['destination_id']);

        if (! $destination) {
            return response()->json(['message' => 'Lokasi tujuan perlu dipilih ulang. Cari kembali kecamatan atau kode pos kamu.'], 422);
        }

        $weight = $shipping->cartWeight($cart);

        try {
            $options = $shipping->calculateCosts($destination['id'], $weight);
        } catch (ShippingException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }

        return response()->json([
            'weight' => $weight,
            'options' => $options,
        ]);
    }
}
