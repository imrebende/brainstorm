<!DOCTYPE html>
<html>
<head>
	<title>Szakértők</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/style.css" rel="stylesheet" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

	<?php require_once('menu.php'); ?>

	<div class="container margin-top">
		<div class="main">
			<h3>Szakértők listája - <?php echo $_GET['tema']; ?></h3>
			<?php
			
				require("service/db.php");
			
				$sql = 'SELECT rangsorpontok.userid, email FROM rangsorpontok, users WHERE rangsorpontok.userid=users.userid AND rangsorpontok.lista_neve="' . $_GET['tema'] . 
					   '" UNION SELECT sulypontok.userid, email FROM sulypontok, users WHERE sulypontok.userid = users.userid AND sulypontok.lista_neve="' . $_GET['tema'] . 
					   '" UNION SELECT elemek.creator, email FROM elemek, users WHERE elemek.creator = users.userid AND elemek.lista_neve="' . $_GET['tema'] . '";';
				$result = $conn->query($sql);
				
				echo "<table class='table'>";
				while($row = mysqli_fetch_array($result)){
					echo "<tr>";
					echo '<td>' . $row['userid'] . '</td>';
					echo '<td>' . $row['email'] . '</td>';
					echo "</tr>";
				}
				echo "</table>";
			
			?>
		
		</div>
	</div>

	<script src="js/jquery.js"></script>
	<script src="js/script.js"></script>
	
</body>
</html>