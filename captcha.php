<?php
// captcha.php - Generate CAPTCHA image

session_start();

// Fungsi untuk menghasilkan teks CAPTCHA acak
function generateCaptchaText($length = 6) {
    $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'; // Huruf dan angka tanpa O, 0, I, 1
    $text = '';
    for ($i = 0; $i < $length; $i++) {
        $text .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $text;
}

// Simpan teks CAPTCHA ke session
$captchaText = generateCaptchaText();
$_SESSION['captcha_text'] = $captchaText;

// Buat gambar CAPTCHA
$width = 200;
$height = 60;
$image = imagecreate($width, $height);

// Warna latar belakang
$bgColor = imagecolorallocate($image, 255, 255, 255); // Putih

// Warna teks
$textColor = imagecolorallocate($image, 0, 0, 0); // Hitam

// Tambahkan noise (garis dan titik)
for ($i = 0; $i < 5; $i++) {
    $lineColor = imagecolorallocate($image, rand(100, 200), rand(100, 200), rand(100, 200));
    imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
}
for ($i = 0; $i < 100; $i++) {
    $dotColor = imagecolorallocate($image, rand(100, 200), rand(100, 200), rand(100, 200));
    imagesetpixel($image, rand(0, $width), rand(0, $height), $dotColor);
}

// Tambahkan teks CAPTCHA ke gambar
$font = 5; // Ukuran font
$x = rand(10, 30); // Posisi horizontal acak
$y = rand(30, 45); // Posisi vertikal acak
imagestring($image, $font, $x, $y, $captchaText, $textColor);

// Output gambar sebagai PNG
header('Content-Type: image/png');
imagepng($image);

// Hapus resource gambar
imagedestroy($image);
?>