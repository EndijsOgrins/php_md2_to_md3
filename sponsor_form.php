<?php

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Riga');
require_once 'classes/config.php';

$errors = [];
$formData = [
    'kompanijas_nosaukums' => '',
    'url' => '',
    'piezimes' => ''
];
$logo = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['kompanijas_nosaukums'] = trim($_POST['kompanijas_nosaukums'] ?? '');
    $formData['url'] = trim($_POST['url'] ?? '');
    $formData['piezimes'] = trim($_POST['piezimes'] ?? '');

    // Validation
    if (empty($formData['kompanijas_nosaukums'])) {
        $errors['kompanijas_nosaukums'] = 'Kompanijas nosaukums ir obligāts';
    }

    if (!empty($formData['url']) && !filter_var($formData['url'], FILTER_VALIDATE_URL)) {
        $errors['url'] = 'Nepareizs URL formāts';
    }

    // File upload handling
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['logo'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['logo'] = 'Faila augšupielādes kļūda';
        } elseif (!in_array($file['type'], $allowedTypes)) {
            $errors['logo'] = 'Atļautie formāti: JPG, JPEG, PNG';
        } elseif ($file['size'] > $maxSize) {
            $errors['logo'] = 'Maksimālais izmērs 2MB';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $destination = __DIR__ . '/logo/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $logo = $filename;
            } else {
                $errors['logo'] = 'Neizdevās saglabāt failu';
            }
        }
    }

    if (!empty($_POST['talrunis'])) {
        $formData['talrunis'] = trim($_POST['talrunis']);
        if (!preg_match('/^\+?[0-9\s\-]+$/', $formData['talrunis'])) {
            $errors['talrunis'] = 'Nepareizs tālruņa numura formāts';
        }
    }

    $allowedExtensions = ['png', 'jpg', 'jpeg'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions)) {
        $errors['logo'] = 'Atļautie faila paplašinājumi: ' . implode(', ', $allowedExtensions);
    }

    if (empty($errors)) {
        try {
            $stmt = $mysqli->prepare("INSERT INTO sponsori 
                (kompanijas_nosaukums, url, logo, piezimes) 
                VALUES (?, ?, ?, ?)");
            $stmt->bind_param(
                'ssss',
                $formData['kompanijas_nosaukums'],
                $formData['url'],
                $logo,
                $formData['piezimes']
            );
            $stmt->execute();

            header('Location: index.php?success=sponsor');
            exit;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $errors['kompanijas_nosaukums'] = 'Šāds sponsors jau eksistē';
            } else {
                $errors['general'] = 'Datubāzes kļūda: ' . $e->getMessage();
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
    <h1>Pievienot jaunu sponsoru</h1>

    <?php if (!empty($errors['general'])): ?>
        <div class="error"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
        <div class="form-group">
            <label for="kompanijas_nosaukums" required>Kompanijas nosaukums</label>
            <input type="text" name="kompanijas_nosaukums" id="kompanijas_nosaukums"
                value="<?= htmlspecialchars($formData['kompanijas_nosaukums']) ?>"
                required>
            <?php if (!empty($errors['kompanijas_nosaukums'])): ?>
                <div class="error-message"><?= $errors['kompanijas_nosaukums'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="url" required>Tīmekļa vietnes URL</label>
            <input type="url" name="url" id="url"
                value="<?= htmlspecialchars($formData['url']) ?>"required>
            <?php if (!empty($errors['url'])): ?>
                <div class="error-message"><?= $errors['url'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="logo" required>Logo</label>
            <input type="file" name="logo" id="logo" accept="image/*" required>
            <?php if (!empty($errors['logo'])): ?>
                <div class="error-message"><?= $errors['logo'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="piezimes">Piezīmes</label>
            <textarea name="piezimes" id="piezimes"><?=htmlspecialchars($formData['piezimes']) ?></textarea>
        </div>

        <div class="button-group">
            <button type="submit">Saglabāt</button>
            <a href="index.php" class="button">Atpakaļ</a>
        </div>
    </form>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>