<?php

require("../core/database/db_config.php");

$trip_id = $_POST['trip_id'];
$driver = $_POST['driver'];
$status = $_POST['status'];
$fuel_ltr = $_POST['fuel_ltr'];
$fuel_money = $_POST['fuel_money'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("UPDATE `trips` SET `driver` = ?, `current_status` = ?, `fuel_ltr` = `fuel_ltr` + ?, `fuel_money` = `fuel_money` + ?, `last_updated` = now() WHERE `trip_id` = ?");
$sql->execute([$driver, $status, $fuel_ltr, $fuel_money, $trip_id]);

echo "Successfully Updated";

?>