<?php

require("../database/db_config.php");

$td_id = $_POST['td_id'];

$td_id = explode('-',$td_id)[2];

$todo = $_POST['todo'];
$end_dt = $_POST['end_dt'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("UPDATE `global_todo` SET `expected_finish_datetime` = ? WHERE `item_id` = ?");
$sql->execute([$end_dt, $td_id]);

$res = ['res' => 'TODO Successfully Updated'];

echo json_encode($res);

?>