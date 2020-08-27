<?php

require("../core/database/db_config.php");

$target_type = $_POST['target_type'];
$target_value = $_POST['target_value'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("INSERT INTO `targets` (`target_type`, `target_value`) VALUES (?, ?)");
$sql->execute([$target_type, $target_value]);

echo "Successfully Updated";

?>