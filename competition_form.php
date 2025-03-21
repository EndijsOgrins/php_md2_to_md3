<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Riga');
require_once 'classes/config.php';

$errors = [];
$formData = [
    'nosaukums' => '',
    'norises_vieta' => '',
    'datums_no' => '',
    'datums_lidz' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['nosaukums'] = trim($_POST['nosaukums'] ?? '');
    $formData['norises_vieta'] = trim($_POST['norises_vieta'] ?? '');
    $formData['datums_no'] = $_POST['datums_no'] ?? '';
    $formData['datums_lidz'] = $_POST['datums_lidz'] ?? '';

    // Validation
    if (empty($formData['nosaukums'])) {
        $errors['nosaukums'] = 'Nosaukums ir obligāts';
    }

    if (empty($formData['norises_vieta'])) {
        $errors['norises_vieta'] = 'Norises vieta ir obligāta';
    }

    if (empty($formData['datums_no'])) {
        $errors['datums_no'] = 'Sākuma datums ir obligāts';
    } elseif (!strtotime($formData['datums_no'])) {
        $errors['datums_no'] = 'Nepareizs datuma formāts';
    }

    if (empty($formData['datums_lidz'])) {
        $errors['datums_lidz'] = 'Beigu datums ir obligāts';
    } elseif (!strtotime($formData['datums_lidz'])) {
        $errors['datums_lidz'] = 'Nepareizs datuma formāts';
    }

    if (empty($errors) && 
        strtotime($formData['datums_no']) > strtotime($formData['datums_lidz'])) {
        $errors['datums_lidz'] = 'Beigu datumam jābūt pēc sākuma datuma';
    }

    if (empty($errors)) {
        try {
            $stmt = $mysqli->prepare("INSERT INTO sacensibas 
                (nosaukums, norises_vieta, datums_no, datums_lidz) 
                VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', 
                $formData['nosaukums'],
                $formData['norises_vieta'],
                $formData['datums_no'],
                $formData['datums_lidz']
            );
            $stmt->execute();
            
            // Show success message on same page
            $success = true;
            // Clear form data
            $formData = [
                'nosaukums' => '',
                'norises_vieta' => '',
                'datums_no' => '',
                'datums_lidz' => ''
            ];
            
        } catch (mysqli_sql_exception $e) {
            $errors['general'] = 'Datubāzes kļūda: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pievienot sacensības</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Pievienot jaunas sacensības</h2>
    
    <?php if (isset($success) && $success): ?>
        <div class="success-message">Sacensības veiksmīgi pievienotas!</div>
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="error"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="form-group">
            <label for="nosaukums" required>Nosaukums</label>
            <input type="text" name="nosaukums" id="nosaukums"
                   value="<?= htmlspecialchars($formData['nosaukums']) ?>"
                   required>
            <?php if (!empty($errors['nosaukums'])): ?>
                <div class="error-message"><?= $errors['nosaukums'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="norises_vieta" required>Norises vieta</label>
            <input type="text" name="norises_vieta" id="norises_vieta"
                   value="<?= htmlspecialchars($formData['norises_vieta']) ?>"
                   required>
            <?php if (!empty($errors['norises_vieta'])): ?>
                <div class="error-message"><?= $errors['norises_vieta'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="datums_no" required>Sākuma datums</label>
            <input type="date" name="datums_no" id="datums_no"
                   value="<?= htmlspecialchars($formData['datums_no']) ?>"
                   required>
            <?php if (!empty($errors['datums_no'])): ?>
                <div class="error-message"><?= $errors['datums_no'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="datums_lidz" required>Beigu datums</label>
            <input type="date" name="datums_lidz" id="datums_lidz"
                   value="<?= htmlspecialchars($formData['datums_lidz']) ?>"
                   required>
            <?php if (!empty($errors['datums_lidz'])): ?>
                <div class="error-message"><?= $errors['datums_lidz'] ?></div>
            <?php endif; ?>
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