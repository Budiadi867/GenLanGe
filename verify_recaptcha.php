<?php
// verify_recaptcha.php - Verifikasi reCAPTCHA
$secretKey = "6LcfNeYqAAAAAIL7E4QULVu-Kc3_zBpipU3rfODF"; // Ganti dengan Secret Key Anda
$response = $_POST['g-recaptcha-response'] ?? null;
$userIp = $_SERVER['REMOTE_ADDR'];

$visitLogFile = __DIR__ . '/visit_log.json';
$visitLog = file_exists($visitLogFile) ? json_decode(file_get_contents($visitLogFile), true) : [];

if ($response) {
    // Kirim permintaan ke Google untuk memverifikasi reCAPTCHA
    $verifyUrl = "https://www.google.com/recaptcha/api/siteverify";
    $postData = [
        'secret' => $secretKey,
        'response' => $response,
        'remoteip' => $userIp
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $verifyUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($result, true);

    if ($responseData['success']) {
        // Reset penghitungan kunjungan setelah verifikasi
        if (!isset($visitLog[$userIp])) {
            $visitLog[$userIp] = [
                'count' => 0 // Reset penghitungan
            ];
        } else {
            $visitLog[$userIp]['count'] = 0; // Reset penghitungan
        }

        // Simpan log kunjungan
        file_put_contents($visitLogFile, json_encode($visitLog));

        // Kirim respons sukses
        echo json_encode(['success' => true]);
    } else {
        // Kirim respons gagal
        echo json_encode(['success' => false, 'error' => 'reCAPTCHA verification failed']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No reCAPTCHA response']);
}
?>