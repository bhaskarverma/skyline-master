<?php

include("../core/database/db_config.php");

$gid = $_POST['gid'];

$pdo = new PDO($dsn, $user, $pass, $options);

$sql = $pdo->prepare("SELECT * FROM `modules`");
$sql->execute();
$all_modules = $sql->fetchAll();

$sql = $pdo->prepare("SELECT `module_id` FROM `workgroup_modules_xref` WHERE `group_id` = ?");
$sql->execute([$gid]);
$res = $sql->fetchAll();

$permitted_modules = array();

for($i=0;$i<count($res);$i++)
{
	array_push($permitted_modules, $res[$i]['module_id']);
}

$return_html = '<option value=""></option>';

for($i=0;$i<count($all_modules);$i++)
{
	$select_switch = (in_array($all_modules[$i]['module_id'], $permitted_modules))?'Selected':'';
	$return_html .= '<option value="'.$all_modules[$i]['module_id'].'" '.$select_switch.'>'.$all_modules[$i]['module_name'].'</option>'; 
}

echo $return_html;

?>