<?php
declare(strict_types=1);

/**
 * Site yapılandırması.
 * Sunucuya taşırken sadece bu dosyayı düzenlemeniz yeterlidir.
 */
return [
    // Veritabanı dosyası (SQLite). Klasör yazılabilir olmalı.
    'db_path' => __DIR__ . '/data/site.sqlite',

    // Yüklenen görsellerin kaydedileceği klasör ve web yolu
    'upload_dir' => __DIR__ . '/public/uploads',
    'upload_url' => '/uploads',

    // İlk kurulumda oluşturulacak yönetici hesabı
    'admin_username' => 'admin',
    'admin_password' => 'admin123',

    // Sayfa başına yazı sayısı
    'per_page' => 9,

    // Oturum çerezi adı
    'session_name' => 'baranselsite_sess',

    // Zaman dilimi
    'timezone' => 'Europe/Istanbul',
];
