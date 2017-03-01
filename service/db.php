<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "brainstormingDB";
	/*$servername = "localhost";
	$username = "fmeavoc";
	$password = "z@htupd-Yx25oldU";
	$dbname = "fmeavoc";*/
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
?>