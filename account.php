<?php
    require_once('config.php');
    session_start();
    
    if (!isset($_SESSION['student-account'])) {
        header('Location: login.php');
        die();
    }
    
?>
<!DOCTYPE html>
<html>
<head>
	<title>Student</title>
	<meta name="description" content="Listening quiz"/>
	<meta name="author" content="Mislav Karić"/>
	<meta charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
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
		<?php
				if (isset($_SESSION['student-account'])) {
					echo '<div class="navbar navbar-default navbar-static-top" role="navigation">';
					echo '<div class="container">';
					 echo '<div class="navbar-header">';
						 echo '<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">';
						 echo '<span class="sr-only">Toggle navigation</span>';
							 echo '<span class="icon-bar"></span>';
							 echo '<span class="icon-bar"></span>';
							 echo '<span class="icon-bar"></span>';
						 echo '</button>';
						echo '</div>';
						echo '<div class="collapse navbar-collapse">';
						echo '<ul class="nav navbar-nav navbar-left">';
						  echo "<li><a href='student.php'>".$_SESSION['student-name']."</a></li>";
						  echo "<li class='active'><a href='account.php'>Account</a></li>";
						  echo "<li><a href='logout.php'>Logout</a></li>";
						echo '</ul>';
						echo '</div>';
					 echo '</div>';
					echo '</div>';
				}
		?>
		<main class="container">
		<?php
			$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			$stmt = $conn->query('SELECT * FROM users WHERE username="'.$_SESSION["student-account"].'"');
			if (!$stmt) {
				printf("Error: %s\n", mysqli_error($conn));
				exit();
            }
			while($row = mysqli_fetch_array($stmt)) {
				echo '<table class="table table-bordered table-hover">';
				echo "<tr><td> Username </td><td>". $row["username"]. "</td></tr>";
				echo "<tr><td> Name </td><td>". $row["name"]. " " . $row["surname"]. "</td></tr>";
				if($row['status'] > 1) echo '<tr><td>Status</td><td> Administrator </td></tr>';
				else if($row['status'] == 1) echo '<tr><td>Status</td><td> Student </td></tr>';
			}
			$stmt->close();
			$conn->close();

		?>
		<form action="update-account2.php" method="post"/>
			<tr>
				<td><label for="name">Change name</label></td>
				<td><input type="text" class="form-control" name="name" /></td>
			</tr>
			<tr>
				<td><label for="surname">Change surname</label></td>
				<td><input type="text" class="form-control" name="surname" /></td>
			</tr>
			<tr>
				<td><label for="password">Change password</label></td>
				<td><input type="password" class="form-control" name="password" id="password" onkeyup='check();' /></td>
			</tr>
			<tr>
				<td><label for="confirm_password">Confirm password</label></td>
				<td><input type="password" class="form-control" name="confirm_password" id="confirm_password" onkeyup='check();' /></td>
			<tr>
			</tr>
				<td><input type="submit" class="btn btn-primary btn-block" id="change" name="submit" value="Apply" /></td>
				<td><span id='message'></span></td>
			</tr>
			</table>
		</form>
	</main>
</body>
</html>
<script>
	var check = function() {
	if (document.getElementById('password').value == document.getElementById('confirm_password').value) {
		document.getElementById('message').style.color = 'green';
		document.getElementById('message').innerHTML = 'Passwords match';
		document.getElementById("change").disabled = false;
	} else if (document.getElementById('password').value != document.getElementById('confirm_password').value) {
		document.getElementById('message').style.color = 'red';
		document.getElementById('message').innerHTML = "Passwords don't match";
		document.getElementById("change").disabled = true;
	}
	var letters = /[a-z]/g;
	  if(document.getElementById('password').value.match(letters)) {  
		document.getElementById("change").disabled = false;
	  } else {
		document.getElementById('message').style.color = 'red';
		document.getElementById('message').innerHTML = "Password must contain a letter";
		document.getElementById("change").disabled = true;
	  }
	  
	  var numbers = /[0-9]/g;
	  if(document.getElementById('password').value.match(numbers)) {  
		document.getElementById("change").disabled = false;
	  } else {
		document.getElementById('message').style.color = 'red';
		document.getElementById('message').innerHTML = "Password must contain a number";
		document.getElementById("change").disabled = true;
	  }
	  
	  if(document.getElementById('password').value.length >= 8) {
		document.getElementById("change").disabled = false;
	  } else {
		document.getElementById('message').style.color = 'red';
		document.getElementById('message').innerHTML = "Password must contain 8 characters";
		document.getElementById("change").disabled = true;
	  }
	}
</script>