<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Riga');
require_once 'classes/config.php';

// Iegūstam sacensības grupētas pa gadiem
$vaicajums = "SELECT DISTINCT
            YEAR(datums_no) AS gads
          FROM sacensibas
          ORDER BY gads DESC";

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
    <h1>Rallija sacensību kalendārs</h1>

    <?php foreach ($gadi as $gads):
        $year = $gads['gads']; // [gads] => 2025
        $sacensibas = [];
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