<?php
	require_once('config.php');
	session_start();
	
	if (!isset($_SESSION['student-account'])) {
        header('Location: login.php');
        die();
    }
	
	$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
		
	if (isset($_SESSION['student-account'])){
		$username = $_SESSION['student-account'];
		if ($_POST['name'] != ''){
			$name = $_POST["name"];
			$stmt = $conn->query("UPDATE users SET name = '$name' WHERE username = '$username'");
			header('Location: account.php');
		}
		if($_POST['surname'] != ''){
			$surname = $_POST["surname"];
			$stmt = $conn->query("UPDATE users SET surname = '$surname' WHERE username = '$username'");
			header('Location: account.php');
		}
		if($_POST['password'] != '' && $_POST['confirm_password'] != ''){
			if($_POST['password'] == $_POST['confirm_password']){
				$password = $_POST["password"];
				$secured = hash('sha256', $password);
				$stmt = $conn->query("UPDATE users SET password = '$secured' WHERE username = '$username'");
				header('Location: account.php');
			}
		}
	}
	$conn->close();
?>