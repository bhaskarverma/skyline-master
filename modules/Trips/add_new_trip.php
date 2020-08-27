<?php

require("../core/database/db_config.php");

$round_trip_id = $_POST['round_trip_id'];

if($round_trip_id == "new")
{
    $pdo = new PDO($dsn, $user, $pass, $options);

    //Preparing Data to Insert in Trips Table
    $data_for_trip = [
    	$_POST['trip_from'],
    	$_POST['trip_to'],
    	$_POST['vehicle'],
    	$_POST['driver'],
    	$_POST['material'],
    	$_POST['quantity'],
    	$_POST['rate'],
    	$_POST['fuel_ltr'],
    	$_POST['fuel_money'],
    	$_POST['freight']
    ];

    //Inserting Data into Trips Table
    $sql = $pdo->prepare("INSERT INTO `trips` (`trip_from`, `trip_to`, `trip_start`,`current_status`, `last_updated`, `vehicle`, `driver`, `material`, `quantity`, `rate`, `fuel_ltr`, `fuel_money`, `freight`) VALUES (?, ?, now(), 'Ready', now(), ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql->execute($data_for_trip);
    $trip_id = $pdo->lastInsertId();

    //Creating a new Round Trip Entry
    $sql = $pdo->prepare("INSERT INTO `round_trip`(`km_start`, `km_end`, `on_road`) VALUES (?, 0, true)");
    $sql->execute([$_POST['km_start']]);
    $round_trip_id = $pdo->lastInsertId();

    //Pairing the new Trip with this Round Trip
    $sql = $pdo->prepare("INSERT INTO `trip_round_trip_xref`(`round_trip_id`, `trip_id`) VALUES (?, ?)");
    $sql->execute([$round_trip_id, $trip_id]);

 }
 else
 {
    $pdo = new PDO($dsn, $user, $pass, $options);

    //Preparing Data to Insert in Trips Table
    $data_for_trip = [
    	$_POST['trip_from'],
    	$_POST['trip_to'],
    	$_POST['vehicle'],
    	$_POST['driver'],
    	$_POST['material'],
    	$_POST['quantity'],
    	$_POST['rate'],
    	$_POST['fuel_ltr'],
    	$_POST['fuel_money'],
    	$_POST['freight']
    ];

    //Inserting Data into Trips Table
    $sql = $pdo->prepare("INSERT INTO `trips` (`trip_from`, `trip_to`, `trip_start`,`current_status`, `last_updated`, `vehicle`, `driver`, `material`, `quantity`, `rate`, `fuel_ltr`, `fuel_money`, `freight`) VALUES (?, ?, now(), 'Ready', now(), ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql->execute($data_for_trip);
    $trip_id = $pdo->lastInsertId();

    //Pairing the new Trip with this Round Trip
    $sql = $pdo->prepare("INSERT INTO `trip_round_trip_xref`(`round_trip_id`, `trip_id`) VALUES (?, ?)");
    $sql->execute([$round_trip_id, $trip_id]);
 }

 header("Location: /?module=Trips&page=Trips");
?>