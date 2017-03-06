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

	<?php require_once('menu.php'); ?>

	<?php
	echo $_GET['elem'];
	if(isset($_GET['elem'])){
			require("service/db.php");
					
			$isParentSql = "SELECT * FROM elemek WHERE lista_neve='" . $_COOKIE['tema'] . "' AND szulo_neve='" . $_GET['elem'] . "';";
			$resultisParentSql = $conn->query($isParentSql);
			$GLOBALS["isParent"] = $resultisParentSql->num_rows > 0;
					
			$count = "SELECT * FROM elemek WHERE lista_neve='" . $_COOKIE['tema'] . "' AND elem_neve='" . $_GET['elem'] . "';";
			$result = $conn->query($count);
			
			if($result->num_rows == 1) {
				while($row = mysqli_fetch_array($result)){
					echo '<script>
					$(function() {
					  $("#szempont").val("' . $row['elem_neve'] . '");
					  $("#dimenzio").val("' . $row['dimenzio'] . '");
					  $("#kizErtek").val("' . $row['kizaro_ertek'] . '");
					  $("#idErtek").val("' . $row['idealis_ertek'] . '");
					  $("#fvErtek").val("' . $row['fuggveny_ertek'] . '");
					});';
				
					if($_COOKIE['usertype'] == "admin"){
						echo '
								$(function() {
								  $("#regiszempont").val("' . $row['elem_neve'] . '");
								});';
					} else {
						echo '$("#szempont").attr("readonly", "readonly");';
					}
					$sqlTema = "SELECT * FROM listak WHERE lista_neve='" . $_COOKIE['tema'] . "';";
					$resultTema = $conn->query($sqlTema);
					
					if($resultTema->num_rows == 0){
						$GLOBALS["allapot"] = "nemletezik";
					}
					while($row = mysqli_fetch_array($resultTema)){
						$GLOBALS["allapot"] = $row['allapot'];
					}
					
					/*if($GLOBALS["allapot"] == "kesz" && $_COOKIE['usertype'] == "admin"){
						$sqlInner = "SELECT AVG(sulypont) as sulypont FROM sulypontok WHERE lista_neve='" . $_COOKIE['tema'] . "' AND elem_neve='" . $_GET['elem'] . "';";
						$resultInner = $conn->query($sqlInner);
						
						while($rowInner = mysqli_fetch_array($resultInner)){
							echo '$(function() {$("#suly").val("' . $rowInner['sulypont'] . '");});';
						}
					}*/
					echo '</script>';
				}
				
			}
					
			$sql = "SELECT * FROM listak WHERE lista_neve='" . $_COOKIE['tema'] . "';";
			$result = $conn->query($sql);
			
			if($result->num_rows == 0){
				$GLOBALS["allapot"] = "nemletezik";
			}
			
			//Rendezhetőség JS
			while($row = mysqli_fetch_array($result)){
				$GLOBALS["allapot"] = $row['allapot'];
			}
		}
	?>
	
	<div class="container margin-top">
		<div class="main">
			<div class="form">
				<?php if(isset($_GET['elem'])): ?>
					<h1>Szempont paraméterei</h1>
				<?php else : ?>
					<h1>Új szempont hozzáadása</h1>
				<?php endif; ?>
				<h3>
				<?php 
					if(isset($_COOKIE['tema'])) $cim = $_COOKIE['tema'];
					if(isset($_GET['szulo'])){
						require("service/db.php");
						
						$cim = $_GET['szulo'];
						$szulo = $_GET['szulo'];
						while($szulo != ""){
							$sql = "SELECT * FROM elemek WHERE elem_neve='" . $szulo . "' AND lista_neve='" . $_COOKIE['tema'] . "';";
							$result = $conn->query($sql);
							
							while($row = mysqli_fetch_array($result)){						
								$szulo = $row['szulo_neve'];
								$cim = $szulo . " > " . $cim;
							}
						}
						
						$cim = $_COOKIE['tema'] . $cim;
					}
					echo $cim;
				?>
				</h3>
				<form id="ujElem" class="ujElem" method="post" action="lista.php<?php if(isset($_GET['szulo'])) echo "?elem=" . $_GET['szulo']; ?>">
					<?php if(isset($_GET['elem'])): ?>
						<?php if($_COOKIE['usertype'] == "admin") : ?>
							<div class="form-group">
								<label for="szempont" class="balraLabel">Régi megnevezés: </label>
								<input type="text" id="regiszempont" name="regiszempont" maxlength="35" class="form-control" readonly>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					<?php if(isset($_GET['szulo'])) echo '<input type="text" id="szulo" name="szulo" maxlength="40" class="form-control hidden" value="' . $_GET['szulo'] . '">'; ?>
					<div class="form-group">
						<?php if(isset($_GET['elem']) && $_COOKIE['usertype'] == "admin"): ?>
							<label for="szempont" class="balraLabel">Új megnevezés: </label>
						<?php else : ?>
							<label for="szempont" class="balraLabel">Megnevezés (kötelező): </label>
						<?php endif; ?>
						<input type="text" id="szempont" name="szempont" maxlength="35" class="form-control" required>
					</div>
					<?php if(isset($_GET['elem']) && !$GLOBALS["isParent"]): ?>
						<div class="form-group">
							<label for="userid">Dimenzió: </label>
							<input id="dimenzio" name="dimenzio" maxlength="20" class="form-control">
						</div>
						<div class="form-group">
							<label for="userid">Kizáró érték: </label>
							<input type="kizErtek" id="kizErtek" name="kizErtek" maxlength="20" class="form-control">
						</div>
						<div class="form-group">
							<label for="idErtek">Ideális érték: </label>
							<input type="text" id="idErtek" name="idErtek" maxlength="20" class="form-control">
						</div>
						<div class="form-group">
							<label for="fvErtek">Függvény: </label>
							<select id="fvErtek" name="fvErtek" class="form-control">
							  <option value=""></option>
							  <option value="1">Lineáris</option>
							  <option value="2">Progresszív</option>
							  <option value="3">Degresszív</option>
							  <option value="4">Haranggörbe</option>
							</select>
						</div>
					<?php endif; ?>
					<!--<?php if($GLOBALS["allapot"] == "kesz" && $_COOKIE['usertype'] == "admin"): ?>
						<div class="form-group">
							<label for="idErtek">Súlyszám: </label>
							<input type="text" id="suly" name="suly" maxlength="40" class="form-control">
						</div>
					<?php endif; ?>-->
					<button class="btn btn-info" type="button" onclick="regSugo()">?</button>
					<button class="btn btn-default" name="vissza" onclick="back()"><span class="glyphicon glyphicon-chevron-left margin-right"></span>Szempontok listája</button>
					<button class="btn btn-default" type="button" onclick="faPopupShow()"><span class="glyphicon glyphicon-tree-conifer margin-right"></span>Fastruktúra</button>
					<?php
						if(isset($_GET['elem'])) 
							echo '<button class="btn btn-success bekuldes" type="submit" value="" name="update">ENTER (módosítás)<span class="glyphicon glyphicon-pencil margin-left"></span></button>';
						else 
							echo '<button class="btn btn-success bekuldes" type="submit" value="" name="insert">ENTER (rögzítés)<span class="glyphicon glyphicon-plus margin-left"></span></button>';
					?>
				</form>
			</div>
		</div>
	</div>
	
	<div class="sugo hidden">
		<div class="layer"></div>
		<div class="alert alert-info" role="alert">
			
				<?php
					require("service/db.php");
					if($_GET['elem'])
						$sql = "SELECT * FROM sugo WHERE nev='elemmodositasa';";
					else 
						$sql = "SELECT * FROM sugo WHERE nev='ujelem';";
					$result = $conn->query($sql);
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				?>
			
			<button class="btn btn-danger" onClick="sugoBezaras()"><span class="glyphicon glyphicon-remove margin-right"></span>Bezárás</button>
		</div>
	</div>
	
	<div class="fapopup hidden">
		<div class="layer"></div>
		<div class="alert alert-info" role="alert">
			<h3>Minősítő szempontok struktúrája</h3>
			<div class="fastruktura">
			<?php
				require("service/db.php");
			
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
			
				$sql = "SELECT * FROM elemek WHERE lista_neve='" . $_COOKIE['tema'] . "';";
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
						if($adottSzuloSzintje != 0) $nyil = "&rarr;";
						$kiirando .= "<span class='szint" . $adottSzuloSzintje . "'>" . $nyil . " <a href='lista.php?elem=" . $elemek[$j]->nev . "'>" . $elemek[$j]->nev ."</a></span><br/>";
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
				
				echo $kiirando;
			?>
			</div>
			<button class="btn btn-danger" onClick="faPopUpBezaras()"><span class="glyphicon glyphicon-remove margin-right"></span>Bezárás</button>
		</div>
	</div>
	
	
	<script>
		function back(){
			window.location.href = "lista.php<?php if(isset($_GET['szulo'])) echo "?elem=" . $_GET['szulo']; ?>";
		}
		
		$('html').keypress(function (e) {
		  if (e.which == 13) {
			$('.bekuldes').click();
		  }
		});
		
		$( "#dimenzio" ).change(function() {
		  if($("#dimenzio").val() == "pont" || $("#dimenzio").val() == "pont"){
			  $("#kizErtek").val(0)
			  $("#idErtek").val(9);
			  $("#fvErtek").val(1);
		  } else if($("#dimenzio").val() == "I/N"){
			  $("#kizErtek").val(0)
			  $("#idErtek").val(1);
			  $("#fvErtek").val(1);
		  }
		});
	
	</script>
</body>
</html>