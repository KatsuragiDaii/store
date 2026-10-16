<?php

namespace App\Services;

use RuntimeException;

/**
 * Dilempar ShippingService kalau layanan ongkir tidak bisa dipakai (key salah,
 * kuota habis, timeout, dsb). Pesannya aman ditampilkan ke pembeli.
 */
class ShippingException extends RuntimeException
{
}
