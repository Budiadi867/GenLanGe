<?php
// index.php - Generator URL
$dataFile = __DIR__ . '/data.json'; // File untuk menyimpan data konten
$shortUrlFile = __DIR__ . '/short_urls.json'; // File untuk menyimpan mapping URL pendek

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input dari form
    $template = htmlspecialchars($_POST['template']);
    $ogTitle = htmlspecialchars($_POST['og_title']);
    $ogDescription = htmlspecialchars($_POST['og_description']);
    $ogImage = htmlspecialchars($_POST['og_image']);
    $selectedParam = htmlspecialchars($_POST['selected_param']); // Pilihan string

    // Validasi input
    if (!empty($ogTitle) && !empty($selectedParam)) {
        // Buat data konten
        $content = [
            'template' => $template,
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_image' => $ogImage,
        ];

        // Hasilkan ID unik (Base64 URL-safe)
        $uniqueId = rtrim(strtr(base64_encode(random_bytes(11)), '+/', '-_'), '=');

        // Simpan data ke file JSON
        $allData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
        $allData[$uniqueId] = $content;
        file_put_contents($dataFile, json_encode($allData));

        // Buat URL bersih
        $baseUrl = "https://chatdatlng.biz.id/p";
        $generatedUrl = $baseUrl . "/" . $uniqueId;

        // Tambahkan parameter acak secara permanen
        $clickParam = bin2hex(random_bytes(2)); // Random string untuk click
        $idParam = bin2hex(random_bytes(4));    // Random string untuk subsource
        $extclick = bin2hex(random_bytes(6));    // Random string untuk ext_click_id
        $generatedUrl .= "?track=$clickParam&u=$selectedParam&subsource=$idParam&ext_click_id=$extclick";

        // Kirim URL asli ke shorten.php untuk dipendekkan menggunakan cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://chatdatlng.biz.id/p/shorten.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['longUrl' => $generatedUrl]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $shortenResponse = curl_exec($ch);
        curl_close($ch);

        // Debugging
        error_log("Generated URL: " . $generatedUrl);
        error_log("Shorten Response: " . $shortenResponse);

        // Decode hasil dari shorten.php
        $shortenData = json_decode($shortenResponse, true);
        if (isset($shortenData['shortUrl'])) {
            $shortUrl = $shortenData['shortUrl'];
        } else {
            error_log("Invalid response from shorten.php: " . $shortenResponse);
            $shortUrl = null;
        }

        // Simpan hasil URL dalam variabel untuk ditampilkan
        $displayUrl = "<div class='url-container'>";
        $displayUrl .= "<p><strong>Generated Shortened URL:</strong></p>";
        $displayUrl .= "<input type='text' id='generated-url' value='" . htmlspecialchars($shortUrl) . "' readonly>";
        $displayUrl .= "<button id='copy-button'>Copy URL</button>";
        $displayUrl .= "</div>";
    } else {
        $displayUrl = "<p style='color:red;'>Please fill in all required fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GenLanGe URL</title>
    <link rel="icon" href="jscss/icoket.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="url"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .url-container {
            margin-top: 20px;
            position: relative;
        }
        .url-container p {
            margin-bottom: 10px;
            font-weight: bold;
        }
        .url-container input {
            width: calc(100% - 110px); /* Ruang untuk tombol Copy */
            padding-right: 100px;
        }
        @media (max-width: 600px) {
            .url-container input {
                width: 100%;
                margin-bottom: 10px;
            }
            .url-container button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>GenLanGe v1.0</h1>
        <form method="POST" action="">
            <!-- Pilihan Template -->
            <label for="template">Custom Url</label>
            <select id="template" name="template" required>
                <option value="template1">Landing Page</option>
                <option value="template2">No Landing</option>
            </select><br><br>

            <!-- OG Title -->
            <label for="og_title">Title/Judul*</label>
            <input type="text" id="og_title" name="og_title" placeholder="{og:title} *Optional" value="<?php echo $ogTitle; ?>"  required><br>

            <!-- OG Description -->
            <label for="og_description">Description*:</label>
            <textarea id="og_description" name="og_description" placeholder="{og:description} *Optional"><?php echo $ogDescription; ?></textarea><br>

            <!-- OG Image -->
            <label for="og_image">Image*</label>
            <input type="url" id="og_image" name="og_image" placeholder="{og:image} *Optional" value="<?php echo $ogImage; ?>"><br>

             <!-- Pilihan String Parameter -->
            <label for="selected_param">LINk ID</label>
            <select id="selected_param" name="selected_param" value="<?php echo $selectedParam; ?>" required>
                <option value="04">USB</option>
                <option value="03">JPEG</option>
            </select><br>
            
            </select><br>

            <button type="submit">Generate URL</button>
        </form>

        <?php
        // Tampilkan hasil URL jika sudah dihasilkan
        if (isset($displayUrl)) {
            echo $displayUrl;
        }
        ?>
    </div>

    <script>
        // Fungsi untuk menyalin URL ke clipboard
        document.getElementById('copy-button')?.addEventListener('click', function () {
            const urlInput = document.getElementById('generated-url');
            urlInput.select();
            urlInput.setSelectionRange(0, 99999); // Untuk perangkat mobile
            navigator.clipboard.writeText(urlInput.value).then(() => {
                console.log('URL copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy URL: ', err);
            });
        });
    </script>
</body>
</html>