<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Riga');
require_once 'classes/config.php';

$id = (int)filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? 0;

// Get competition data
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

// Handle form submissions
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $sponsorId = null;

    try {
        if ($action === 'add') {
            $sponsorId = filter_input(INPUT_POST, 'sponsor_id', FILTER_VALIDATE_INT);
            if (!$sponsorId) throw new Exception('Nepareizs sponsors!');
            
            $stmt = $mysqli->prepare("INSERT INTO sacensibas_sponsori 
                                    (sacensibas_id, sponsora_id) 
                                    VALUES (?, ?)");
            $stmt->bind_param('ii', $id, $sponsorId);
            $stmt->execute();
        } 
        elseif ($action === 'remove') {
            $sponsorId = filter_input(INPUT_POST, 'remove_sponsor_id', FILTER_VALIDATE_INT);
            if (!$sponsorId) throw new Exception('Nepareizs sponsors!');
            
            $stmt = $mysqli->prepare("DELETE FROM sacensibas_sponsori 
                                    WHERE sacensibas_id = ? AND sponsora_id = ?");
            $stmt->bind_param('ii', $id, $sponsorId);
            $stmt->execute();
        }

        // Refresh page after successful operation
        header("Location: details.php?id=$id");
        exit;
        
    } catch (mysqli_sql_exception $e) {
        $error = match ($e->getCode()) {
            1062 => 'Šis sponsors jau ir pievienots!',
            1451 => 'Nevar dzēst sponsoru!',
            default => 'Datubāzes kļūda: ' . $e->getMessage()
        };
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get current sponsors
$sponsori = [];
$stmt = $mysqli->prepare("SELECT s.* 
                         FROM sponsori s
                         JOIN sacensibas_sponsori ss ON s.id = ss.sponsora_id
                         WHERE ss.sacensibas_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$sponsori = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get available sponsors
$availableSponsors = [];
$stmt = $mysqli->prepare("SELECT s.id, s.kompanijas_nosaukums 
                         FROM sponsori s
                         WHERE s.id NOT IN (
                             SELECT sponsora_id 
                             FROM sacensibas_sponsori 
                             WHERE sacensibas_id = ?
                         )");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$availableSponsors = $result->fetch_all(MYSQLI_ASSOC);

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
            
            <h2>Sponsoru pārvaldība</h2>
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
        
            <form method="POST">
                <div class="form-group">
                    <label>Pievienot jaunu sponsoru:</label>
                    <select name="sponsor_id">
                        <option value="">Izvēlēties sponsoru no saraksta</option>
                        <?php foreach ($availableSponsors as $sponsor): ?>
                            <option value="<?= $sponsor['id'] ?>">
                                <?= htmlspecialchars($sponsor['kompanijas_nosaukums']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="action" value="add" class="action-button add">
                        Pievienot
                    </button>
                </div>
        
                <div class="form-group">
                    <label>Noņemt esošu sponsoru:</label>
                    <select name="remove_sponsor_id">
                        <option value="">Izvēlēties pievienoto sponsoru</option>
                        <?php foreach ($sponsori as $s): ?>
                            <option value="<?= $s['id'] ?>">
                                <?= htmlspecialchars($s['kompanijas_nosaukums']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="action" value="remove" 
                            class="action-button remove"
                            onclick="return confirm('Vai tiešām vēlaties noņemt šo sponsoru?')">
                        Noņemt
                    </button>
                </div>
            </form>
            
            <p><a href="index.php">&#x00AB; Atpakaļ uz sarakstu</a></p>
    
    <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>
</body>
</html>