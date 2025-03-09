<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Riga');
require_once 'classes/config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? 0;

// Iegūstam sacensību datus
$sacensiba = [];
$stmt = $mysqli->prepare("SELECT * FROM sacensibas WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$sacensiba = $result->fetch_assoc();
$stmt->close();

//print_r($sacensiba); echo "<br>"; // [id] => 1 [nosaukums] => 2025 Latvijas rallijs [norises_vieta] => Latvija, Cēsis [datums_no] => 2025-06-01 [datums_lidz] => 2025-06-04
//var_dump($sacensiba);

if(!$sacensiba) die('Sacensības nav atrastas!');


$sponsori = [];
$stmt = $mysqli->prepare("SELECT s.* 
                         FROM sponsori s
                         JOIN sacensibas_sponsori ss ON s.id = ss.sponsora_id
                         WHERE ss.sacensibas_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$sponsori = $result->fetch_all(MYSQLI_ASSOC);

//print_r($sponsori); echo "<br>";
//var_dump($sponsori);

$stmt->close();
?>

<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title><?= htmlspecialchars($sacensiba['nosaukums']) ?></title>
</head>

<body>
    <h1><?= htmlspecialchars($sacensiba['nosaukums']) ?></h1>
    
    <div class="info">
        <p><strong>Norises vieta:</strong> <?= htmlspecialchars($sacensiba['norises_vieta']) ?></p>
        <p><strong>Laika posms:</strong> 
            <?= date('d.m.Y', strtotime($sacensiba['datums_no'])) ?> - 
            <?= date('d.m.Y', strtotime($sacensiba['datums_lidz'])) ?>
        </p>
    </div>

    <h2>Sponsori</h2>
    <div class="sponsors">
        <?php foreach($sponsori as $s): ?>
        <div class="sponsor">
            <a href="<?= htmlspecialchars($s['url']) ?>" target="_blank" rel="noopener noreferrer">
                <?php if($s['logo']): ?>
                <img src="logo/<?= htmlspecialchars($s['logo']) ?>" alt="<?= htmlspecialchars($s['kompanijas_nosaukums']) ?> logo">
                <?php endif; ?>
                <div><?= htmlspecialchars($s['kompanijas_nosaukums']) ?></div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <p><a href="index.php">&#x00AB; Atpakaļ uz sarakstu</a></p>
</body>

</html>