<?php

require('components/global_chat.php');

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("SELECT * FROM `global_chat` ORDER BY `date_of_message` ASC");
$sql->execute();
$messages_data = $sql->fetchAll();

$chatObj = new GlobalChat($_SESSION['user_data']['uid']);

for($i=0;$i<count($messages_data); $i++)
{
	$sql = $pdo->prepare("SELECT `name` FROM `users` WHERE `user_id` = ?");
	$sql->execute([$messages_data[$i]['origin_user']]);
	$name = $sql->fetch()['name'];

	$msg_date_time = date_create($messages_data[$i]['date_of_message']);
	$msg_date_time = date_format($msg_date_time, 'd M Y g:i a');

	$chatObj->addMessage($name, $msg_date_time, $messages_data[$i]['message_text'], ($messages_data[$i]['origin_user'] == $_SESSION['user_data']['uid']));
}

echo $chatObj->getHTML();

?>