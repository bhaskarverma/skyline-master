<?php

require("../core/database/db_config.php");

$trip_id = $_POST['trip_id'];
$km_end = $_POST['km_end'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("SELECT `round_trip_id` FROM `trip_round_trip_xref` WHERE trip_id = ?");
$sql->execute([$trip_id]);
$rid = $sql->fetch()['round_trip_id'];

$sql = $pdo->prepare("UPDATE `round_trip` SET `km_end` = ?, `on_road` = false WHERE `round_trip_id` = ?");
$sql->execute([$km_end,$rid]);

echo "Successfully Updated";

?>