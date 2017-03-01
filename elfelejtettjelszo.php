<html>
<head>

	<title>Elfelejtett jelszó</title>

	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/style.css" rel="stylesheet" />
	
</head>
<body>
	<?php require_once('menu.php'); ?>
	<div class="container margin-top">
		<div class="main">
			<div class="form">
				<h1>Elfelejtett jelszó</h1>
				<?php
					if(isset($_POST['email'])){
						require("service/db.php");
						
						$sql = "SELECT * FROM users WHERE email='" . $_POST['email'] . "';";
						$result = $conn->query($sql);
						
						if($result->num_rows > 0){
							while($row = mysqli_fetch_array($result)){
								$headers = 'From: beiraai@gmail.com' . "\r\n" .
									'Reply-To: beiraai@gmail.com' . "\r\n" .
									'X-Mailer: PHP/' . phpversion();
								$message = "Kedves " . $row['userid'] . "!/n/nAz Ön jelszava az oldalon a következő: " . $row['password'];
								mail($row['email'], 'VOC - Jelszó újraküldése', $message/*, $headers*/);
							}
							echo "<p>A jelszó elküldésre került az e-mail címére.</p>";
						} else {
							echo "<p>Az Ön e-mail címe még nincsen regisztrálva a rendszerben.</p>";
						}
						
					}
				?>
				<form method="post" action="elfelejtettjelszo.php">
					<div class="form-group">
						<label for="userid">E-mail cím (kötelező): </label>
						<input type="email" id="email" name="email" maxlength="100" class="form-control" required>
					</div>
					<input class="btn btn-success" type="submit" value="Jelszó újraküldése">
				</form>
			</div>
		</div>
	</div>

</body>
</html>