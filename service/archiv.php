<?php

	header('Content-type: text/html; charset=utf-8');

	if(/*isset($_POST['h'])*/true){
		//HTML létrehozása
		//$tema = strtr(strip_tags(($_COOKIE['tema'])), "éáíüúűőöóÉÁÍÜÚŰÓÖŐ ", "eaiuuuoooEAIUUUOOO.");
		//echo $_COOKIE['tema'];
		$tema = $_COOKIE['tema'];
		$myfile = fopen("../archiv/" . iconv("UTF-8", "ISO-8859-2//TRANSLIT", $tema) . ".html", "w");
		stream_filter_append($myfile, 'convert.iconv.UTF-8/OLD-ENCODING');
		stream_copy_to_stream($myfile, fopen($output, 'w'));
		fwrite($myfile, $_POST['h']);
		fclose($myfile);
		
		//CSV létrehozása
		require("db.php");
		
		$sql = "SELECT * FROM listak WHERE lista_neve='" . $_COOKIE['tema'] . "';";
		$result = $conn->query($sql);

		while($row = mysqli_fetch_array($result)){
			$GLOBALS['fmea'] = $row['isfmea'];
		}
	
		/*$myfile = fopen("../archiv/" . iconv("UTF-8", "ISO-8859-1//TRANSLIT", $_COOKIE['tema']) . ".csv", "w");
		stream_filter_append($myfile, 'convert.iconv.UTF-8/OLD-ENCODING');
		stream_copy_to_stream($myfile, fopen($output, 'w'));*/
		$csv = fopen("../archiv/" . iconv("UTF-8", "ISO-8859-2//TRANSLIT", $tema) . ".csv", "w");
		
		//is_archived beállítása
		$sql = "UPDATE listak SET is_archived=true WHERE lista_neve='" . $_COOKIE['tema']  . "';";
		$result = $conn->query($sql);
		
		$sql = "SELECT * FROM elemek LEFT JOIN sulypontok ON elemek.elem_neve = sulypontok.elem_neve AND elemek.lista_neve=sulypontok.lista_neve WHERE elemek.lista_neve='" . $_COOKIE['tema'] . "' ORDER BY sulypontok.sulypont DESC;";
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
			$elemek[$i]->sulyszam = $row['sulypont'];
			$elemek[$i]->szulo = $row['szulo_neve'];
			$elemek[$i]->fuggveny = $row['fuggveny_ertek'];
			$elemek[$i]->voltMar = false;
			$i++;
		}
		
		/*$sqlUsers = "SELECT DISTINCT(userid) as user FROM rangsorpontok WHERE lista_neve='" . $_COOKIE['tema'] . "' ORDER BY userid DESC;";
		$resultUsers = $conn->query($sqlUsers);	*/
		
		/*$usersString = "";
		$usersArray = [];
		$usersNumber = 0;
		while($row = mysqli_fetch_array($resultUsers)){
			$usersString .= $row['user'] . ";";
			$usersArray[$usersNumber] = $row['user'];
			$usersNumber++;
		}*/
		
		
		
		/*$j = 0;
		$kiirtakSzama = 0;
		$adottSzulo = "";
		$adottSzuloSzintje = 0;
		while($kiirtakSzama < $i){
			if($elemek[$j]->voltMar == false && $elemek[$j]->szulo == $adottSzulo){
				$pontok = ""; $min = 0; $max = 0;
				$pontSzamok = [];
				$ossz = 0;
				for($k = 0; $k < $usersNumber; $k++){
					$sqlUserPoint = "SELECT pontszam FROM rangsorpontok WHERE lista_neve='" . $_COOKIE['tema'] . "' AND userid='" . $usersArray[$k] . "' AND elem_neve='" . $elemek[$j]->nev . "';";
					$resultUserPoint = $conn->query($sqlUserPoint);	
					while($row = mysqli_fetch_array($resultUserPoint)){
						$pontok .= $row['pontszam'] . ";";
						$pontSzamok[$k] = $row['pontszam'];
						$ossz += $row['pontszam'];
					}
				}
				$kiirando .= str_repeat(" ", $adottSzuloSzintje * 2) . $elemek[$j]->nev .";" . $pontok . $elemek[$j]->dimenzio . ";" . $elemek[$j]->kizaro . ";" . $elemek[$j]->idealis . ";" . $elemek[$j]->fuggveny . ";" . $elemek[$j]->sulyszam . "\n";
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
		}*/
		
					//Sorok összerakása
					$sorok = "";
					$j = 0;
					$kiirtakSzama = 0;
					$adottSzulo = "";
					$adottSzuloSzintje = 0;
					
					$sqlUsers = "SELECT DISTINCT(userid) as user FROM rangsorpontok WHERE lista_neve='" . $_COOKIE['tema'] . "' ORDER BY userid DESC;";
					$resultUsers = $conn->query($sqlUsers);		
					
					$usersString = "";
					$usersArray = [];
					$usersNumber = 0;
					while($row = mysqli_fetch_array($resultUsers)){
						if($_COOKIE['usertype'] == "admin" || $_COOKIE['user'] == $row['user']){
							$usersString .= $row['user'] . " (" . $row['email'] . ");";
						}
						$usersArray[$usersNumber] = $row['user'];
						$usersNumber++;
					}
					
					$szuloSulyok = [];
					
					$kiirando = "";
					//$kiirando .= "Ág;Alág;Levél;Dimenzió;Kizáró érték;Ideális érték;Függvény típusa;Súly" . $usersString . "\n";
					if(!$GLOBALS['fmea'])
						$kiirando .= "Ág;Alág;Levél;Dimenzió;Kizáró érték;Ideális érték;" . $usersString . "\n";
					else
						$kiirando .= "Ág;Alág;Levél;Dimenzió;Kizáró érték;Ideális érték;" . $usersString . "\n";
					
					while($kiirtakSzama < $i){
						if($elemek[$j]->voltMar == false && $elemek[$j]->szulo == $adottSzulo){
							$pontok = ""; $min = 0; $max = 0;
							$pontSzamok = [];
							$ossz = 0;
							for($k = 0; $k < $usersNumber; $k++){
								$sqlUserPoint = "SELECT sulypont FROM sulypontok WHERE lista_neve='" . $_COOKIE['tema'] . "' AND userid='" . $usersArray[$k] . "' AND elem_neve='" . $elemek[$j]->nev . "';";
								$resultUserPoint = $conn->query($sqlUserPoint);	
								while($row = mysqli_fetch_array($resultUserPoint)){
									if($_COOKIE['usertype'] == "admin" || $_COOKIE['user'] == $usersArray[$k]){
										if(!$GLOBALS['fmea'])
											$pontok .= round($row['sulypont'], 1) . "%;";
										else
											$pontok .= round($row['sulypont'], 1);
									}
									$pontSzamok[$k] = $row['sulypont'];
									$ossz += $row['sulypont'];
								}
							}
							$sulyszam = 0;
							if($adottSzuloSzintje != 0)
								$sulyszam = round($elemek[$j]->sulyszam * ($szuloSulyok[$adottSzuloSzintje - 1] / 100), 1);
							else 
								$sulyszam = round($elemek[$j]->sulyszam, 1);
							$szuloSulyok[$adottSzuloSzintje] = $sulyszam;
							
							$sqlIsParent = "SELECT * FROM elemek WHERE szulo_neve='" . $elemek[$j]->nev . "'";
							$resultIsParent = $conn->query($sqlIsParent);	
							if($resultIsParent->num_rows > 0){
								$sulyszam = 0;
							}
							
							//$sorok .= "<tr><td class='szint" . $adottSzuloSzintje . "'>" . $elemek[$j]->nev ."</td>" . $pontok . "<td>" . $elemek[$j]->dimenzio . "</td><td>" . $elemek[$j]->kizaro . "</td><td>" . $elemek[$j]->idealis . "</td><td>" . $elemek[$j]->fuggveny . "</td><td>" . $sulyszam . "%</td><td><a class='btn btn-default hidden' href='ujelem.php?elem=" . $elemek[$j]->nev . "'><span class='glyphicon glyphicon-pencil'></span></a></td></tr>";
							
							$nev = "";
							if($adottSzuloSzintje == 0){
								$nev = $elemek[$j]->nev . ";;";
							} else if ($adottSzuloSzintje == 1){
								$nev = ";" . $elemek[$j]->nev . ";";
							} else if ($adottSzuloSzintje == 2){
								$nev = ";;" . $elemek[$j]->nev;
							}
							
							$kiirando .= $nev . ";" . $elemek[$j]->dimenzio . ";" . $elemek[$j]->kizaro . ";" . $elemek[$j]->idealis . /*";" . $elemek[$j]->fuggveny . ";" . $sulyszam . */";" . $pontok . "\n";

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
		
		//$enc=$enc=='ISO-8859-2'?'ISO-8859-2':'UTF-8';  	
		$kiirando = mb_convert_encoding($kiirando, 'UCS-2LE', 'UTF-8');
		fwrite($csv, $kiirando);
		fclose($csv);
	}
?>