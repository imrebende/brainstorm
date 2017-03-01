<?php 

	$regOrLog = "";
	if(isset($_COOKIE['user'])){
		$regOrLog .= '<li id="lista"><a href="bejelentkezes.php">Téma választása</a></li>
		<li id="archivum"><a href="archivum.php">Archívum</a></li>
		<li id="user"><a href="bejelentkezes.php?logout=a">Kijelentkezés</a></li>';
		if($_COOKIE['usertype'] == "admin"){
			$regOrLog .= '<li id="sugo"><a href="sugo.php">Súgók kezelése</a></li>';
		}
	} else {
		$regOrLog .= '<li id="bejelentkezes"><a href="bejelentkezes.php">Bejelentkezés</a></li>
				<li id="regisztracio"><a href="regisztracio.php">Regisztráció</a></li>';
	}

	echo '<nav class="navbar navbar-inverse navbar-fixed-top">
		  <div class="container">
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </button>
			  <a class="navbar-brand" href="#">Voice Of the Customer</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
			  <ul class="nav navbar-nav">' . $regOrLog . 
			  '</ul>
			</div>
		  </div>
		</nav>';
?>