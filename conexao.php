<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "bancodequestões";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
	echo "Error: Unable to connect to MySQL.". mysqli_connect_error();
	exit;
}
else {
	$db = mysqli_select_db($conn, $database);
	if (!$db) {
		echo "Error: Unable to connet to ".$database;
	}
	else {
		echo "Success";
	}
}