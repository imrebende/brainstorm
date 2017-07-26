<!DOCTYPE html>
<html>
<head>
	<title>Új elem hozzáadása</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/style.css" rel="stylesheet" />
	<script src="js/jquery.js"></script>
	<script src="js/script.js"></script>
	<style>
		form.ujElem input, form.ujElem select{
			width: 50%;
			display: initial;
			max-width: 230px;
		}
		form.ujElem input#szempont, form.ujElem input#regiszempont{
			width: 100%;
			max-width: 377px;
		}
		form.ujElem label{
			width: 100px;
		}
		form.ujElem .balraLabel{
			width: 100%;
			text-align: left;
			margin-left: 28%;
		}
	</style>
</head>
<body>

<?php
					
					require("service/db.php");
					
					
		
					$sql = "SELECT * FROM listak WHERE allapot='kesz' ORDER BY CreatedOrModified DESC;";
					$result = $conn->query($sql);

					$o = 0;
					$tema = "tema";
					if($result->num_rows > 0){
						while(($row = mysqli_fetch_array($result)) && $o < 1){
							$tema = $row['lista_neve'];
							$o++;
						}
					}
					
					$sql = "SELECT * FROM listak WHERE lista_neve='" . $tema . "';";
					$result = $conn->query($sql);

					while($row = mysqli_fetch_array($result)){
						$GLOBALS['fmea'] = $row['isfmea'];
					}

					$sql = "SELECT elemek.elem_neve, dimenzio, kizaro_ertek, idealis_ertek, AVG(sulypont) as sulypontatlag, szulo_neve, fuggveny_ertek FROM elemek LEFT JOIN sulypontok ON elemek.elem_neve = sulypontok.elem_neve AND elemek.lista_neve=sulypontok.lista_neve WHERE elemek.lista_neve='" . $tema. "' GROUP BY elemek.elem_neve ORDER BY sulypontatlag DESC;";
					$result = $conn->query($sql);
								
					class Elem {
						public $sulyszam;
						public $nev;
						public $dimenzio;
						public $szulo;
						public $fuggveny;
						public $voltMar;
						public $kizaro;
						public $idealis;
					}
								
					$i = 0;
					while($row = mysqli_fetch_array($result)){
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
					
					$sorok = "";
					$j = 0;
					$kiirtakSzama = 0;
					$adottSzulo = "";
					$adottSzuloSzintje = 0;
					
					$sqlUsers = "SELECT DISTINCT(rangsorpontok.userid) as user, users.email as email FROM rangsorpontok LEFT JOIN users ON rangsorpontok.userid=users.userid WHERE rangsorpontok.lista_neve='" . $tema . "' ORDER BY rangsorpontok.userid DESC;";
					$resultUsers = $conn->query($sqlUsers);		
					
					$usersString = "";
					$emailsString = "";
					$usersArray = [];
					$usersNumber = 0;
					while($row = mysqli_fetch_array($resultUsers)){
						$usersString .= "<td>" . $row['user'] . "</td>";
						$emailsString .= "<td>" . $row['email'] . "</td>";
						$usersArray[$usersNumber] = $row['user'];
						$usersNumber++;
					}
					
					$szuloSulyok = [];
					
					$kiirando = "<table class='table'>";
					$kiirando .= "<tr><td>Ág</td><td>Alág</td><td>Levél</td><td>Dimenzió</td><td>Kizáró érték</td><td>Ideális érték</td><td>Függvény típusa</td><td>Átlag</td>" . $usersString . "</tr>";
					
					while($kiirtakSzama < $i){
						if($elemek[$j]->voltMar == false && $elemek[$j]->szulo == $adottSzulo){
							$pontok = ""; $min = 0; $max = 0;
							$pontSzamok = [];
							$ossz = 0;
							
							$sulyszam = 0;
							if($adottSzuloSzintje != 0 && !$GLOBALS['fmea'])
								$sulyszam = round($elemek[$j]->sulyszam * ($szuloSulyok[$adottSzuloSzintje - 1] / 100), 1);
							else
								$sulyszam = round($elemek[$j]->sulyszam, 1);
							$szuloSulyok[$adottSzuloSzintje] = $sulyszam;
							
							$sqlIsParent = "SELECT * FROM elemek WHERE szulo_neve='" . $elemek[$j]->nev . "'";
							$resultIsParent = $conn->query($sqlIsParent);	
							if($resultIsParent->num_rows > 0 && !$GLOBALS['fmea']){
								$sulyszam = 0;
							}
							
							for($k = 0; $k < $usersNumber; $k++){
								$sqlUserPoint = "SELECT sulypont FROM sulypontok WHERE lista_neve='" . $tema . "' AND userid='" . $usersArray[$k] . "' AND elem_neve='" . $elemek[$j]->nev . "';";
								$resultUserPoint = $conn->query($sqlUserPoint);	
								while($row = mysqli_fetch_array($resultUserPoint)){
										if(!$GLOBALS['fmea']){
											if($adottSzuloSzintje != 0 && !$GLOBALS['fmea'])
												$tmp = round($row['sulypont'] * ($szuloSulyok[$adottSzuloSzintje - 1] / 100), 1);
											else
												$tmp = round($row['sulypont'], 1);
											$pontok .= "<td>" . $tmp . "%</td>";
										}
										else{
											if($adottSzuloSzintje != 0 && !$GLOBALS['fmea'])
												$tmp = round($row['sulypont'] * ($szuloSulyok[$adottSzuloSzintje - 1] / 100), 1);
											else
												$tmp = round($row['sulypont'], 1);
											$pontok .= "<td>" . $tmp . "</td>";
										}
									$pontSzamok[$k] = $row['sulypont'];
									$ossz += $row['sulypont'];
								}
							}
							
							if($sulyszam == 0){
								$pontok = "";
								for($k = 0; $k < $usersNumber; $k++){
									if($GLOBALS['fmea'])
										$pontok .= "<td></td>";
									else
										$pontok .= "<td></td>";
								}
								$sulyszam = "";
							} else {
								if(!$GLOBALS['fmea']){
									$sulyszam .= "%";
								}
							}
							
							
							$nev = "";
							if($adottSzuloSzintje == 0){
								$nev = "<td>" . $elemek[$j]->nev . "</td><td></td><td></td>";
							} else if ($adottSzuloSzintje == 1){
								$nev = "<td></td><td>" . $elemek[$j]->nev . "</td><td></td>";
							} else if ($adottSzuloSzintje == 2){
								$nev = "<td></td><td></td><td>" . $elemek[$j]->nev . "</td>";
							}
							
							if($GLOBALS['fmea'])
								$kiirando .= "<tr>" . $nev . "<td>" . $elemek[$j]->dimenzio . "</td><td>" . $elemek[$j]->kizaro . "</td><td>" . $elemek[$j]->idealis . "</td><td>" . $elemek[$j]->fuggveny . "</td><td>" . $sulyszam . "</td>" . $pontok . "</tr>";
							else
								$kiirando .= "<tr>" . $nev . "<td>" . $elemek[$j]->dimenzio . "</td><td>" . $elemek[$j]->kizaro . "</td><td>" . $elemek[$j]->idealis . "</td><td>" . $elemek[$j]->fuggveny . "</td><td>" . $sulyszam . "</td>" . $pontok . "</tr>";

						
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
					
					$kiirando .= "<tr><td/><td/><td/><td/><td/><td/><td/><td/>" . $emailsString . "</tr>";

					$kiirando .= "</table>";
					echo $kiirando;


?>

</body>
</html>