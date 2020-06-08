<!DOCTYPE html>
<html>
	<head>
		<title> Registration </title>
		<meta charset="UTF-8">
	</head>
	<body>
		<?php
			/* Spajanje na bazu */
			require_once('config.php');
			session_start();
			$conn= new mysqli($dbhost,$dbuser,$dbpass, $dbname);
			if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
			}		
			
			$username = $_POST['username'];
			$password = $_POST['password'];
			$name = $_POST['name'];
			$surname = $_POST['surname'];
			$submit = $_POST['submit'];

			$secured = hash('sha256', $password);
			
			/* Provjera postoji li korisnik i upis u bazu */
			$stmt = $conn->query("SELECT * FROM $users WHERE username= '$username'");
			if($stmt->num_rows > 0){
						$_SESSION['registration-fail'] = "Username already exists";
						header('Location: registration.php');
			}
			else{
				$sql = "INSERT INTO users(`username`, `password`, `status`, `name`, `surname`) VALUES('$username', '$secured', '1', '$name', '$surname');";
			}
			$stmt->close();
			
			/* Korisnički račun uspješno kreiran */
			if ($conn->multi_query($sql) === TRUE) {
			echo '<p id="welcome"> Welcome '.$username.'</p>';
			header('Refresh: 5; URL=index.php');
			echo '<p id="redirect">You will be redirected in 5 seconds.</p>';
			}
			$conn->close();
		?>
	</body>
</html>