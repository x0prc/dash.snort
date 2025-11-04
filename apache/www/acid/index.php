<?php
require_once 'acid_conf.php';
require_once 'db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dash for Snort</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Home</h1>
    <a href="report.php">View Reports</a>
    <table border="1">
        <tr><th>Time</th><th>Signature</th><th>Source</th><th>Destination</th></tr>
        <?php
        $alerts = getAlerts();
        foreach($alerts as $row) {
            echo "<tr>";
            echo "<td>{$row['timestamp']}</td>";
            echo "<td>{$row['signature']}</td>";
            echo "<td>{$row['src_ip']}</td>";
            echo "<td>{$row['dst_ip']}</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
