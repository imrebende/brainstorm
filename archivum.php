<?php header("Content-Type: text/html; charset=utf-8");?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Archívum</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/style.css" rel="stylesheet" />
</head>
<body>

	<?php require_once('menu.php'); ?>
	
	<div class="container margin-top">
		<div class="main">
			<h1>Archívum</h1>
			<ul>
				<?php
					$files = scandir('archiv/');
					foreach($files as $file) {
						if($file != "." && $file != ".."){
							$fileName = htmlentities(mb_convert_encoding($file, 'UTF-8', 'latin2'), ENT_QUOTES, 'UTF-8');
							echo "<li><a href='archiv/" . $fileName . "'>" . $fileName . "</a></li>";
						}
					}
				?>
			</ul>
		</div>
	</div>
	
	<script src="js/jquery.js"></script>
	<script src="js/script.js"></script>
	<script>
		$( function() {
			$("#archivum").addClass("active");
		} );
	</script>
	
</body>
</html>