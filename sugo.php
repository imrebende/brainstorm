<!DOCTYPE html>
<html>
<head>
	<title>Súgó oldalak</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/style.css" rel="stylesheet" />
	<script src="js/jquery.js"></script>
	<script src="js/script.js"></script>
</head>
<body>
	<?php require_once('menu.php'); ?>

	<div class="container margin-top">
		<div class="main">
			<div class="form">
				<h1>Súgó oldalak</h1>
				<p>HTML kódot kell felküldeni.
					<ul>
						<li> &lt;p&gt; Bekezdés szövege &lt;/p&gt;</li>
						<li> &lt;ul&gt;
								&lt;li&gt; Felsorolás szövege 1 &lt;/li&gt;
								&lt;li&gt; Felsorolás szövege 2 &lt;/li&gt;
								&lt;li&gt; ... &lt;/li&gt;
							&lt;/ul&gt;</li>
					</ul>
				</p>
				<h3>Bejelentkezés</h3>
				 <textarea rows="8" cols="50" id="bejelentkezesSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='bejelentkezes';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea><br/>
				 <button class="sugoFelkuldes" id="bejelentkezes">Súgó felküldése</button>
				 <h3>Regisztráció</h3>
				 <textarea rows="8" cols="50" id="regisztracioSzoveg"><?php
					require("service/db.php");
					$sql = "SELECT * FROM sugo WHERE nev='regisztracio';";
					$result = $conn->query($sql);
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea><br/>
				 <button class="sugoFelkuldes" id="regisztracio">Súgó felküldése</button>
				<h3>Téma választása</h3>
				 <textarea rows="8" cols="50" id="temavalasztasSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='temavalasztas';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea><br/>
				 <button class="sugoFelkuldes" id="temavalasztas">Súgó felküldése</button>
				 <h3>Lista - új</h3>
				 <textarea rows="8" cols="50" id="ujSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='uj';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea> <br/>
				 <button class="sugoFelkuldes" id="uj">Súgó felküldése</button>
				 <h3>Új elem bevitele</h3>
				 <textarea rows="8" cols="50" id="ujelemSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='ujelem';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea> <br/>
				 <button class="sugoFelkuldes" id="ujelem">Súgó felküldése</button>
				 <h3>Elem módosítása/paraméterei</h3>
				 <textarea rows="8" cols="50" id="elemmodositasaSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='elemmodositasa';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea> <br/>
				 <button class="sugoFelkuldes" id="elemmodositasa">Súgó felküldése</button>
				 <h3>Lista - rendezés</h3>
				 <textarea rows="8" cols="50" id="rendezesSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='rendezes';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea> <br/>
				 <button class="sugoFelkuldes" id="rendezes">Súgó felküldése</button>
				 <h3>Lista - súlyozás</h3>
				 <textarea rows="8" cols="50" id="sulyozasSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='sulyozas';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea> <br/>
			<button class="sugoFelkuldes" id="sulyozas">Súgó felküldése</button>
			<h3>Lista - kész</h3>
				 <textarea rows="8" cols="50" id="keszSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='kesz';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea> <br/>
			<button class="sugoFelkuldes" id="kesz">Súgó felküldése</button>
			
			<h2>FMEA súgók</h2>
			
				 <h3>Lista - új</h3>
				 <textarea rows="8" cols="50" id="fmeaujSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='fmeauj';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea> <br/>
				 <button class="sugoFelkuldes" id="fmeauj">Súgó felküldése</button>
				 <h3>Új elem bevitele</h3>
				 <textarea rows="8" cols="50" id="fmeaujelemSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='fmeaujelem';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea> <br/>
				 <button class="sugoFelkuldes" id="fmeaujelem">Súgó felküldése</button>
				 <h3>Elem módosítása/paraméterei</h3>
				 <textarea rows="8" cols="50" id="fmeaelemmodositasaSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='fmeaelemmodositasa';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea> <br/>
				 <button class="sugoFelkuldes" id="fmeaelemmodositasa">Súgó felküldése</button>
				 <h3>Lista - rendezés</h3>
				 <textarea rows="8" cols="50" id="fmearendezesSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='fmearendezes';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea> <br/>
				 <button class="sugoFelkuldes" id="fmearendezes">Súgó felküldése</button>
				 <h3>Lista - súlyozás</h3>
				 <textarea rows="8" cols="50" id="fmeasulyozasSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='fmeasulyozas';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea> <br/>
			<button class="sugoFelkuldes" id="fmeasulyozas">Súgó felküldése</button>
			<h3>Lista - kész</h3>
				 <textarea rows="8" cols="50" id="fmeakeszSzoveg"><?php
					require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='fmeakesz';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
				 ?></textarea> <br/>
			<button class="sugoFelkuldes" id="fmeakesz">Súgó felküldése</button>
			</div>
			
		</div>
	</div>
	<script>
		$( ".sugoFelkuldes" ).click(function(attr) {
		  var id = attr.target.id;
		  
		  var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					//document.getElementById("demo").innerHTML = this.responseText;
				}				
			};
			xhttp.open("GET", "service/sugosave.php?id=" + id + "&szoveg=" + $("#" + id + "Szoveg").val(), true);
			xhttp.send();
		});
	</script>
</body>
</html>