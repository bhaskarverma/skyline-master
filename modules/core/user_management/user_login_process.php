<?php

session_start();

include("../database/db_config.php");

function setPermissionsForUser($uid, $pdo)
{	
	//Fetching Workgroup for the User
	$sql = $pdo->prepare("SELECT `group_id` FROM `users` WHERE `user_id` = ?");
	$sql->execute([$uid]);
	$gid = $sql->fetch()['group_id'];
	$_SESSION['user_data']['gid'] = $gid;

	if($gid == 1 || $gid == 2)
	{
		//Displaying all Modules for Group "Core Developer" and "Admin"
		$sql = $pdo->prepare("SELECT `module_name` FROM `modules`");
		$sql->execute();
		$perms = $sql->fetchAll();

		//Reformatting modules_in_db array
		$tmp_mid = array();

		for($i=0; $i<count($perms); $i++)
		{
		  array_push($tmp_mid, $perms[$i]['module_name']);
		}

		$perms = $tmp_mid;
		unset($tmp_mid);

		$_SESSION['user_access'] = $perms;
		return;
	}	
	else
	{
		//Fetching The Permissions for Other Groups
		$sql = $pdo->prepare("SELECT `module_name` FROM `modules` WHERE `module_id` IN (SELECT `module_id` FROM `workgroup_modules_xref` WHERE group_id = ?)");
		$sql->execute([$gid]);
		$perms = $sql->fetchAll();

		//Reformatting modules_in_db array
		$tmp_mid = array();

		for($i=0; $i<count($perms); $i++)
		{
		  array_push($tmp_mid, $perms[$i]['module_name']);
		}

		$perms = $tmp_mid;
		unset($tmp_mid);

		$_SESSION['user_access'] = $perms;
		return;
	}
}

$uname = $_POST['uname'];
$pword = $_POST['pword'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("SELECT * FROM users WHERE uname = ?");
$sql->execute([$uname]);
$res = $sql->fetch();

if(empty($res))
{
	header('Location: /login.php?err=No User Found');
	exit;
}

if(password_verify($pword, $res['password']))
{
	$_SESSION['logged_in'] = true;
	$_SESSION['user_data']['name'] = $res['name'];
	$_SESSION['user_data']['uid'] = $res['user_id'];

	setPermissionsForUser($res['user_id'], $pdo);

	header('Location: /');
	exit;
}
else
{
	header('Location: /login.php?err=Wrong Password');
	exit;
}

?>