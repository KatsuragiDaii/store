-- Jalankan lewat phpMyAdmin di database production kamu (bukan schema
-- lengkap seperti waktu deploy awal — ini cuma nambah yang perlu buat
-- fitur "Login dengan Google", database yang sekarang sudah ada isinya
-- jadi jangan pakai schema-hostinger-mysql.sql lagi.

ALTER TABLE `users`
  ADD COLUMN `google_id` VARCHAR(255) NULL UNIQUE AFTER `email`,
  MODIFY `password` VARCHAR(255) NULL;
