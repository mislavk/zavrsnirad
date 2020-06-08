<?php
    require_once('config.php');
    session_start();
    
	if(!isset($_SESSION["registration-fail"])) {
		$_SESSION["registration-fail"] = "";
		header('Location: registration.php');
        die();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Registration</title>
	<meta name="description" content="Listening quiz">
	<meta name="author" content="Mislav Karić">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<style>
		body{
			background-image: url("http://outweb.tvz.hr/leo/wp-content/uploads/2019/02/night_background_cartoon-wallpaper-2560x1440.jpg?_t=1550312228");
		}
		.container{
			background-color: white;
			margin: 0 auto;
		}
		</style>
</head>
<body>
	<main class="container col-md-3">
	<div id="error"><?php echo $_SESSION["registration-fail"]; session_unset(); ?></div>
		<form name="registration" action="registration-action.php" method="POST">
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text" class="form-control" id="username" name="username" required />
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" class="form-control" id="password" name="password" required />
			</div>
			<div class="form-group">
				<label for="name">Name</label>
				<input type="text" class="form-control" id="name" name="name" required />
			</div>
			<div class="form-group">
				<label for="surname">Surname</label>
				<input type="text" class="form-control" id="surname" name="surname" required />
			</div>
			<input type="submit" class="btn btn-primary btn-block" name="submit" value="Register" id="submit"/>
		</form>
		<div id="warning">
			<p id="condition"></p>
			<p id="letter" class="invalid"></p>
			<p id="number" class="invalid"></p>
			<p id="length" class="invalid"></p>
		</div>
		<br/>
		<a href="index.php"> Back to login </a>
	</main>
</body>
</html>

<script>				
	var input = document.getElementById("password");
	var letter = document.getElementById("letter");
	var number = document.getElementById("number");
	var length = document.getElementById("length");


	input.onfocus = function() {
		document.getElementById("warning").style.display = "block";
	}


	input.onblur = function() {
		document.getElementById("warning").style.display = "none";
	}


	input.onkeyup = function() {

	  var lowerCaseLetters = /[a-z]/g;
	  if(input.value.match(lowerCaseLetters)) {
		document.getElementById("submit").disabled = false;
		document.getElementById("letter").style.display = "none";
	  } else {
		document.getElementById("submit").disabled = true;
		document.getElementById("letter").innerHTML = "A lowercase letter";
		document.getElementById('letter').style.color = 'red';
		document.getElementById("letter").style.display = "block";
	  }
	  
	  var numbers = /[0-9]/g;
	  if(input.value.match(numbers)) {  
		document.getElementById("submit").disabled = false;
		document.getElementById("number").style.display = "none";
	  } else {
		document.getElementById("submit").disabled = true;
		document.getElementById("number").innerHTML = "A number";
		document.getElementById('number').style.color = 'red';
		document.getElementById("number").style.display = "block";
	  }
	  
	  if(input.value.length >= 8) {
		document.getElementById("submit").disabled = false;
		document.getElementById("length").style.display = "none";
	  } else {
		document.getElementById("submit").disabled = true;
		document.getElementById("length").innerHTML = "Minimum 8 characters";
		document.getElementById('length').style.color = 'red';
		document.getElementById("length").style.display = "block";
	  }
		if(input.value.match(lowerCaseLetters) && input.value.match(numbers) && input.value.length >= 8){
			document.getElementById("condition").innerHTML = "<strong>Password meets all conditions.</strong>";
			document.getElementById('condition').style.color = 'green';
			document.getElementById("warning").className="alert alert-success";
		}
		else{
			document.getElementById("condition").innerHTML = "<strong>Password must contain:</strong>";
			document.getElementById('condition').style.color = 'red';
			document.getElementById("warning").className="alert alert-danger";
		}
	}
</script>