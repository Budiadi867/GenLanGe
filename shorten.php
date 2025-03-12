<?php
// shorten.php - Proses pemendekan URL
$shortUrlFile = __DIR__ . '/short_urls.json'; // File untuk menyimpan mapping URL pendek

// Ambil data dari permintaan POST
$data = json_decode(file_get_contents('php://input'), true);
$longUrl = $data['longUrl'] ?? null;

// Debugging
error_log("Received long URL: " . ($longUrl ?? 'No URL'));

if (!empty($longUrl)) {
    // Hasilkan string acak untuk URL pendek
    $shortCode = substr(md5(uniqid(rand(), true)), 0, 13); // 6 karakter acak
    $randomBase64 = rtrim(strtr(base64_encode(random_bytes(99)), '+/', '-_'), '='); // Random Base64

    // Gabungkan short code dan random Base64
    $shortUrl = "https://chatdatlng.biz.id/p/s/" . $shortCode . "&fbclid=" . $randomBase64 . "";

    // Simpan mapping antara short code (tanpa Base64) dan URL panjang
    $shortUrls = file_exists($shortUrlFile) ? json_decode(file_get_contents($shortUrlFile), true) : [];
    $shortUrls[$shortCode] = $longUrl;
    file_put_contents($shortUrlFile, json_encode($shortUrls));

    // Kirim respons JSON
    echo json_encode(['shortUrl' => $shortUrl]);
} else {
    echo json_encode(['error' => 'Invalid URL']);
}
?>