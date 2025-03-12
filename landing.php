<?php
// landing.php - Router untuk memilih template
$dataFile = 'data.json'; // File untuk menyimpan data konten

// Ambil ID unik dari URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id && file_exists($dataFile)) {
    // Baca data dari file JSON
    $allData = json_decode(file_get_contents($dataFile), true);

    // Cari data berdasarkan ID unik
    if (isset($allData[$id])) {
        $content = $allData[$id];
    } else {
        $content = null; // Data tidak ditemukan
    }
} else {
    $content = null; // ID tidak valid atau file tidak ada
}

// Jika data tidak ditemukan, tampilkan pesan error
if (!$content) {
    http_response_code(404);
    die("<h1>Page Not Found</h1>");
}

// Redirect ke file template yang sesuai
$template = $content['template'];
switch ($template) {
    case 'template1':
        include 'template1.php';
        break;
    case 'template2':
        include 'template2.php';
        break;
    default:
        http_response_code(404);
        die("<h1>Template Not Found</h1>");
}
?>