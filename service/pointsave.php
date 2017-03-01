<?php
	require("db.php");

	$i = 1;
	$values = explode(",", $_GET["list"]);
	foreach ($values as &$value) {
		$value = strip_tags($value);
		if($value !== "" && $value != "undefined"){
			echo $value."<br>";
			$count = "SELECT * FROM rangsorpontok WHERE userid='" . ($_COOKIE['user']) . "' AND lista_neve='" . ($_COOKIE['tema']) . "' AND elem_neve='" . ($value) . "';";
			$result = $conn->query($count);
			
			$sql = "";
			if($result->num_rows > 0){
				$sql = "UPDATE rangsorpontok SET pontszam='" . (string)($i)  .
				"' WHERE lista_neve='" . ($_COOKIE['tema'])  .
				"' AND userid='" . ($_COOKIE['user']) .
				"' AND elem_neve='" . ($value) . "';";
			} else {
				$sql = "INSERT INTO rangsorpontok (lista_neve, elem_neve, userid, pontszam)
						VALUES ('" . ($_COOKIE['tema']) . "','" . $value . "','" . ($_COOKIE['user']) . "','" . (string)($i) . "');";
			}
			if($sql !== "") $result2 = $conn->query($sql);
			$i++;
		}
	}

?>