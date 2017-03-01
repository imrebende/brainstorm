<?php
if(isset($_GET['tema'])){
	$_POST['tema'] = $_GET['tema'];
	if(!isset($_GET['jelszo'])) $_POST['jelszo'] = "";
	else $_POST['jelszo'] = $_GET['jelszo'];
}
//Beállítom Cookie-ba a témát
if(isset($_POST['tema'])){
	
	require("service/db.php");
	
	$sql = "SELECT * FROM listak WHERE lista_neve='" . $_POST['tema'] . "' AND jelszo='" . $_POST['jelszo'] . "';";
	$result = $conn->query($sql);
	
	$_COOKIE['temajelszo'] = "";
	setcookie("temajelszo", "", time()-3600*72, "/");
	if($result->num_rows == 1){
		setcookie("tema", $_POST['tema'], time() + 3600*72, "/");
		$_COOKIE['tema'] = $_POST['tema'];
		setcookie("temajelszo", $_POST['jelszo'], time() + 3600*72, "/");
		$_COOKIE['temajelszo'] = $_POST['jelszo'];
	} else {
		setcookie("tema", "", time()-3600*72, "/");
		unset($_COOKIE['tema']);
	}
}

if(isset($_POST['ujElem'])){
	//Új elem létrehozásához új oldal megnyitása
	if(!isset($_POST['szulo']) || $_POST['szulo'] == "") header('Location: '. "ujelem.php");
	else header('Location: '. "ujelem.php?szulo=" . $_POST['szulo']);
} else if(isset($_POST['inputClose']) || isset($_POST['sortClose']) || isset($_POST['skalaClose'])){
	//Téma állapotának változtatása
	require("service/db.php");
	
	$sql = "";
	if(isset($_POST['skalaClose'])){
		$sql = "UPDATE listak SET allapot='kesz' WHERE lista_neve='" . $_COOKIE['tema']  . "';";
	} else if(isset($_POST['sortClose'])){
		$sql = "UPDATE listak SET allapot='skalazas' WHERE lista_neve='" . $_COOKIE['tema']  . "';";
	} else if(isset($_POST['inputClose'])){
		$sql = "UPDATE listak SET allapot='rendezes' WHERE lista_neve='" . $_COOKIE['tema']  . "';";
	}
	
	$result = $conn->query($sql);
} else if(isset($_POST['insert'])){
	//Új elem beszúrása
	require("service/db.php");
		
	if(!isset($_POST['szulo'])) $_POST['szulo'] = "";
	
	$sql = "INSERT INTO elemek (lista_neve, elem_neve, szulo_neve)
			VALUES ('" . $_COOKIE['tema'] . "','" . $_POST['szempont'] . "','" . $_POST['szulo'] . "');";
			
	$result = $conn->query($sql);
	
	//Ha van gyereke, akkor töröljük a paramétereit
			$sqlChange = "UPDATE elemek
				SET kizaro_ertek='', idealis_ertek='', fuggveny_ertek='', dimenzio=''
				WHERE lista_neve='" . $_COOKIE['tema'] . "' AND elem_neve='" . $_POST['szulo'] . "';";
			$resultChange = $conn->query($sqlChange);
} else if(isset($_POST['update'])){
	//Új elem beszúrása
	require("service/db.php");
	
	if(isset($_POST['dimenzio'])){
		if($_POST['dimenzio'] == "pont" || $_POST['dimenzio'] == "Pont"){
			$_POST['fvErtek'] = '1';
			$_POST['idErtek'] = '9';
			$_POST['kizErtek'] = '0';
		} else if($_POST['dimenzio'] == "I/N"){
			$_POST['fvErtek'] = '1';
			$_POST['idErtek'] = '1';
			$_POST['kizErtek'] = '0';
		}
	}
	
	if(!isset($_POST['kizErtek'])){
		$_POST['kizErtek'] = '';
	}
	if(!isset($_POST['idErtek'])){
		$_POST['idErtek'] = '';
	}
	if(!isset($_POST['fvErtek'])){
		$_POST['fvErtek'] = '';
	}
	if(!isset($_POST['dimenzio'])){
		$_POST['dimenzio'] = '';
	}
	
	if(isset($_POST['regiszempont'])){
		$sql = "UPDATE elemek
		SET elem_neve='" . $_POST['szempont'] . "', kizaro_ertek='" . $_POST['kizErtek'] . "', idealis_ertek='" . $_POST['idErtek'] . "', fuggveny_ertek='" . $_POST['fvErtek'] . "', dimenzio='" . $_POST['dimenzio'] . "'
		WHERE lista_neve='" . $_COOKIE['tema'] . "' AND elem_neve='" . $_POST['regiszempont'] . "';";
		
		//Gyerek elemek átírása
		if($_POST['regiszempont'] != $_POST['szempont']){
			$sqlParent = "UPDATE elemek
			SET szulo_neve='" . $_POST['szempont'] . "'
			WHERE lista_neve='" . $_COOKIE['tema'] . "' AND szulo_neve='" . $_POST['regiszempont'] . "';";
			$resultParent = $conn->query($sqlParent);
			
			//Rendezés, súlyozás átírása
			$sqlParent = "UPDATE rangsorpontok
			SET elem_neve='" . $_POST['szempont'] . "
			WHERE lista_neve='" . $_COOKIE['tema'] . "' AND elem_neve='" . $_POST['regiszempont'] . "';";
			$resultParent = $conn->query($sqlParent);
			
			$sqlParent = "UPDATE sulypontok
			SET elem_neve='" . $_POST['szempont'] . "
			WHERE lista_neve='" . $_COOKIE['tema'] . "' AND elem_neve='" . $_POST['regiszempont'] . "';";
			$resultParent = $conn->query($sqlParent);
		}
		
		/*if(isset($_POST['suly'])){
			$sqlUpdateSuly = "UPDATE sulypontok
			SET sulypont='" . $_POST['suly'] . "'
			WHERE lista_neve='" . $_COOKIE['tema'] . "' AND elem_neve='" . $_POST['szempont'] . "';";
			$resultUpdateSuly = $conn->query($sqlUpdateSuly);
		}*/
	} else {
		$sql = "UPDATE elemek
			SET kizaro_ertek='" . $_POST['kizErtek'] . "', idealis_ertek='" . $_POST['idErtek'] . "', fuggveny_ertek='" . $_POST['fvErtek'] . "', dimenzio='" . $_POST['dimenzio'] . "'
			WHERE lista_neve='" . $_COOKIE['tema'] . "' AND elem_neve='" . $_POST['szempont'] . "';";
	}
	$result = $conn->query($sql);
	
	//Ha van gyereke, akkor töröljük a paramétereit
	$isParentSql = "SELECT * FROM elemek WHERE lista_neve='" . $_COOKIE['tema'] . "' AND szulo_neve='" . $_GET['elem'] . "';";
	$resultisParentSql = $conn->query($isParentSql);
	if($resultisParentSql->num_rows > 0){
		$sqlChange = "UPDATE elemek
			SET kizaro_ertek='', idealis_ertek='', fuggveny_ertek='', dimenzio=''
			WHERE lista_neve='" . $_COOKIE['tema'] . "' AND elem_neve='" . $_POST['szulo'] . "';";
		$resultChange = $conn->query($sqlChange);
	}
} else if(isset($_GET['torol'])){
	//Elem törlése
	require("service/db.php");
		
	if(!isset($_POST['szulo'])) $_POST['szulo'] = "";
	
	$sql = "DELETE FROM elemek
			WHERE lista_neve='" . $_COOKIE['tema'] . "' AND elem_neve='" . $_GET['torol'] . "';";
	$result = $conn->query($sql);
	$GLOBALS['sql'] = $sql;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Lista nézet</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/jquery-ui.min.css" rel="stylesheet" />
	<link href="css/jquery-ui.structure.min.css" rel="stylesheet" />
	<link href="css/jquery-ui.theme.min.css" rel="stylesheet" />
	<link href="css/style.css" rel="stylesheet" />
	<style>
		#sortable ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
		#sortable li { list-style-type: none; margin: 5px; padding: 5px; }
		#prevButton, #nextButton {font-size: 20px;}
	</style>
	<script src="js/jquery.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
	
	<?php
		//Megnézzük, hogy a rendezés státuszban van e lista, és ha igen, akkor rendezhető formában teszük azt ki
		$GLOBALS["allapot"] = "";
		if(isset($_COOKIE['tema'])){
			require("service/db.php"); 
					
			$sql = "SELECT * FROM listak WHERE lista_neve='" . $_COOKIE['tema'] . "' AND jelszo='" . $_COOKIE['temajelszo'] . "';";
			$result = $conn->query($sql);
			
			if($result->num_rows == 0){
				$GLOBALS["allapot"] = "nemletezik";
			}
			
			//Rendezhetőség JS
			while($row = mysqli_fetch_array($result)){
				$GLOBALS["allapot"] = $row['allapot'];
				if($row['allapot'] == "rendezes"){
					echo '<script>
					  $( function() {
						$( "#sortable" ).sortable({
						  revert: true
						});
						$( "ul, li" ).disableSelection();
						$(".editbutton").addClass("hidden");
						$(".removebutton").addClass("hidden");
					  } );
					  </script>';
				}
			}
			
			if($_COOKIE['usertype'] != "admin" && $GLOBALS["allapot"] == "uj"){
				echo '<script>
					  $( function() {
						$(".removebutton").addClass("hidden");
					  } );
					  </script>';
			}
		}
	?>
	
</head>
<body>
	<?php require_once('menu.php'); ?>
	<div class="container margin-top">
		<div class="main">
			<?php if($GLOBALS["allapot"] == "rendezes"): ?>
				<h1>Szempontok rendezése</h1>
			<?php elseif($GLOBALS["allapot"] == "skalazas"): ?>
				<h1>Súlyozás</h1>
			<?php elseif($GLOBALS["allapot"] == "uj"): ?>
				<h1>Szempontok listája</h1>
			<?php elseif($GLOBALS["allapot"] == "kesz"): ?>
				<!--<h1></h1>-->
			<?php endif; ?>
			<h3><?php
				if($GLOBALS["allapot"] == ""){
					echo "Nincs jogosultsága ehhez a témához!";
				}
				$cim = "";
				if(isset($_COOKIE['tema'])) $cim = $_COOKIE['tema'];
				if(isset($_GET['elem'])){
					require("service/db.php");
					
					$cim = $_GET['elem'];
					$szulo = $_GET['elem'];
					while($szulo != ""){
						$sql = "SELECT * FROM elemek WHERE elem_neve='" . $szulo . "' AND lista_neve='" . $_COOKIE['tema'] . "';";
						$result = $conn->query($sql);
						
						while($row = mysqli_fetch_array($result)){						
							$szulo = $row['szulo_neve'];
							$cim = $szulo . " > " . $cim;
						}
					}
					$cim = $_COOKIE['tema'] . "<span style='font-weight: 300;'>" . $cim . "</span>";
				}
				echo $cim;
				
				if($GLOBALS["allapot"] == "kesz"){
					if(!isset($_GET['szakerto'])) $_GET['szakerto'] = 0;
					echo '<br/>';
					echo '<a id="prevButton" class="btn" href="lista.php?szakerto='; echo $_GET['szakerto'] - 1 . '"> &lt; </a>';
					echo '<span style="font-size: 18px;" id="userName"></span>';
					echo '<a id="nextButton" class="btn" href="lista.php?szakerto='; echo $_GET['szakerto'] + 1 . '"> &gt; </a> <span style="font-size: 16px;">súlyszámai</span>';
				}
			?></h3>
			<ul id="sortable">
				<?php 
					if(isset($_COOKIE['tema'])){
						
						class Elem {
							public $pontszam;
							public $usercount;
							public $sulyszam;
							public $nev;
							public $dimenzio;
							public $szulo;
							public $fuggveny;
							public $voltMar;
							public $kizaro;
							public $idealis;
						}
						
						$elemek = [];
						
						require("service/db.php");
						
						if(!isset($_GET['elem'])) $_GET['elem'] = "";
						
						if($GLOBALS["allapot"] == "rendezes")
							$sql = "SELECT elemek.elem_neve as elem_neve, rangsorpontok.pontszam as pontszam, elemek.szulo_neve FROM elemek LEFT JOIN rangsorpontok ON elemek.elem_neve = rangsorpontok.elem_neve AND elemek.lista_neve = rangsorpontok.lista_neve WHERE elemek.lista_neve='" . $_COOKIE['tema'] . "' AND elemek.szulo_neve='" . $_GET['elem'] . "' AND rangsorpontok.userid='" . $_COOKIE['user'] . "' ORDER BY pontszam ASC;";
						else if($GLOBALS["allapot"] == "skalazas")
							$sql = "SELECT * FROM elemek LEFT JOIN sulypontok ON elemek.elem_neve = sulypontok.elem_neve AND elemek.lista_neve = sulypontok.lista_neve WHERE elemek.lista_neve='" . $_COOKIE['tema'] . "' AND elemek.szulo_neve='" . $_GET['elem'] . "' AND sulypontok.userid='" . $_COOKIE['user'] . "' ORDER BY sulypontok.sulypont DESC;";
						else if($GLOBALS["allapot"] == "uj")
							$sql = "SELECT * FROM elemek WHERE elemek.lista_neve='" . $_COOKIE['tema'] . "' AND elemek.szulo_neve='" . $_GET['elem'] . "';";
						else if($GLOBALS["allapot"] == "kesz")
							$sql = "SELECT elemek.elem_neve, dimenzio, kizaro_ertek, idealis_ertek, AVG(sulypont) as sulypontatlag, szulo_neve, fuggveny_ertek FROM elemek LEFT JOIN sulypontok ON elemek.elem_neve = sulypontok.elem_neve AND elemek.lista_neve=sulypontok.lista_neve WHERE elemek.lista_neve='" . $_COOKIE['tema'] . "' GROUP BY elemek.elem_neve ORDER BY sulypontatlag DESC;";
						$result = $conn->query($sql);
						
						if($GLOBALS["allapot"] == "rendezes" && $result->num_rows == 0){
							$sql = "SELECT * FROM elemek WHERE elemek.lista_neve='" . $_COOKIE['tema'] . "' AND elemek.szulo_neve='" . $_GET['elem'] . "';";
							$result = $conn->query($sql);
							$ertek = 1;
							while($row = mysqli_fetch_array($result)){
								$insertSql = "INSERT INTO rangsorpontok (lista_neve, elem_neve, userid, pontszam)
											VALUES ('" . ($_COOKIE['tema']) . "','" . $row['elem_neve'] . "','" . ($_COOKIE['user']) . "','" . $ertek . "');";
								$insertResult = $conn->query($insertSql);
								$ertek++;
							}
							echo "Ez a lista még nem volt rendezve a felhasználó által!";
							
							$sql = "SELECT elemek.elem_neve as elem_neve, rangsorpontok.pontszam as pontszam, elemek.szulo_neve FROM elemek LEFT JOIN rangsorpontok ON elemek.elem_neve = rangsorpontok.elem_neve AND elemek.lista_neve = rangsorpontok.lista_neve WHERE elemek.lista_neve='" . $_COOKIE['tema'] . "' AND elemek.szulo_neve='" . $_GET['elem'] . "' AND rangsorpontok.userid='" . $_COOKIE['user'] . "' ORDER BY pontszam ASC;";
							$result = $conn->query($sql);
						}
						
						$data = "[";
						$label = "[";
						$i = 0;
						$GLOBALS["elemek"] = [];
						while($row = mysqli_fetch_array($result)){
							if(!isset($_GET['elem'])) $_GET['elem'] = "";
							if ($GLOBALS["allapot"] == "uj" || $GLOBALS["allapot"] == "rendezes"){
								echo '<li class="ui-state-default">' . $row['elem_neve'] .
							'<a class="removebutton btn btn-default margin-left" href="lista.php?torol=' . urlencode($row['elem_neve']) . '"><span class="glyphicon glyphicon-remove"></span></a>
							<!--<a class="treebutton btn btn-default margin-left" href="lista.php?elem=' . $row['elem_neve'] . '"><span class="glyphicon glyphicon-menu-hamburger"></span></a>-->
							<a class="editbutton btn btn-default margin-long-left" href="ujelem.php?szulo=' . urlencode($_GET['elem']) . '&elem=' . urlencode($row['elem_neve']) . '"><span class="glyphicon glyphicon-pencil"></span></a>
							</li>';
							} else if($GLOBALS["allapot"] == "skalazas" && $row['sulypont'] != "" && $row['elem_neve'] != ""){
								//$data .= "{value:" . $row['sulypont'] . ", label:'" . $row['elem_neve'] . "'},";
								$label .= "'" . $row['elem_neve'] . "',";
								$data .= round($row['sulypont'], 1) . ",";
								$GLOBALS["elemek"][$i] = $row['elem_neve'];
								$GLOBALS["sulyok"][$i] = $row['sulypont'];
								$i++;
							} else if($GLOBALS["allapot"] == "kesz"){
								$elemek[$i] = new Elem();
								$elemek[$i]->nev = $row['elem_neve'];
								$elemek[$i]->dimenzio = $row['dimenzio'];
								$elemek[$i]->kizaro = $row['kizaro_ertek'];
								$elemek[$i]->idealis = $row['idealis_ertek'];
								$elemek[$i]->sulyszam = $row['sulypontatlag'];
								$elemek[$i]->szulo = $row['szulo_neve'];
								$elemek[$i]->fuggveny = $row['fuggveny_ertek'];
								$elemek[$i]->voltMar = false;
								$i++;
							}
						}
						//Ha még nem mentett le, akkor készítünk egy diagramot
						if($result->num_rows == 0 && $GLOBALS["allapot"] == "skalazas"){
							$sqlSelect = "SELECT elemek.elem_neve as elem_neve, SUM(rangsorpontok.pontszam) as pontszam, elemek.szulo_neve FROM elemek LEFT JOIN rangsorpontok ON elemek.elem_neve = rangsorpontok.elem_neve AND elemek.lista_neve = rangsorpontok.lista_neve WHERE elemek.lista_neve='" . $_COOKIE['tema'] . "' AND elemek.szulo_neve='" . $_GET['elem'] . "' GROUP BY elemek.elem_neve ORDER BY pontszam ASC;";
							$resultSelect = $conn->query($sqlSelect);
							$i = 0;
							while($row = mysqli_fetch_array($resultSelect)){
								$label .= "'" . $row['elem_neve'] . "',";
								$sulypont = (100 * (0.7 - 1)) / ((pow(0.7, $resultSelect->num_rows)) - 1) * (pow(0.7, $i));
								$sulypont = round($sulypont, 1);
								$data .= round($sulypont, 1) . ",";
								$GLOBALS["elemek"][$i] = $row['elem_neve'];
								$GLOBALS["sulyok"][$i] = round($sulypont, 1);
								$i++;
							}
						}
						
						$data .= "]";
						$label .= "]";
						$GLOBALS["sulypontokSzama"] = $i;
						
						if($GLOBALS["allapot"] == "skalazas") echo '<script>
							var data = {
								labels:' . $label . ',
								datasets: [
									{
										backgroundColor: "blue",
										data:' . $data . '
									}
								]
							};
							window.onload = function() {
							var ctx = document.getElementById("chartContainer").getContext("2d");
							ctx.canvas.width = 1140;
							ctx.canvas.height = 300;
							window.myBar = new Chart(ctx, {
								type: "bar",
								data: data,
								options: {
									/*onClick: function(e){
										window.location.href = "lista.php?elem=" + this.getElementAtEvent(e)[0]._model.label;
									},*/
									maintainAspectRatio: true,
									responsive: true,
									legend: {
										display: false
									},
									scales: {
										yAxes: [ { id: "y-axis-1", type: "linear", position: "left", ticks: { min: 0, max: 90 ,
           callback: function(value) {
               return value + "%"
           }} }, ],
		   xAxes: [{
                display: false,barPercentage: 0.95,categoryPercentage: 0.95
            }]
									}
								}
							});

						};
						</script>';
					}
				
				echo "</ul>";
				
				if($GLOBALS["allapot"] == "skalazas"){
					echo "<table style='margin-left:40px;'><tr>";
					for($j = 0; $j < $GLOBALS["sulypontokSzama"]; $j++){
						echo "<td style='width:" . 1100 / $i . "px;padding-left:" . 1100 / 100 ."px;'><button class='btn btn-default upButton' id='" . str_replace(" ", "", $GLOBALS["elemek"][$j]) . "Up' style='width: 80%;'><span class='glyphicon glyphicon-chevron-up'></span></button></td>";
					}
					echo "</tr></table>";
				}
				
				if($GLOBALS["allapot"] == "kesz" && $GLOBALS['usertype'] == "admin"){
					//Sorok összerakása
					$sorok = "";
					$j = 0;
					$kiirtakSzama = 0;
					$adottSzulo = "";
					$adottSzuloSzintje = 0;
					
					require("service/db.php");
					
					$sqlUsers = "SELECT DISTINCT(userid) as user FROM rangsorpontok WHERE lista_neve='" . $_COOKIE['tema'] . "' ORDER BY userid DESC;";
					$resultUsers = $conn->query($sqlUsers);		
					
					$kivalasztottSzakertoNeve = "";
					
					$usersString = "";
					$usersArray = [];
					$usersNumber = 0;
					while($row = mysqli_fetch_array($resultUsers)){
						if($usersNumber == $_GET['szakerto'] - 1) $kivalasztottSzakertoNeve = $row['user']; 
						if(/*$_COOKIE['usertype'] == "admin" || $_COOKIE['user'] == $row['user'] || */$row['user'] == $kivalasztottSzakertoNeve){
							$usersString .= "<th class='text-right'>" . $row['user'] . "</th>";
						}
						$usersArray[$usersNumber] = $row['user'];
						$usersNumber++;
					}
					
					if($kivalasztottSzakertoNeve == ""){
						$kivalasztottSzakertoNeve = "Csoport";
						$kivalasztottSzakertoPontszamai = "";
					} else {
						$kivalasztottSzakertoPontszamai = "[";
					}
					
					echo '<script>$( document ).ready(function() {
							$("#userName").text("' . $kivalasztottSzakertoNeve . '");
						});	</script>';
	
					if($_GET['szakerto'] <= 0){
						echo '<script>$( document ).ready(function() {
								$("#prevButton").addClass("hidden");
							});	</script>';
					}
					
					if($_GET['szakerto'] >= $usersNumber + 1){
						echo '<script>$( document ).ready(function() {
								$("#nextButton").addClass("hidden");
							});	</script>';
					}
					
					$szuloSulyok = [];
					$szakertoSzuloSulyok = [];
					$diagrammLabel = "[";
					$diagrammValue = "[";
					
					while($kiirtakSzama < $i){
						if($elemek[$j]->voltMar == false && $elemek[$j]->szulo == $adottSzulo){
							$pontok = ""; $min = 0; $max = 0;
							$pontSzamok = [];
							$ossz = 0;
							
							$sulyszam = 0;
							if($adottSzuloSzintje != 0){
								$sulyszam = round($elemek[$j]->sulyszam * ($szuloSulyok[$adottSzuloSzintje - 1] / 100), 1);
							}
							else 
								$sulyszam = round($elemek[$j]->sulyszam, 1);
							
							$sqlIsParent = "SELECT * FROM elemek WHERE lista_neve='" . $_COOKIE['tema'] . "' szulo_neve='" . $elemek[$j]->nev . "'";
							$resultIsParent = $conn->query($sqlIsParent);	
							if($resultIsParent->num_rows > 0){
								$sulyszam = 0;
							}
							
							$kiszamoltErtek = 0;
							for($k = 0; $k < $usersNumber; $k++){
								$sqlUserPoint = "SELECT sulypont FROM sulypontok WHERE lista_neve='" . $_COOKIE['tema'] . "' AND userid='" . $usersArray[$k] . "' AND elem_neve='" . $elemek[$j]->nev . "';";
								$resultUserPoint = $conn->query($sqlUserPoint);	
								while($row = mysqli_fetch_array($resultUserPoint)){
									if(/*$_COOKIE['usertype'] == "admin" || $_COOKIE['user'] == $usersArray[$k] || */$usersArray[$k] == $kivalasztottSzakertoNeve){
										$pontok .= "<td class='text-right'>" . round($row['sulypont'], 1) . "%</td>";
										$szakertoSulyszama = 0;
										if($adottSzuloSzintje != 0){
											$szakertoSulyszama = round($row['sulypont'] * ($szakertoSzuloSulyok[$adottSzuloSzintje - 1] / 100), 1);
										}
										else 
											$szakertoSulyszama = round($row['sulypont'], 1);
										$szakertoSzuloSulyok[$adottSzuloSzintje] = round($row['sulypont'], 1);
										if($resultIsParent->num_rows == 0) $kivalasztottSzakertoPontszamai .= $szakertoSulyszama . ", ";
									}
									$kiszamoltErtek += pow($row['sulypont'] - $elemek[$j]->sulyszam, 2);
									$pontSzamok[$k] = $row['sulypont'];
									$ossz += $row['sulypont'];
								}
							}
							
							$szuloSulyok[$adottSzuloSzintje] = $sulyszam;
							
							if($sulyszam != 0){
								$diagrammLabel .= "'" . $elemek[$j]->nev . "', ";
								$diagrammValue .= $sulyszam . ", ";
							}
							
							$adottSzuloLabel = $adottSzulo;
							if($adottSzulo == "") $adottSzuloLabel = $_COOKIE['tema'];
							if(!isset($diagramoknakLabel[$adottSzuloLabel])){
								$diagramoknakLabel[$adottSzuloLabel] = "[";
							}
							$diagramoknakLabel[$adottSzuloLabel] .= '"' . $elemek[$j]->nev . '",';
							
							if(!isset($diagramoknakValue[$adottSzuloLabel])){
								$diagramoknakValue[$adottSzuloLabel] = "[";
							}
							$diagramoknakValue[$adottSzuloLabel] .= $elemek[$j]->sulyszam . ",";
							
							$egyetertesiAllando = 1 - round(sqrt((1 / ($usersNumber - 1)) * $kiszamoltErtek), 1);
							
							$sorok .= "<tr>
								<td class='szint" . $adottSzuloSzintje . "'>" . $elemek[$j]->nev ."</td>"
								. $pontok . 
								"<td>" . $elemek[$j]->dimenzio . "</td>
								<td>" . $elemek[$j]->kizaro . "</td>
								<td>" . $elemek[$j]->idealis . "</td>
								<td>" . $elemek[$j]->fuggveny . "</td>
								<td class='text-right'>" . $sulyszam . "%</td>
								<td class='text-right'>" . $egyetertesiAllando . "</td>
								<td><a class='btn btn-default hidden' href='ujelem.php?elem=" . $elemek[$j]->nev . "'><span class='glyphicon glyphicon-pencil'></span></a></td></tr>";
							$elemek[$j]->voltMar = true; 
							$adottSzulo = $elemek[$j]->nev;
							$adottSzuloSzintje++;
							$j = 0;
							$kiirtakSzama++;
						} else {
							$j++;
							if($j == $i){
								$j = 0;
								//Megkeressük a szülő elemét és annak a szuloje lesz az adottSzulo
								$l = false;
								for($k = 0; $k < $i; $k++){
									if(!$l && $adottSzulo == $elemek[$k]->nev){
										$adottSzulo = $elemek[$k]->szulo;
										$l = true;
									}
								}
								$adottSzuloSzintje--;
							}
						}
					}
					$diagrammLabel .= "]"; $diagrammValue .= "]";
					
					if($kivalasztottSzakertoNeve != "Csoport"){
						$kivalasztottSzakertoPontszamai .= "]";
						$kivalasztottUserAdatok = '{
										backgroundColor: "blue",
										data:' . $kivalasztottSzakertoPontszamai . '
									}';
					} else $kivalasztottUserAdatok = "";
					
					$kivalasztottUserAdatok = "";
								
					echo '<table class="table" id="keszTabla">
							<thead>
								<tr>
									<th>Szempont</th>'
									. $usersString .
									'<th>Dimenzió</th>
									<th class="vekony">Kizáró érték</th>
									<th class="vekony">Ideális érték</th>
									<th class="vekony">Függvény típusa</th>
									<th class="text-right">Súly</th>
									<th class="text-right" style="width: 90px;">Egyetértés</th>
									<th></th>
								</tr>
							</thead>
							<tbody>'
								. $sorok . 
							'</tbody>
						</table>';
					echo '<button class="btn btn-info" type="button" onclick="regSugo()">?</button>
					<!--<button class="btn btn-success"><span class="glyphicon glyphicon-save margin-right"></span>Mentés</button>-->
					<button class="btn btn-danger" onclick="torlesPopup()"><span class="glyphicon glyphicon-trash margin-right"></span>Törlés</button>
					<button class="btn btn-success" onclick="archiv()"><span class="glyphicon glyphicon-folder-open margin-right"></span>Archíválás</button>';
					
					//Diagramok kiírása
					//foreach ($diagramoknakLabel as $key => $value) {
						echo '<div id="container" style="height: 300px; width: 100%;">
							<canvas id="chartContainer"></canvas>
						</div>';
						
						echo '<script>
							var data = {
								labels:' . $diagrammLabel . ',
								datasets: [
									{
										backgroundColor: "blue",
										data:' . $diagrammValue . '
									}, ' . $kivalasztottUserAdatok . '
								]
							};
							var ctx = document.getElementById("chartContainer").getContext("2d");
							ctx.canvas.width = 1140;
							ctx.canvas.height = 300;
							window.myBar = new Chart(ctx, {
								type: "bar",
								data: data,
								options: {
									maintainAspectRatio: true,
									responsive: true,
									legend: {
										display: false
									},
									scales: {
										yAxes: [ { id: "y-axis-1", type: "linear", position: "left", ticks: { min: 0, max: 90 ,
										   callback: function(value) {
											   return value + "%"
										   }} }, ],
									   xAxes: [{
											display: false,barPercentage: 0.95,categoryPercentage: 0.95
										}]
									}
								}
							});

						</script>';
					//}
				}
				
				?>
				
			<?php if($GLOBALS["allapot"] == "nemletezik") : ?>
				<p>Ez a téma még nem létezik</p>
			<?php endif; ?>
			<form action="lista.php" method="post" id="lista">
				<?php if(isset($_GET['elem'])) echo '<input type="text" id="szulo" name="szulo" maxlength="40" class="form-control hidden" value="' . $_GET['elem'] . '">'; ?>
				<?php if($_COOKIE['usertype'] == "admin") : ?>
					<?php if($GLOBALS["allapot"] == "uj") : ?>
						<?php if(isset($_GET['elem']) && $_GET['elem'] !== "") echo '<!--<a class="btn btn-default" name="vissza" href="lista.php"><span class="glyphicon glyphicon-chevron-left margin-right"></span>Vissza a gyökérhez</a>-->';?>
						<button class="btn btn-info" type="button" onclick="regSugo()">?</button>
						<button class="btn btn-default" type="button" onclick="faPopupShow()"><span class="glyphicon glyphicon-tree-conifer margin-right"></span>Fastruktúra</button>
						<button class="btn btn-success" type="submit" name="ujElem" id="ujElemButton"><span class="glyphicon glyphicon-plus margin-right"></span>Új szempont hozzáadása</button>
						<button class="btn btn-danger" type="submit" name="inputClose">Bevitel lezárása<span class="margin-left glyphicon glyphicon-off"></span></button>
					<?php elseif($GLOBALS["allapot"] == "rendezes") : ?>
						<?php if(isset($_GET['elem']) && $_GET['elem'] !== "") echo '<!--<a class="btn btn-default" name="vissza" href="lista.php"><span class="glyphicon glyphicon-chevron-left margin-right"></span>Vissza a gyökérhez</a>-->';?>
						<button class="btn btn-info" type="button" onclick="regSugo()">?</button>
						<button class="btn btn-default" type="button" onclick="faPopupShow()"><span class="glyphicon glyphicon-tree-conifer margin-right"></span>Fastruktúra</button>
						<input class="btn btn-success" type="button" value="Rendezett lista beküldése" name="sortSend" onClick="rendezettListaBekuldese()">
						<button class="btn btn-danger" type="button" name="sortCloseCheck" onclick="rendezesEllenorzesPopupNyitas()">Rendezés lezárása<span class="margin-left glyphicon glyphicon-off"></span></button>
					<?php elseif($GLOBALS["allapot"] == "skalazas") : ?>
						<div id="container" style="height: 300px; width: 100%;">
							<canvas id="chartContainer"></canvas>
						</div>
						<?php 
							echo "<table style='margin-left:40px; margin-bottom:35px;'><tr>";
							for($j = 0; $j < $GLOBALS["sulypontokSzama"]; $j++){
								echo "<td style='width:" . 1100 / $i . "px;padding-left:" . 1100 / 100 ."px;'><button type='button' class='btn btn-default downButton' id='" . str_replace(" ", "", $GLOBALS["elemek"][$j]) . "Down' style='width: 80%;margin-top:-5px;'><span class='glyphicon glyphicon-chevron-down'></span></button></td>";
							}
							echo "</tr><tr>";
							for($j = 0; $j < $GLOBALS["sulypontokSzama"]; $j++){
								echo "<td style='width:" . 1100 / $i . "px;padding-left:" . 1100 / 100 ."px;'><div class='input-group' style='width: 80%;margin-left: 10%;'><input class='form-control ertekInputok' type='text' id='" . str_replace(" ", "", $GLOBALS["elemek"][$j]) . "' style='text-align: center;' value='" . round($GLOBALS["sulyok"][$j], 1) . "' /><span class='input-group-addon' style='max-width:26px;padding-left:6px;'>%</span></div></td>";
							}
							echo "</tr></table>";
						?>
						<?php if(isset($_GET['elem']) && $_GET['elem'] !== "") echo '<!--<a class="btn btn-default" name="vissza" href="lista.php"><span class="glyphicon glyphicon-chevron-left margin-right"></span>Vissza a gyökérhez</a>-->';?>
						<button class="btn btn-info" type="button" onclick="regSugo()">?</button>
						<button class="btn btn-default" type="button" onclick="faPopupShow()"><span class="glyphicon glyphicon-tree-conifer margin-right"></span>Fastruktúra</button>
						<button class="btn btn-success" type="button" name="sortChartSend" onClick="skalazottListaBekuldese()">Súlyozott lista beküldése</button>
						<button class="btn btn-danger" type="button" name="skalaClose" onclick="sulyozasEllenorzesPopupNyitas()">Súlyozás lezárása<span class="margin-left glyphicon glyphicon-off"></span></button>
					<?php endif; ?>
				<?php elseif($_COOKIE['usertype'] == "user") : ?>
					<?php if($GLOBALS["allapot"] == "uj") : ?>
						<?php if(isset($_GET['elem']) && $_GET['elem'] !== "") echo '<!--<a class="btn btn-default" name="vissza" href="lista.php"><span class="glyphicon glyphicon-chevron-left margin-right"></span>Vissza a gyökérhez</a>-->';?>
						<button class="btn btn-info" type="button" onclick="regSugo()">?</button>
						<button class="btn btn-default" type="button" onclick="faPopupShow()"><span class="glyphicon glyphicon-tree-conifer margin-right"></span>Fastruktúra</button>
						<button class="btn btn-success" type="submit" name="ujElem" id="ujElemButton"><span class="glyphicon glyphicon-plus margin-right"></span>Új szempont hozzáadása</button>
					<?php elseif($GLOBALS["allapot"] == "rendezes") : ?>
						<?php if(isset($_GET['elem']) && $_GET['elem'] !== "") echo '<!--<a class="btn btn-default" name="vissza" href="lista.php"><span class="glyphicon glyphicon-chevron-left margin-right"></span>Vissza a gyökérhez</a>-->';?>
						<button class="btn btn-info" type="button" onclick="regSugo()">?</button>
						<button class="btn btn-default" type="button" onclick="faPopupShow()"><span class="glyphicon glyphicon-tree-conifer margin-right"></span>Fastruktúra</button>
						<input class="btn btn-success" type="button" value="Rendezett lista beküldése" name="sortSend" onClick="rendezettListaBekuldese()">
					<?php elseif($GLOBALS["allapot"] == "skalazas") : ?>
						<div id="container" style="height: 300px; width: 100%;">
							<canvas id="chartContainer"></canvas>
						</div>
						<?php 
							echo "<table style='margin-left:40px; margin-bottom:35px;'><tr>";
							for($j = 0; $j < $GLOBALS["sulypontokSzama"]; $j++){
								echo "<td style='width:" . 1100 / $i . "px;padding-left:" . 1100 / 100 ."px;'><button type='button' class='btn btn-default downButton' id='" . str_replace(" ", "", $GLOBALS["elemek"][$j]) . "Down' style='width: 80%; margin-top:-5px'><span class='glyphicon glyphicon-chevron-down'></span></button></td>";
							}
							echo "</tr><tr>";
							for($j = 0; $j < $GLOBALS["sulypontokSzama"]; $j++){
								echo "<td style='padding-top:20px;width:" . 1100 / $i. "px;padding-left:" . 1100 / 100 . "px;'><div class='input-group' style='width: 80%;margin-left: 10%;'><input class='form-control ertekInputok' type='text' id='" . $GLOBALS["elemek"][$j] . "' style='text-align: center;' value='" . round($GLOBALS["sulyok"][$j], 1) . "' /><span class='input-group-addon' style='max-width:26px;padding-left:6px;'>%</span></div></td>";
							}
							echo "</tr></table>";
						?>
						<?php if(isset($_GET['elem']) && $_GET['elem'] !== "") echo '<!--<a class="btn btn-default" name="vissza" href="lista.php"><span class="glyphicon glyphicon-chevron-left margin-right"></span>Vissza a gyökérhez</a>-->';?>
						<button class="btn btn-info" type="button" onclick="regSugo()">?</button>
						<button class="btn btn-default" type="button" onclick="faPopupShow()"><span class="glyphicon glyphicon-tree-conifer margin-right"></span>Fastruktúra</button>
						<input class="btn btn-success" type="button" value="Súlyozott lista beküldése" name="sortChartSend" onClick="skalazottListaBekuldese()">
					<?php endif; ?>
				<?php endif; ?>
			</form>
		</div>
	</div>
	
	<div class="sugo hidden">
		<div class="layer"></div>
		<div class="alert alert-info" role="alert">
			<?php if($GLOBALS["allapot"] == "uj") : ?>
				<?php
					require("service/db.php");
					$sql = "SELECT * FROM sugo WHERE nev='uj';";
					$result = $conn->query($sql);
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				?>
			<?php elseif($GLOBALS["allapot"] == "rendezes") : ?>	
				<?php
					require("service/db.php");
					$sql = "SELECT * FROM sugo WHERE nev='rendezes';";
					$result = $conn->query($sql);
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				?>
			<?php elseif($GLOBALS["allapot"] == "skalazas") : ?>
				<?php
					require("service/db.php");
					$sql = "SELECT * FROM sugo WHERE nev='sulyozas';";
					$result = $conn->query($sql);
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				?>
			<?php elseif($GLOBALS["allapot"] == "kesz") : ?>
				<?php
					require("service/db.php");
					$sql = "SELECT * FROM sugo WHERE nev='kesz';";
					$result = $conn->query($sql);
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				?>
			<?php endif; ?>
			<button class="btn btn-danger" onClick="sugoBezaras()"><span class="glyphicon glyphicon-remove margin-right"></span>Bezárás</button>
		</div>
	</div>
	
	<div class="torlesPopupClass hidden">
		<div class="layer"></div>
		<div class="alert alert-info" role="alert">
			<h3>FIGYELMEZTETÉS!</h3>
			<p>Biztosan törölni akarja a témához tartozó összes bejegyzést?</p>
			<button class="btn btn-success" onClick="torles()"><span class="glyphicon glyphicon-ok margin-right"></span>Igen</button>
			<button class="btn btn-danger" onClick="torlesPopupBezaras()"><span class="glyphicon glyphicon-remove margin-right"></span>Nem</button>
		</div>
	</div>
	
	<div class="archivalasPopup hidden">
		<div class="layer"></div>
		<div class="alert alert-info" role="alert">
			<h3>FIGYELMEZTETÉS!</h3>
			<p>Az archíválás megtörtént!</p>
			<button class="btn btn-success" onClick="archivalasPopupBezaras()">Rendben</button>
		</div>
	</div>
	
	<?php if($GLOBALS["allapot"] == "rendezes") : ?>
		<div class="rendezesEllenorzesPopup hidden">
			<div class="layer"></div>
			<div class="alert alert-info" role="alert">
				<h3>Kendall-féle rangkonkordancia fastruktúrában</h3>
				<p><table style="border: 0px none;"><?php 
				require("service/db.php");
			
				$sql = "SELECT elemek.elem_neve, elemek.szulo_neve, SUM(rangsorpontok.pontszam) as pontszamossz, COUNT(userid) as usersszama FROM elemek LEFT JOIN rangsorpontok ON elemek.elem_neve = rangsorpontok.elem_neve AND elemek.lista_neve = rangsorpontok.lista_neve WHERE elemek.lista_neve='" . $_COOKIE['tema'] . "' GROUP BY elemek.elem_neve;";
				$result = $conn->query($sql);
				
				$sqlUsers = "SELECT DISTINCT(userid) as user FROM rangsorpontok WHERE lista_neve='" . $_COOKIE['tema'] . "' ORDER BY userid DESC;";
				$resultUsers = $conn->query($sqlUsers);	
				$usersCount = $resultUsers->num_rows;
							
				$i = 0;
				while($row = mysqli_fetch_array($result)){
					$elemek[$i] = new Elem();
					$elemek[$i]->nev = $row['elem_neve'];
					$elemek[$i]->szulo = $row['szulo_neve'];
					$elemek[$i]->pontszam = $row['pontszamossz'];
					$elemek[$i]->usercount = $row['usersszama'];
					$elemek[$i]->voltMar = false;
					$i++;
				}
				
				$j = 0;
				$kiirtakSzama = 0;
				$adottSzulo = "";
				$adottSzuloSzintje = 1;
				$n = 0;
				$sum = 0;
				while($kiirtakSzama < $i){
					if($elemek[$j]->voltMar == false && $elemek[$j]->szulo == $adottSzulo){
						$nyil = "";
						if($adottSzuloSzintje != 0) $nyil = "&rarr;";
						$elemek[$j]->voltMar = true;
						$szuloVagyTema = $adottSzulo;
						if($adottSzulo == "") $szuloVagyTema = $_COOKIE['tema'];
						if(isset($szuloOsszeg[$szuloVagyTema])){
							$szuloOsszeg[$szuloVagyTema] += $elemek[$j]->pontszam;
							$szuloSzamossaga[$szuloVagyTema]++;
						} else {
							$szuloOsszeg[$szuloVagyTema] = $elemek[$j]->pontszam;
							$szuloSzamossaga[$szuloVagyTema] = 1;
							$szint[$szuloVagyTema] = $adottSzuloSzintje - 1;
						}
						$adottSzulo = $elemek[$j]->nev;
						$adottSzuloSzintje++;
						$j = 0;
						$kiirtakSzama++;
					} else {
						$j++;
						if($j == $i){
							$j = 0;
							//Megkeressük a szülő elemét és annak a szuloje lesz az adottSzulo
							$l = false;
							for($k = 0; $k < $i; $k++){
								if(!$l && $adottSzulo == $elemek[$k]->nev){
									$adottSzulo = $elemek[$k]->szulo;
									$l = true;
								}
							}
							$adottSzuloSzintje--;
						}
					}
				}
				
				foreach ($szuloOsszeg as $key => $value) {
					$negyzetesElteres = 0;
					for($k = 0; $k < $i; $k++){
						$szuloVagyTema = $elemek[$k]->szulo;
						if($elemek[$k]->szulo == "") $szuloVagyTema = $_COOKIE['tema'];
						if($szuloVagyTema == $key){
							$negyzetesElteres += pow($elemek[$k]->pontszam - $value / $szuloSzamossaga[$key], 2);
						}
					}
					$szempont = "SELECT DISTINCT(userid) FROM rangsorpontok, elemek WHERE elemek.lista_neve=rangsorpontok.lista_neve AND elemek.elem_neve=rangsorpontok.elem_neve AND elemek.szulo_neve='" . $elemek[$k]->szulo . "' AND elemek.lista_neve='" . $_COOKIE['tema'] . "'";
					$resultSzempont = $conn->query($szempont);
					
					$teljesEgyetertes = pow($usersCount, 2) * (pow($szuloSzamossaga[$key], 3) - $szuloSzamossaga[$key]) / 12;
					$nyil = "";
					if($szint[$key] != 0) $nyil = "&rarr;";	 
					echo /*"<span class='szint" . $szint[$key] . "'>" . $nyil . */"<tr><td class='cimke'>" . $key . " (" . $resultSzempont->num_rows . " szakértő súlyozta): </td><td class='ertek'>" . round($negyzetesElteres / $teljesEgyetertes, 1) . "</td></tr>";
				}
					
				?></table></p>
				<p>Biztosan lezárja a rendezést?</p>
				<form method="post" action="lista.php"><button class="btn btn-success" name="sortClose" name="sortClose" onclick="rendezesLezarasa()"><span class="glyphicon glyphicon-ok margin-right"></span>Igen</button>
				<button class="btn btn-danger" onClick="rendezesEllenorzesPopupBezaras()"><span class="glyphicon glyphicon-remove margin-right"></span>Nem</button></form>
			</div>
		</div>
	<?php endif; ?>
	
	<?php if($GLOBALS["allapot"] == "skalazas") : ?>
		<div class="sulyozasrakerultPopup hidden">
			<div class="layer"></div>
			<div class="alert alert-info" role="alert">
				<h3>FIGYELMEZTETÉS!</h3>
				<p>A súlyozás megtörtént!</p>
				<button class="btn btn-success" onClick="sulyozasrakerultPopupBezaras()">Rendben</button>
			</div>
		</div>
		
		<div class="sulyozasEllenorzesPopup popup hidden">
			<div class="layer"></div>
			<div class="alert alert-info" role="alert">
				<h3>Egyetértési együttható</h3>
				<p><table style="border: 0px none;"><?php 
				require("service/db.php");
			
				$sql = "SELECT elemek.elem_neve, AVG(sulypont) as sulypontatlag, szulo_neve FROM elemek LEFT JOIN sulypontok ON elemek.elem_neve = sulypontok.elem_neve AND elemek.lista_neve=sulypontok.lista_neve WHERE elemek.lista_neve='" . $_COOKIE['tema'] . "' GROUP BY elemek.elem_neve ORDER BY sulypontatlag DESC;";
				$result = $conn->query($sql);
				$n = $result->num_rows;
				
				
				$sqlUsers = "SELECT DISTINCT(userid) FROM sulypontok WHERE lista_neve='" . $_COOKIE['tema'] . "';";
				$resultUsers = $conn->query($sqlUsers);	
				$m = $resultUsers->num_rows;
				
				$kiszamoltErtek = 0;
				$szempontNeve = "";
				$teljes = 0;
				while($row = mysqli_fetch_array($result)){
					$szempontNeve = $row['elem_neve'];
					$atlag = $row['sulypontatlag'];
					$szempont = "SELECT * FROM sulypontok WHERE lista_neve='" . $_COOKIE['tema'] . "' AND elem_neve='" . $szempontNeve . "'";
					$resultSzempont = $conn->query($szempont);
					$kiszamoltErtek = 0;
					while($rowInner = mysqli_fetch_array($resultSzempont)){
						$kiszamoltErtek += pow($rowInner['sulypont'] - $atlag, 2);
					}
					$teljes += $kiszamoltErtek;
					echo "<tr><td class='cimke'>"; echo $szempontNeve . " ("; echo $resultSzempont->num_rows; echo " szakértő súlyozta): </td><td class='ertek'>"; echo 1 - round(sqrt((1 / ($m - 1)) * $kiszamoltErtek), 1); echo "</td></tr>";
				}				
				echo "<tr><td class='cimke'>Egyetértés a teljes súlyrendszernél: </td><td class='ertek'>"; echo 1 - round(sqrt(1 / ($m * $n - 1) * $teljes), 1); echo "</td></tr>";
					
				?></table></p>
				<p>Biztosan lezárja a súlyozást?</p>
				<form method="post" action="lista.php"><button class="btn btn-success" name="skalaClose" name="skalaClose"><span class="glyphicon glyphicon-ok margin-right"></span>Igen</button>
				<button class="btn btn-danger" onClick="popupBezaras()" type="button"><span class="glyphicon glyphicon-remove margin-right"></span>Nem</button></form>
			</div>
		</div>
	<?php endif; ?>
	
	<div class="fapopup hidden">
		<div class="layer"></div>
		<div class="alert alert-info" role="alert">
			<h3>Minősítő szempontok struktúrája</h3>
			<div class="fastruktura">
			<?php
				require("service/db.php");
			
				$result = $conn->query($sql);
				
				$sql = "SELECT * FROM elemek WHERE lista_neve='" . $_COOKIE['tema'] . "';";
				
				if($GLOBALS["allapot"] == "rendezes"){
					$sql = "SELECT elemek.elem_neve, elemek.szulo_neve FROM elemek LEFT JOIN (SELECT * FROM rangsorpontok WHERE userid='" . $_COOKIE['user'] . "') as rangsorpontok ON rangsorpontok.lista_neve=elemek.lista_neve AND rangsorpontok.elem_neve=elemek.elem_neve WHERE elemek.lista_neve='" . $_COOKIE['tema'] . "' ORDER BY rangsorpontok.pontszam ASC;";
				} else if($GLOBALS["allapot"] == "skalazas"){
					$sql = "SELECT elemek.elem_neve, elemek.szulo_neve FROM elemek LEFT JOIN (SELECT * FROM sulypontok WHERE userid='" . $_COOKIE['user'] . "') as sulypontok ON sulypontok.lista_neve=elemek.lista_neve AND sulypontok.elem_neve=elemek.elem_neve WHERE elemek.lista_neve='" . $_COOKIE['tema'] . "' ORDER BY sulypontok.sulypont DESC;";
					//echo $sql;
				}
				
				$result = $conn->query($sql);
				
							
				$i = 0;
				while($row = mysqli_fetch_array($result)){
					$elemek[$i] = new Elem();
					$elemek[$i]->nev = $row['elem_neve'];
					$elemek[$i]->szulo = $row['szulo_neve'];
					$elemek[$i]->voltMar = false;
					$i++;
				}
				
				$kiirando = "<strong><a href='lista.php'> " . $_COOKIE['tema'] ."</a></strong><br/>";
				$j = 0;
				$kiirtakSzama = 0;
				$adottSzulo = "";
				$adottSzuloSzintje = 1;
				while($kiirtakSzama < $i){
					if($elemek[$j]->voltMar == false && $elemek[$j]->szulo == $adottSzulo){
						$nyil = "";
						if($adottSzuloSzintje != 0) $nyil = '<img src="img/arrow.png" style="width:20px;">';
						$kiirando .= "<span class='szint" . $adottSzuloSzintje . "'>" . $nyil . " <a href='lista.php?elem=" . urlencode($elemek[$j]->nev) . "'>" . $elemek[$j]->nev ."</a></span>";
						
						$sql = "SELECT sulypont FROM sulypontok WHERE elem_neve='" . $elemek[$j]->nev . "' AND userid='" . $_COOKIE['user'] .  "' AND lista_neve='" . $_COOKIE['tema'] . "';";
						$result = $conn->query($sql);
						while($row = mysqli_fetch_array($result)){
							$kiirando .= " (" . $row['sulypont'] . "%)";
						}
						
						$kiirando .= "<br/>";
						$elemek[$j]->voltMar = true; 
						$adottSzulo = $elemek[$j]->nev;
						$adottSzuloSzintje++;
						$j = 0;
						$kiirtakSzama++;
					} else {
						$j++;
						if($j == $i){
							$j = 0;
							//Megkeressük a szülő elemét és annak a szuloje lesz az adottSzulo
							$l = false;
							for($k = 0; $k < $i && !$l; $k++){
								if(!$l && $adottSzulo == $elemek[$k]->nev){
									$adottSzulo = $elemek[$k]->szulo;
									$l = true;
								}
							}
							$adottSzuloSzintje--;
						}
					}
				}
				
				echo $kiirando;
			?>
			</div>
			<button class="btn btn-danger" onClick="faPopUpBezaras()"><span class="glyphicon glyphicon-remove margin-right"></span>Bezárás</button>
		</div>
	</div>
	
	<script>	
	function strip(html){
	   var tmp = document.createElement("DIV");
	   tmp.innerHTML = html;
	   return tmp.textContent || tmp.innerText || "";
	}
	
	function rendezettListaBekuldese(){
		var list = document.getElementById('sortable').childNodes;
		var theArray = [];
		for(var i=0;i < list.length; i++) {
			var arrValue = list[i].innerHTML;
			theArray.push(strip(arrValue));
		}
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				window.location.href = window.location.href;
			}
		};
		xhttp.open("GET", "service/pointsave.php?list=" + theArray, true);
		xhttp.send();
	}
	
	function skalazottListaBekuldese(){
		var i = 0, elozoErtek, ossz = 0, values = [], labels = [], l = true;
		$(".ertekInputok").each(function(index){
		  labels.push($(this).context.id);
		  values.push($(this).val());
		  if(i == 0){
			  elozoErtek = parseInt($(this).val());
		  } else if(elozoErtek <= parseInt($(this).val())){
			  l = false;
		  }
		  ossz += parseInt($(this).val());
		  i++;
		});
		//if(ossz == 100){
			if(l){
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						//document.getElementById("demo").innerHTML = this.responseText;
					}
				};
				xhttp.open("GET", "service/scalasave.php?labels=" + labels + "&values=" + values, true);
				xhttp.send();
				$(".sulyozasrakerultPopup").removeClass("hidden");
			} else {
				alert("Az oszlopok értékeinek sorrendje nem változhat. Kérem adja meg úgy az értékeket, hogy a sorrend nem változik!")
			}
		/*} else {
			alert("Az oszlopok összege nem 100. Kérem adja meg úgy az adatok, hogy 100 maradjon!");
		}*/
		
	}
	
	function szazasHelyreAllitas(){
		var osszeg = 0;
		var utolso = "";
		$(".ertekInputok").each(function(index){
			osszeg += parseFloat($(this).val());
			utolso = $(this).context.id;
		});	
		console.log(utolso);
		var tmp = (parseFloat($("#"+utolso).val())-(osszeg - 100)).toFixed(1);
		if(osszeg != 100) $("#"+utolso).val(tmp);
	}
	
	function helyreallitas(id, regiErtek, ujErtek){
		var data = [];
		var data2 = [];
		var db = 0;
		$(".ertekInputok").each(function(index){
			data.push($(this).val());
			db++;
		});
		$(".ertekInputok").each(function(index){
			if($(this).context.id != id){
				var tmp = parseFloat($(this).val());
				tmp = tmp * (100 - ujErtek) / (100 - regiErtek);
				tmp = tmp.toFixed(1);
				$(this).val(tmp);
			}
			data2.push($(this).val());
		});
		
		//Ellenőrzés
		var l = true;
		var i = 0; var elozoErtek;
		$(".ertekInputok").each(function(index){
		  if(i == 0){
			  elozoErtek = parseFloat($(this).val());
		  } else if(elozoErtek < parseFloat($(this).val())){
			  l = false;
		  }
		  if(parseFloat($(this).val()) >= 90.5 || parseFloat($(this).val()) <= 0.5) l = false;
		  elozoErtek = data2[i];
		  i++;
		});
		
		if(!l){
			i = 0;
			$(".ertekInputok").each(function(index){
				$(this).val(data[i]);
				i++;
			});
			$("#"+id).val(regiErtek);
		}
		szazasHelyreAllitas();
	}
	
	function changeCanvas(dataArray){
		$('#chartContainer').remove();
		$('#container').append('<canvas id="chartContainer"><canvas>');
		var labels = [];
		$(".ertekInputok").each(function(index){
			labels.push($(this).context.id);
		});
		var data = {
			labels:labels,
			datasets: [
				{
					backgroundColor: "blue",
					data:dataArray
				}
			]
		};
		var ctx = document.getElementById("chartContainer").getContext("2d");
		ctx.canvas.width = 1140;
		ctx.canvas.height = 300;
		window.myBar = new Chart(ctx, {
			type: "bar",
			data: data,
			options: {
				/*onClick: function(e){
					window.location.href = "lista.php?elem=" + this.getElementAtEvent(e)[0]._model.label;
				},*/
				maintainAspectRatio: true,
				responsive: true,
				legend: {
					display: false
				},
				scales: {
					yAxes: [ { id: "y-axis-1", type: "linear", position: "left", ticks: { min: 0, max: 90 ,
           callback: function(value) {
               return value + "%"
           } } }, ],
		   xAxes: [{
                display: false,barPercentage: 0.95,categoryPercentage: 0.95
            }]
				}
			}
		});
	}
	
	$( ".upButton" ).click(function(attr) {
	  var tmp = parseFloat($("#" + attr.target.id.replace("Up", "")).val());
	  var regiErtek = tmp; var ujErtek = tmp + 1;
	  $("#" + attr.target.id.replace("Up", "")).val(tmp + 1);
	  helyreallitas(attr.target.id.replace("Up", ""), regiErtek, ujErtek);
	  var dataArray = [];
	  $(".ertekInputok").each(function(index){
		  dataArray.push($(this).val());
	  });
	  changeCanvas(dataArray);
	  return false;
	});
	
	$( ".downButton" ).click(function(attr) {
	  var tmp = parseFloat($("#" + attr.target.id.replace("Down", "")).val());
	  var regiErtek = tmp; var ujErtek = tmp - 1;
	  $("#" + attr.target.id.replace("Down", "")).val(tmp - 1);
	  var dataArray = [];
	  helyreallitas(attr.target.id.replace("Down", ""), regiErtek, ujErtek);
	  $(".ertekInputok").each(function(index){
		  dataArray.push($(this).val());
	  });
	  changeCanvas(dataArray);
	  return false;
	});
	
	$('.ertekInputok').on('focusin', function(){
		$(this).attr("data-oldvalue", $(this).val());
	});
	
	$(".ertekInputok").focusout(function(attr){
		helyreallitas($(this).context.id, $(this).attr("data-oldvalue"), $(this).val());
		var dataArray = [];
		$(".ertekInputok").each(function(index){
			dataArray.push($(this).val());
		});
		changeCanvas(dataArray);
		return false;
	}); 
	
	function archiv(){
		var data = {
		  h: '<html><head><meta charset="utf-8"><link href="../css/style.css" rel="stylesheet"/><link href="../css/bootstrap.min.css" rel="stylesheet"/></head><body><table class="table" style="width:80%;margin-left:auto;margin-right:auto;">' + $("#keszTabla").html() + "</table></body></html>"
		};
		$.post("service/archiv.php", data);
		$(".archivalasPopup").removeClass("hidden");
	}
	
	function archivalasPopupBezaras(){
		$(".archivalasPopup").addClass("hidden");
	}
	
	function rendezesEllenorzesPopupBezaras(){
		$(".rendezesEllenorzesPopup").addClass("hidden");
	}
	
	function rendezesEllenorzesPopupNyitas(){
		$(".rendezesEllenorzesPopup").removeClass("hidden");
	}
	
	function sulyozasEllenorzesPopupNyitas(){
		$(".sulyozasEllenorzesPopup").removeClass("hidden");
	}
	
	function torles(){
		var data = {};
		$.post("service/torles.php", data)
		  .done(function( data ) {
			window.location.href = "bejelentkezes.php";
		  });
	}
	
	function torlesPopup(){
		$(".torlesPopupClass").removeClass("hidden");
	}
	
	function torlesPopupBezaras(){
		$(".torlesPopupClass").addClass("hidden");
	}
	
	function sulyozasrakerultPopupBezaras(){
		window.location.href = window.location.href;
	}
	
	$( document ).ready(function() {
		if($("#sortable li").length >= 10){
			$("#ujElemButton").attr("disabled", "disabled");
		}
		szazasHelyreAllitas();
	});	
	
	function popupBezaras(){
		$(".popup").addClass("hidden");
	}
	
	</script>	
</body>
</html>