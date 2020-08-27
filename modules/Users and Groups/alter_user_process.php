<?php

include("../core/database/db_config.php");

$user = $_POST['user'];
$workgroup = $_POST['workgroup'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("UPDATE `users` SET `group_id` = ? WHERE `user_id` = ?");
$sql->execute([$workgroup, $user]);

header("Location: /?module=Users%20and%20Groups&page=Alter%20User");

?>