<?php

require("../core/database/db_config.php");

$vno = $_POST['vehicle_no'];
$details = $_POST['details'];
$type = $_POST['type'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("INSERT INTO `damage`(`vehicle`, `type`, `date_of_breakdown`, `details`) VALUES (?, ?, now(), ?)");
$sql->execute([$vno, $type, $details]);

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("UPDATE vehicles SET breakdown = true WHERE vehicle_no = ?");
$sql->execute([$vno]);

echo "Successfully Updated";

?>