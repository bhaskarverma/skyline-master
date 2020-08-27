<?php

require("../database/db_config.php");

$module = $_POST['module'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("INSERT INTO `modules`(`module_name`) VALUES (?)");
$sql->execute([$module]);

echo "Successfully Added";

?>