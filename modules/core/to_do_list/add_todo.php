<?php

require("../database/db_config.php");

$todo = $_POST['todo'];
$end_dt = $_POST['end_dt'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("INSERT INTO `global_todo`(`text`, `expected_finish_datetime`, `is_completed`, `added_on`) VALUES (?,?,false,NOW())");
$sql->execute([$todo, $end_dt]);

$res = ['res' => 'TODO Successfully Added'];

echo json_encode($res);

?>