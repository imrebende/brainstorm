<?php

	if(isset($_GET['id'])){
		if(isset($_GET['jelszo'])){
			require("db.php");
			
			$sql = "UPDATE listak
				SET jelszo='" . $_GET['jelszo'] . "' 
				WHERE lista_neve='" . $_GET['id'] . "';";
			$result = $conn->query($sql);
		}
	}



?>