<?php

include("../core/database/db_config.php");

$gid = 1;
$user_uname = 'bverma';
$user_full_name = 'Bhaskar Verma';
$password = password_hash("KaT*376@", PASSWORD_DEFAULT);

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("INSERT INTO `users`(`name`, `uname`, `password`, `group_id`) VALUES (?,?,?,?)");
$sql->execute([$user_full_name, $user_uname, $password, $gid]);

header("Location: /?module=Users%20and%20Groups&page=Add%20User");

?>