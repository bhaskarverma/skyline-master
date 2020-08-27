<?php

require("../core/database/db_config.php");

$pdo = new PDO($dsn, $user, $pass, $options);

$name = $_POST['name'];

$name = strtolower(explode(' ', $name)[0]);

$sql = $pdo->prepare("SELECT COUNT(*) as user_count FROM users WHERE uname LIKE '%".$name."%'");
$sql->execute();
$count = $sql->fetch()['user_count'];

if(!empty($count)) {
    $name = $name . $count;
}

echo $name;

?>