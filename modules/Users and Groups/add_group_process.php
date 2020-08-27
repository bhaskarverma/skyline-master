<?php

include("../core/database/db_config.php");

$gname = $_POST['workgroup'];
$modules = $_POST['modules'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("INSERT INTO `workgroups` (`group_name`) VALUES (?)");
$sql->execute([$gname]);
$gid = $pdo->lastInsertId();

$sql = $pdo->prepare("INSERT INTO `workgroup_modules_xref` (`group_id`, `module_id`) VALUES (?, ?)");

for($i=0; $i<count($modules); $i++)
{
  $sql->execute([$gid,$modules[$i]]);
}

header("Location: /?module=Users%20and%20Groups&page=Add%20Group");

?>