<!DOCTYPE html>
<html>
<head>
	<title>Szakértők</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/style.css" rel="stylesheet" />
</head>
<body>

	<?php require_once('menu.php'); ?>

	<div class="container margin-top">
		<div class="main">
			<h3>Szakértők listája - <?php echo $_GET['tema']; ?></h3>
			<?php
			
				require("service/db.php");
			
				$sql = 'SELECT rangsorpontok.userid, email FROM rangsorpontok, users WHERE rangsorpontok.userid=users.userid
AND rangsorpontok.lista_neve="' . $_GET['tema'] . '" UNION SELECT sulypontok.userid, email FROM sulypontok, users WHERE sulypontok.userid = users.userid AND sulypontok.lista_neve="' . $_GET['tema'] . '";';
				$result = $conn->query($sql);
				
				while($row = mysqli_fetch_array($result)){
					echo $row['email'] . ' ';
				}
			
			?>
		
		</div>
	</div>

	<script src="js/jquery.js"></script>
	<script src="js/script.js"></script>
	
</body>
</html>