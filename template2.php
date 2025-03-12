<?php
// landing_template2.php - Template Landing Page 2
$dataFile = __DIR__ . '/data.json'; // File untuk menyimpan data konten
$visitLogFile = __DIR__ . '/visit_log.json'; // File untuk mencatat kunjungan per IP

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

// Catat kunjungan pengguna berdasarkan IP
$userIp = $_SERVER['REMOTE_ADDR'];
$visitLog = file_exists($visitLogFile) ? json_decode(file_get_contents($visitLogFile), true) : [];

// Inisialisasi data untuk IP pengunjung
if (!isset($visitLog[$userIp])) {
    $visitLog[$userIp] = [
        'count' => 1 // Jumlah kunjungan
    ];
} else {
    $visitLog[$userIp]['count']++;
}

// Simpan log kunjungan
file_put_contents($visitLogFile, json_encode($visitLog));

// Tentukan apakah CAPTCHA diperlukan
$showCaptcha = $visitLog[$userIp]['count'] > 5;

// Render halaman sesuai template
$template = $content['template'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($content['og_title']); ?></title>
    <meta property="og:title" content="<?php echo htmlspecialchars($content['og_title']); ?>" />
    <meta property="og:description" content="<?php echo htmlspecialchars($content['og_description']); ?>" />
    <meta property="og:image" content="<?php echo htmlspecialchars($content['og_image']); ?>" />
    <meta property="og:url" content="<?php echo htmlspecialchars("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow-x: hidden;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }

        p {
            font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.5;
            color: #555;
        }

        .captcha-container {
            margin-top: 20px;
        }

        .g-recaptcha {
            margin-bottom: 15px;
        }

        button {
            padding: 12px 24px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        /* Animasi Fade-In */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            h1 {
                font-size: 20px;
            }

            p {
                font-size: 14px;
            }

            button {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($content['og_title']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($content['og_description'])); ?></p>

        <?php if ($showCaptcha): ?>
            <form id="captcha-form" method="POST" action="">
                <div class="g-recaptcha" data-sitekey="6LcfNeYqAAAAADroa_TmhTqlKCCmfbWGIBkjpYzX"></div>
                <button type="submit" id="captcha-button">Proceed</button>
            </form>
        <?php else: ?>
            <p>You will be redirected in <span id="countdown">5</span> seconds...</p>
        <?php endif; ?>
    </div>

    <script>
        // Delay redirect
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        const redirectUrl = "<?php echo htmlspecialchars($content['button_link']); ?>";

        function startRedirect() {
            const interval = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;

                if (countdown <= 0) {
                    clearInterval(interval);
                    window.location.href = redirectUrl;
                }
            }, 1000);
        }

        // Start redirect only if CAPTCHA is not required
        <?php if (!$showCaptcha): ?>
            startRedirect();
        <?php endif; ?>

        // Handle CAPTCHA form submission
        document.getElementById('captcha-form')?.addEventListener('submit', function (e) {
            e.preventDefault();

            // Kirim token reCAPTCHA ke backend
            fetch('verify_recaptcha.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'g-recaptcha-response': grecaptcha.getResponse()
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect jika reCAPTCHA berhasil
                    window.location.href = redirectUrl;
                } else {
                    alert('reCAPTCHA verification failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    </script>
</body>
</html>