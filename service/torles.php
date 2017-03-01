<?php
	require("db.php");
	
	if(isset($_GET['tema'])){
		$sqlTema = "DELETE FROM listak WHERE lista_neve='" . $_GET['tema'] . "';";

		$result = $conn->query($sqlTema);
		
		$sqlElemek = "DELETE FROM elemek WHERE lista_neve='" . $_GET['tema'] . "';";
		$result = $conn->query($sqlElemek);
		
		$sqlSulyozas = "DELETE FROM sulypontok WHERE lista_neve='" . $_GET['tema'] . "';";
		$result = $conn->query($sqlSulyozas);
		
		$sqlRangsor = "DELETE FROM rangsorpontok WHERE lista_neve='" . $_GET['tema'] . "';";
		$result = $conn->query($sqlRangsor);
	} else {	
		$sqlTema = "DELETE FROM listak WHERE lista_neve='" . $_COOKIE['tema'] . "';";

		$result = $conn->query($sqlTema);
		
		$sqlElemek = "DELETE FROM elemek WHERE lista_neve='" . $_COOKIE['tema'] . "';";
		$result = $conn->query($sqlElemek);
		
		$sqlSulyozas = "DELETE FROM sulypontok WHERE lista_neve='" . $_COOKIE['tema'] . "';";
		$result = $conn->query($sqlSulyozas);
		
		$sqlRangsor = "DELETE FROM rangsorpontok WHERE lista_neve='" . $_COOKIE['tema'] . "';";
		$result = $conn->query($sqlRangsor);
	}
?>