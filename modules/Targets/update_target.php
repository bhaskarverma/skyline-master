<?php

require("../core/database/db_config.php");

$target_type = $_POST['target_type'];
$target_value = $_POST['target_value'];

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = $pdo->prepare("UPDATE targets SET target_value = ? WHERE target_type = ?");
$sql->execute([$target_value, $target_type]);

echo "Successfully Updated";

?>