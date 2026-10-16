-- Jalankan lewat phpMyAdmin di database production kamu (tab "SQL"),
-- SATU KALI SAJA. Ini cuma menambah kolom untuk fitur ongkir otomatis;
-- jangan pakai schema-hostinger-mysql.sql lagi karena database sudah ada isinya.
--
-- Kalau muncul error "Duplicate column name", berarti file ini sudah pernah
-- dijalankan; tidak perlu diulang.
--
-- Setara dengan migrasi:
--   migrations/2026_10_16_100000_add_shipping_fields_to_products_and_orders.php

-- Berat per produk dalam gram. Semua produk lama otomatis diisi 300 gram;
-- ubah per produk lewat Admin > Produk > Edit.
ALTER TABLE `products`
  ADD COLUMN `weight` INT UNSIGNED NOT NULL DEFAULT 300;

-- Catatan ongkir yang dihitung saat checkout.
ALTER TABLE `orders`
  ADD COLUMN `shipping_destination_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  ADD COLUMN `total_weight` INT UNSIGNED NULL DEFAULT NULL,
  ADD COLUMN `shipping_etd` VARCHAR(255) NULL DEFAULT NULL;
