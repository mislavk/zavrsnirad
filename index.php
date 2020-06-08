<?php
	//Provjera postoji li poruka greške prijave
    require_once('config.php');
    session_start();

	if(!isset($_SESSION["login-fail"])) {
		$_SESSION["login-fail"] = "";
		header('Location: index.php');
        die();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Index</title>
	<meta name="description" content="Listening quiz"/>
	<meta name="author" content="Mislav Karić"/>
	<meta charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<style>
		body{
			background-image: url("http://outweb.tvz.hr/leo/wp-content/uploads/2019/02/night_background_cartoon-wallpaper-2560x1440.jpg?_t=1550312228");
		}
	</style>
</head>
<body>
<div class="container theme-showcase" role="main">
	<div class="jumbotron">
		<div id="error"><?php echo $_SESSION["login-fail"]; session_unset(); ?></div>
		<form action="login.php" method="post">
			<div class="input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
				<input type="text" class="form-control" name="username" placeholder="Username" required>
			</div>
			<div class="input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
				<input type="password" class="form-control" name="password" placeholder="password" required>
			</div>
				<input type="submit" class="btn btn-primary btn-block" name="submit" value="Log In" id="submit" />
		</form>
		<a href="registration.php">Create an account</a>
	</div>
</div>
</body>
</html>