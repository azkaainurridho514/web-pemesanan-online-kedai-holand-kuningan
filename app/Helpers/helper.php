<?php

if (!function_exists('price_format')) {
    function price_format($price)
    {
        // pastikan hanya angka, lalu format ke Rupiah
        if (!is_numeric($price)) {
            $price = 0;
        }
        return 'Rp ' . number_format($price, 0, ',', '.');
    }
}
