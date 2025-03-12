<?php
// redirect.php - Redirect URL pendek ke URL panjang
$shortUrlFile = __DIR__ . '/short_urls.json'; // File untuk menyimpan mapping URL pendek

// Ambil kode dari URL pendek
$code = isset($_GET['code']) ? $_GET['code'] : null;

if ($code && file_exists($shortUrlFile)) {
    // Baca data dari file JSON
    $shortUrls = json_decode(file_get_contents($shortUrlFile), true);

    // Cari URL panjang berdasarkan kode (tanpa Base64)
    if (isset($shortUrls[$code])) {
        $longUrl = $shortUrls[$code];
        header("Location: $longUrl", true, 302); // Redirect ke URL panjang
        exit;
    }
}

// Jika kode tidak ditemukan, tampilkan pesan error
http_response_code(404);
die("<h1>Short URL Not Found</h1>");
?>