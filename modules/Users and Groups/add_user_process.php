<?php

include("../core/database/db_config.php");

$gid = $_POST['workgroup'];
$user_uname = $_POST['user_uname'];
$user_full_name = $_POST['user_full_name'];
$password = password_hash("Skyline@123", PASSWORD_DEFAULT);

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("INSERT INTO `users`(`name`, `uname`, `password`, `group_id`) VALUES (?,?,?,?)");
$sql->execute([$user_full_name, $user_uname, $password, $gid]);

header("Location: /?module=Users%20and%20Groups&page=Add%20User");

?>