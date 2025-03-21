<?php

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Riga');
require_once 'classes/config.php';

$errors = [];
$success = false;
$formData = [
    'kompanijas_nosaukums' => '',
    'url' => '',
    'piezimes' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'kompanijas_nosaukums' => trim($_POST['kompanijas_nosaukums'] ?? ''),
        'url' => trim($_POST['url'] ?? ''),
        'piezimes' => trim($_POST['piezimes'] ?? '')
    ];

    // Step 1: Validate required fields
    if (empty($formData['kompanijas_nosaukums'])) {
        $errors['kompanijas_nosaukums'] = 'Kompanijas nosaukums ir obligāts';
    }

    if (empty($formData['url'])) {
        $errors['url'] = 'URL ir obligāts';
    }

    // Step 2: Check for existing company name early
    if (empty($errors['kompanijas_nosaukums'])) {
        $checkStmt = $mysqli->prepare("SELECT id FROM sponsori WHERE kompanijas_nosaukums = ?");
        $checkStmt->bind_param('s', $formData['kompanijas_nosaukums']);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $errors['kompanijas_nosaukums'] = 'Šāds sponsors jau eksistē';
        }
        $checkStmt->close();
    }

    // Validate URL format
    if (!empty($formData['url']) && !filter_var($formData['url'], FILTER_VALIDATE_URL)) {
        $errors['url'] = 'Nepareizs URL formāts';
    }

    // Step 3: Process file upload only if other validations pass
    if (empty($errors)) {
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] === UPLOAD_ERR_NO_FILE) {
            $errors['logo'] = 'Logo ir obligāts';
        } else {
            $file = $_FILES['logo'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $maxSize = 2 * 1024 * 1024;

            // File validation
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors['logo'] = 'Faila augšupielādes kļūda';
            } elseif (!in_array($file['type'], $allowedTypes)) {
                $errors['logo'] = 'Atļautie formāti: JPG, JPEG, PNG';
            } elseif ($file['size'] > $maxSize) {
                $errors['logo'] = 'Maksimālais izmērs 2MB';
            } else {
                $allowedExtensions = ['png', 'jpg', 'jpeg'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                if (!in_array($ext, $allowedExtensions)) {
                    $errors['logo'] = 'Atļautie paplašinājumi: ' . implode(', ', $allowedExtensions);
                } else {
                    $filename = basename($file['name']);
                    $destination = __DIR__ . '/logo/' . $filename;

                    // Temporary safe storage
                    $tempFilePath = sys_get_temp_dir() . '/' . uniqid() . '_' . $filename;
                    if (!move_uploaded_file($file['tmp_name'], $tempFilePath)) {
                        $errors['logo'] = 'Neizdevās apstrādāt failu';
                    }
                }
            }
        }
    }

    // Step 4: Final checks before permanent storage and DB commit
    if (empty($errors)) {
        try {
            // Check for existing file name
            if (file_exists($destination)) {
                $errors['logo'] = 'Fails jau eksistē! Lūdzu, pārsauciet savu failu.';
                throw new Exception('File conflict');
            }

            // Move from temp to permanent location
            if (!rename($tempFilePath, $destination)) {
                throw new Exception('Neizdevās saglabāt failu');
            }

            $stmt = $mysqli->prepare("INSERT INTO sponsori 
                (kompanijas_nosaukums, url, logo, piezimes) 
                VALUES (?, ?, ?, ?)");
            $stmt->bind_param(
                'ssss',
                $formData['kompanijas_nosaukums'],
                $formData['url'],
                $filename,
                $formData['piezimes']
            );
            $stmt->execute();

            // Success handling
            $success = true;

            // Clear form data on success
            $formData = [
                'kompanijas_nosaukums' => '',
                'url' => '',
                'piezimes' => ''
            ];
        } catch (mysqli_sql_exception $e) {
            // Cleanup file if database operation fails
            if ($fileMoved && file_exists($destination)) {
                unlink($destination);
            }

            if ($e->getCode() === 1062) {
                $errors['kompanijas_nosaukums'] = 'Šāds sponsors jau eksistē';
            } else {
                $errors['general'] = 'Datubāzes kļūda: ' . $e->getMessage();
            }
        } catch (Exception $e) {
            // Handle file-related errors
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pievienot sponsoru</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <h2>Pievienot jaunu sponsoru</h2>

    <?php if ($success): ?>
        <div class="success-message">Sponsors veiksmīgi pievienots!</div>
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="error"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
        <div class="form-group">
            <label for="kompanijas_nosaukums">Kompanijas nosaukums</label>
            <input type="text" name="kompanijas_nosaukums" id="kompanijas_nosaukums"
                value="<?= htmlspecialchars($formData['kompanijas_nosaukums']) ?>"
                required>
            <?php if (!empty($errors['kompanijas_nosaukums'])): ?>
                <div class="error-message"><?= $errors['kompanijas_nosaukums'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="url">Tīmekļa vietnes URL</label>
            <input type="url" name="url" id="url"
                value="<?= htmlspecialchars($formData['url']) ?>" required>
            <?php if (!empty($errors['url'])): ?>
                <div class="error-message"><?= $errors['url'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="logo">Logo</label>
            <input type="file" name="logo" id="logo" accept="image/*" required>
            <?php if (!empty($errors['logo'])): ?>
                <div class="error-message"><?= $errors['logo'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="piezimes">Piezīmes</label>
            <textarea name="piezimes" id="piezimes"><?= htmlspecialchars($formData['piezimes']) ?></textarea>
        </div>

        <div class="button-group">
            <button type="submit">Saglabāt</button>
            <a href="index.php" class="button">&#x00AB; Atpakaļ</a>
        </div>
    </form>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>