<?php

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Riga');
require_once 'classes/config.php';

// Sacensības unikālas, grupētas pa gadiem un kārtoju jau vaicājumā secību priekš attēlošanas. Datums no - 2024. gadā sākums un beigas 2025. būs kā 2024. gada posms.
$vaicajums = "SELECT DISTINCT
            YEAR(datums_no) AS gads
          FROM sacensibas
          ORDER BY YEAR(datums_no) DESC";

$gadi = [];
$result = $mysqli->query($vaicajums);


if ($result) {
    $gadi = $result->fetch_all(MYSQLI_ASSOC);

    //print_r($gadi); echo "<br>"; // [gads] => 2025
    //var_dump($gadi);

    $result->free();
}

?>

<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rallija sacensību kalendārs</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <header>
        <h2>Rallija sacensību kalendārs</h2>
        <nav>
            <ul>
                <li><a href="competition_form.php">Pievienot sacensības</a></li> |
                <li><a href="sponsor_form.php">Pievienot sponsoru</a></li>
            </ul>
        </nav>
    </header>


    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
        <div class="success-message">Sacensības veiksmīgi dzēstas!</div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
            <?= ($_GET['success'] === 'competition') ?
                'Sacensības veiksmīgi pievienotas!' :
                'Sponsors veiksmīgi pievienots!' ?>
        </div>
    <?php endif; ?>

    <?php foreach ($gadi as $gads):
        $year = $gads['gads']; // [gads] => 2025
        $sacensibas = [];
        // Atlasu katra $gadi objecta sacensības un sakārtoju dilstošā secībā.
        $result = $mysqli->query("SELECT * FROM sacensibas 
                                 WHERE YEAR(datums_no) = $year
                                 ORDER BY datums_no DESC");
        if ($result) {
            $sacensibas = $result->fetch_all(MYSQLI_ASSOC);

            //print_r($sacensibas); echo "<br>"; // [id] => 1 [nosaukums] => 2025 Latvijas rallijs [norises_vieta] => Latvija, Cēsis [datums_no] => 2025-06-01 [datums_lidz] => 2025-06-04
            //var_dump($sacensibas);

            $result->free();
        }
    ?>
        <div class="gads">
            <h2><?= $gads['gads'] ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Nosaukums</th>
                        <th>Norises vieta</th>
                        <th>Sākums</th>
                        <th>Beigas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sacensibas as $sac): ?>
                        <tr onclick="window.location='details.php?id=<?= $sac['id'] ?>'">
                            <td><a href="details.php?id=<?= $sac['id'] ?>"><?= htmlspecialchars($sac['nosaukums']) ?></a></td>
                            <td><?= htmlspecialchars($sac['norises_vieta']) ?></td>
                            <td><?= date('d.m.Y', strtotime($sac['datums_no'])) ?></td>
                            <td><?= date('d.m.Y', strtotime($sac['datums_lidz'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</body>

</html>