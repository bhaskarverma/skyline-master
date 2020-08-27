<?php

require("../database/db_config.php");

$messageText = $_POST['messageText'];
$sender = $_POST['sender'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("INSERT INTO `global_chat`(`message_text`, `origin_user`, `date_of_message`) VALUES (?,?,NOW())");
$sql->execute([$messageText, $sender]);

echo "Successfully Sent";

?>