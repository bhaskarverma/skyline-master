<?php

include("../core/database/db_config.php");

$uid = $_POST['uid'];

$pdo = new PDO($dsn, $user, $pass, $options);

$sql = $pdo->prepare("SELECT `group_id` FROM `users` WHERE `user_id` = ?");
$sql->execute([$uid]);
$group_id = $sql->fetch()['group_id'];

$sql = $pdo->prepare("SELECT * FROM `workgroups`");
$sql->execute();
$groups = $sql->fetchAll();

$return_html = '<option value=""></option>';

for($i=0;$i<count($groups);$i++)
{
	$select_switch = ($groups[$i]['group_id'] == $group_id)?'Selected':'';
	$return_html .= '<option value="'.$groups[$i]['group_id'].'" '.$select_switch.'>'.$groups[$i]['group_name'].'</option>'; 
}

echo $return_html;

?>