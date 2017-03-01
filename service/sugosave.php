<?php

	if(isset($_GET['id'])){
		if(isset($_GET['szoveg'])){
			require("db.php");
			
			$sql = "UPDATE sugo
				SET szoveg='" . $_GET['szoveg'] . "' 
				WHERE nev='" . $_GET['id'] . "';";
			$result = $conn->query($sql);
		}
	}



?>