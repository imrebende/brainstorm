<?php
if(isset($_POST['listaGomb'])){
	if(isset($_POST['ujTema']))
		header('Location: '. "lista.php?tema=" . $_POST['ujTema'] . "&jelszo=" . $_POST['password']);
	else
		header('Location: '. "lista.php?tema=" . $_POST['tema'] . "&jelszo=" . $_POST['password']);
}
if(isset($_GET['logout'])){
			setcookie("user", "", time()-3600*72, "/");
			unset($_COOKIE['user']);
			setcookie("usertype", "", time()-3600*72, "/");
			unset($_COOKIE['usertype']);
		}
		
		if(isset($_POST['userid']) && isset($_POST['password'])){
			require("service/db.php");
					
			$count = "SELECT * FROM users WHERE userid='" . $_POST['userid'] . "' AND password='" . $_POST['password'] . "';";
			$result = $conn->query($count);
			
			if($result->num_rows == 1) {
				$cookie_name = "user";
				$cookie_value = $_POST['userid'];
				setcookie($cookie_name, $cookie_value, time() + 3600*72, "/");
				$_COOKIE['user'] = $cookie_value;
				while($row = mysqli_fetch_array($result)){
					setcookie('usertype', $row['usertype'], time() + 3600*72, "/");
					$_COOKIE['usertype'] = $row['usertype'];
				}
			}
		}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Bejelentkezés</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/style.css" rel="stylesheet" />
	<style>
		.form input, .form select{
			width: 50%;
			display: initial;
		}
		.form input#tema, .form input#ujTema{
			width: 100%;
			max-width: 377px;
		}
		.form label{
			width: 100px;
		}
		.form .balraLabel{
			width: 100%;
			text-align: left;
			margin-left: 28%;
		}
		
		#userid, #password{
			width: 231px;
		}
	</style>
</head>
<body>
	<?php 	
		if(isset($_POST['ujTema'])){
			require("service/db.php");
		
			$sql = "INSERT INTO listak (lista_neve, allapot, jelszo)
				VALUES ('" . $_POST['ujTema'] . "', 'uj','" . $_POST['password'] . "');";
			$result = $conn->query($sql);
					
		}
		require_once('menu.php');
		
		
		echo '<div class="container margin-top">
		<div class="main"><div class="logError">';

		if(isset($_POST['userid']) && isset($_POST['password'])){
			if($result->num_rows == 0){
				echo "Nincs ilyen felhasználónév, vagy rossz a jelszó!";
			}
		}
		echo '</div>';
	?>
	
			<?php if(!isset($_COOKIE['user'])) : ?>
			<div class="form">
				<h1>Bejelentkezés</h1>
				<form method="post" action="bejelentkezes.php">
					<div class="form-group">
						<label for="userid">Felhasználó név: </label>
						<input type="text" id="userid" name="userid" maxlength="8" class="form-control" required>
					</div>
					<div class="form-group">
						<label for="password">Jelszó: </label>
						<input type="password" id="password" name="password" maxlength="20" class="form-control" required>
					</div>
					<!--<a class="btn btn-warning" href="elfelejtettjelszo.php">Elfelejtett jelszó</a>-->
					<button class="btn btn-info" type="button" onclick="regSugo()" style="margin-left: 68px;">?</button>
					<input class="btn btn-success" type="submit" value="Bejelentkezés" id="bejelentkezes" style="width: 230px;">
				</form>
			</div>
			<?php elseif($_COOKIE['usertype'] == "admin") : ?>
			<div class="form">
				<h1>Téma létrehozása / választása</h1>
				<form method="post" action="bejelentkezes.php">
					<div class="form-group">
						<label for="userid" class="balraLabel">Téma: </label>
						<input type="text" id="ujTema" name="ujTema" maxlength="35" class="form-control" required>
					</div>
					<div class="form-group">
						<label for="password">Jelszó: </label>
						<input type="password" id="password" name="password" maxlength="20" class="form-control">
					</div>
					<button class="btn btn-info" type="button" onclick="regSugo()">?</button>
					<button class="btn btn-success" type="submit" name="listaGomb">Tovább<span class="glyphicon glyphicon-chevron-right margin-left"></span></button>
					<button class="btn btn-success" type="submit"><span class="glyphicon glyphicon-plus margin-right"></span>Létrehozás</button>
				</form>
				<table id="listak" class="table" style="margin-top:20px;">
						<thead>
								<tr>
									<th>Téma</th>
									<th>Státusz</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php
									require("service/db.php");
									
									$sql = "SELECT * FROM listak;";
									$result = $conn->query($sql);
									while($row = mysqli_fetch_array($result)){
										echo "<tr id='" . str_replace(" ", "%20", $row['lista_neve']) . "Row'>";
										echo "<td>" . $row['lista_neve'] . "</td>";
										$statusz = "";
										if($row['allapot'] == "uj"){
											$statusz = "új";
										} else if($row['allapot'] == "rendezes"){
											$statusz = "rendezés";
										} else if($row['allapot'] == "skalazas"){
											$statusz = "súlyozás";
										} else if($row['allapot'] == "kesz"){
											$statusz = "kész";
										}
										$pw = "<span class='hidden pw'>" . $row['jelszo'] . "</span>";
										echo "<td>" . $statusz . $pw . "</td>";
										echo "<td style='text-align: center;'>
												<button class='btn btn-success szakertokButton' type='button' id='" . $row['lista_neve'] . "Szakertok'>Szakértők</button> 
												<button class='btn btn-success ujJelszoButton' type='button' id='" . $row['lista_neve'] . "Jelszo'>Új jelszó</button> 
												<button class='btn btn-danger removeButton' type='button' id='" . $row['lista_neve'] . "'>Törlés</button>
												<button class='btn btn-success openButton' type='button' id='" . str_replace(" ", "%20", $row['lista_neve']) . "Open'>Megnyitás</button>
												</td>";
										echo "</tr>";
									}
								?>
							</tbody>
					</table>
			</div>
			<?php else : ?>
			<div class="form">
				<h1>Téma választása</h1>
				<form method="post" action="bejelentkezes.php">
					<div class="form-group">
						<label for="userid" class="balraLabel">Téma: </label>
						<input type="text" id="tema" name="tema" maxlength="35" class="form-control" required>
					</div>
					<div class="form-group">
						<label for="password">Jelszó: </label>
						<input type="password" id="password" name="password" maxlength="40" class="form-control">
					</div>
					<button class="btn btn-info" type="button" onclick="regSugo()">?</button>
					<button class="btn btn-success" type="submit" name="listaGomb">Tovább<span class="glyphicon glyphicon-chevron-right margin-left"></span></button>
				</form>
			</div>
			<?php endif; ?>
		</div>
	</div>
	
	<div class="sugo hidden">
		<div class="layer"></div>
		<div class="alert alert-info" role="alert">
			<?php
					require("service/db.php");
					
					if(isset($_COOKIE['user']))
						$sql = "SELECT * FROM sugo WHERE nev='temavalasztas';";
					else
						$sql = "SELECT * FROM sugo WHERE nev='bejelentkezes';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?>
			<button class="btn btn-danger" onClick="sugoBezaras()"><span class="glyphicon glyphicon-remove margin-right"></span>Bezárás</button>
		</div>
	</div>
	
	<div class="ujJelszoPopup hidden">
	
			<div class="layer"></div>
			<div class="alert alert-info" role="alert">
				<div class="form-group">
						<label for="userid" class="balraLabel">Téma: </label>
						<input type="text" id="temaUjJelszohoz" name="temaUjJelszohoz" maxlength="35" class="form-control">
					</div>
				<div class="form-group">
						<label for="password">Új jelszó: </label>
						<input type="password" id="newpassword" name="newpassword" maxlength="40" class="form-control">
					</div>
				<button class="btn btn-success" type='button' onClick="ujJelszoValtoztatas()">Változtatás</button>
				<button class="btn btn-danger" type='button' onClick="ujJelszoPopupBezaras()">Bezárás</button>
			</div>
		</div>
	
	<?php if (isset($_POST['ujTema'])) : ?>
		<div class="ujTemaPopup">
			<div class="layer"></div>
			<div class="alert alert-info" role="alert">
				<h3>FIGYELMEZTETÉS!</h3>
				<p>Az új téma létrejött <?php echo $_POST['ujTema']; ?> néven!</p>
				<button class="btn btn-success" onClick="ujTemaPopupBezaras()">Rendben</button>
			</div>
		</div>
		
	<?php endif; ?>
	<div class="torlesPopupClass hidden">
			<div class="layer"></div>
			<div class="alert alert-info" role="alert">
				<h3>FIGYELMEZTETÉS!</h3>
				<p>Biztosan törölni akarja a témát és a témához tartozó összes bejegyzést (<span id="temaNeve"></span>)?</p>
				<button class="btn btn-success" onClick="torles()"><span class="glyphicon glyphicon-ok margin-right"></span>Igen</button>
				<button class="btn btn-danger" onClick="torlesPopupBezaras()"><span class="glyphicon glyphicon-remove margin-right"></span>Nem</button>
			</div>
		</div>
	
	<script src="js/jquery.js"></script>
	<script>
		$( function() {
			$("#bejelentkezes").addClass("active");
		} );
		
		$('.ujJelszoButton').click(function(){
			var id = $(this).context.id.replace("Jelszo", "");
			$("#temaUjJelszohoz").val(id);
			$(".ujJelszoPopup").removeClass("hidden");
		});
		
		$('.szakertokButton').click(function(){
			window.location.href = "szakertok.php?tema=" + $(this).context.id.replace("Szakertok", "");
		});
		
		$('.openButton').click(function(){
			var id = $(this).context.id.replace("Open", "");
			//document.getElementById("szerda18:00Row").getElementsByClassName("pw")
			var idString = document.getElementById(id + "Row").getElementsByClassName("pw");
			//console.log($(idString).html());
			var pw = $(idString).html() == undefined;
			if($(idString).html() == undefined) pw = "";
			
			console.log(pw);
			window.location.href = "lista.php?tema=" + id + "&jelszo=" + $(idString).html();
		});
		
		$('.removeButton').click(function(){
			$(".torlesPopupClass").removeClass("hidden");
			$("#temaNeve").html($(this).context.id);
			window.scrollTo(0, 0);
		});
		
		function ujJelszoValtoztatas(){
		  var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					//document.getElementById("demo").innerHTML = this.responseText;
				}				
			};
			xhttp.open("GET", "service/ujjelszo.php?id=" + $("#temaUjJelszohoz").val() + "&jelszo=" + $("#newpassword").val(), true);
			xhttp.send();
			ujJelszoPopupBezaras();
		}
		
		function ujTemaPopupBezaras(){
			$(".ujTemaPopup").addClass("hidden");
		}
		
		function ujJelszoPopupBezaras(){
			$(".ujJelszoPopup").addClass("hidden");
		}
		
		function torlesPopupBezaras(){
			$(".torlesPopupClass").addClass("hidden");
		}
		
		function torlesPopup(){
			$(".torlesPopupClass").removeClass("hidden");
			
		}
		
		function torles(){
			var data = {};
			$.post("service/torles.php?tema="+$("#temaNeve").html(), data)
			  .done(function( data ) {
				window.location.href = "bejelentkezes.php";
			  });
		}
	</script>
	<script src="js/script.js"></script>

</body>
</html>