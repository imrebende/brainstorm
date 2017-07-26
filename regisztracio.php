<!DOCTYPE html>
<html>
<head>
	<title>Regisztráció</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/style.css" rel="stylesheet" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
		.form input, .form select{
			width: 50%;
			display: initial;
		}
		.form label{
			width: 120px;
		}
		#userid, #password, #email{
			width: 230px;
		}
		#regisztracioButton{
			width: 230px;
		}
	</style>
</head>
<body>

	<?php require_once('menu.php'); ?>

	<div class="container margin-top">
		<div class="main">

		<div class="logError">
		<?php 
		
		if(isset($_POST['userid']) && isset($_POST['password']) && isset($_POST['email'])){
			require("service/db.php");
			
			$count = "SELECT * FROM users WHERE userid='" . $_POST['userid'] . "' OR email='" . $_POST['email'] . "';";
			$result = $conn->query($count);
			
			//Regisztráció: 1000 főnél több nem lehet a DB-be; ha már volt ilyen, akkor azt kiírja; egyébként létrehozza az új felhasználót
			if($result->num_rows >= 1000){
				echo "Maximum 1000 felhasználó lehet";
			} else if($result->num_rows > 0){
				echo "Már van ilyen felhasználónév/e-mail cím!";
			} else {
				$sql = "INSERT INTO users (userid, password, email, usertype)
					VALUES ('" . $_POST['userid'] . "','" . $_POST['password'] . "','" . $_POST['email'] . "', 'user');";
				$result = $conn->query($sql);
				echo "Sikeres regisztráció. Kérem lépjen be a bejelentkező felületen!";
			}
		}
		
		?>
		</div>
		
			<div class="form">
				<h1>Regisztráció</h1>
				<form method="post" action="regisztracio.php">
					<div class="form-group">
						<label for="userid">Felhasználó név (kötelező): </label>
						<input type="text" id="userid" name="userid" maxlength="8" class="form-control" required>
					</div>
					<div class="form-group">
						<label for="userid">E-mail cím: </label>
						<input type="email" id="email" name="email" maxlength="20" class="form-control">
					</div>
					<div class="form-group">
						<label for="password">Jelszó (kötelező): </label>
						<input type="password" id="password" name="password" maxlength="20" class="form-control" required>
					</div>
					<button class="btn btn-info" type="button" onclick="regSugo()" style="margin-left: 84px;">?</button>
					<button id="regisztracioButton" class="btn btn-success" type="submit">Regisztráció</button>
				</form>
			</div>
		
		</div>
	</div>
	
	<div class="sugo hidden">
		<div class="layer"></div>
		<div class="alert alert-info" role="alert">
			<?php
				require("service/db.php");
									
					$sql = "SELECT * FROM sugo WHERE nev='regisztracio';";
					$result = $conn->query($sql);
					
					while($row = mysqli_fetch_array($result)){
						echo $row['szoveg'];
					}
			?>
			<button class="btn btn-danger" onClick="sugoBezaras()"><span class="glyphicon glyphicon-remove margin-right"></span>Bezárás</button>
		</div>
	</div>

	<script src="js/jquery.js"></script>
	<script src="js/script.js"></script>
	<script>
		$( function() {
			$("#regisztracio").addClass("active");
		} );
	</script>
	
</body>
</html>