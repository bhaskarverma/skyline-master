<?php

require("../core/database/db_config.php");

$d_id = $_POST['damage'];
$cost = $_POST['cost'];
$vno = $_POST['vno'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("UPDATE `damage` SET `cost_of_repair` = ?, `date_of_repair` = now() WHERE damage_id = ?");
$sql->execute([$cost, $d_id]);

$sql = $pdo->prepare("UPDATE `vehicles` SET `breakdown` = false WHERE `vehicle_no` = ?");
$sql->execute([$vno]);

echo "Successfully Updated";

?>