<?php

include("../core/database/db_config.php");

$gid = $_POST['workgroup'];
$modules = $_POST['modules'];

$pdo = new PDO($dsn, $user, $pass, $options);

$sql = $pdo->prepare("DELETE FROM `workgroup_modules_xref` WHERE `group_id` = ?");
$sql->execute([$gid]);

$sql = $pdo->prepare("INSERT INTO `workgroup_modules_xref` (`group_id`, `module_id`) VALUES (?, ?)");

for($i=0; $i<count($modules); $i++)
{
  $sql->execute([$gid,$modules[$i]]);
}

header("Location: /?module=Users%20and%20Groups&page=Alter%20Group");

?>