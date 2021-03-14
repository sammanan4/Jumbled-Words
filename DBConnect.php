<?php

$host = "localhost";
$user = "root";
$password = "";
$dbName = "entries";

$connect = @new mysqli($host, $user, $password, $dbName);

if($connect->connect_error){
	die($connect->connect_error);
}
?>
