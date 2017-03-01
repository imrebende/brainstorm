<?php
	require("db.php");

	$i = 0;
	$labels = explode(",", $_GET["labels"]);	
	$values = explode(",", $_GET["values"]);
	foreach ($labels as &$label) {
		$label = strip_tags($label);
		if($label !== ""){
			$count = "SELECT * FROM sulypontok WHERE userid='" . $_COOKIE['user'] . "' AND lista_neve='" . $_COOKIE['tema'] . "' AND elem_neve='" . $label . "';";
			$result = $conn->query($count);
			
			$sql = "";
			if($result->num_rows > 0){
				$sql = "UPDATE sulypontok SET sulypont=" . $values[$i] .
				" WHERE userid='" . $_COOKIE['user'] .
				"' AND lista_neve='" . $_COOKIE['tema'] .
				"' AND elem_neve='" . $label . "';";
			} else {
				$sql = "INSERT INTO sulypontok (lista_neve, elem_neve, userid, sulypont)
						VALUES ('" . $_COOKIE['tema'] . "','" . $label . "','" . $_COOKIE['user'] . "','" . $values[$i] . "');";
			}
			
			$result2 = $conn->query($sql);
		}
		$i++;
	}

?>