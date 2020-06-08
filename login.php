<?php
	/* Spajanje na bazu */
    require_once('config.php');
    session_start();
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
	
    /* Provjera korisničkog imena */
    $username = $_POST['username'];
    $stmt = $conn->query("SELECT * FROM users WHERE username = '$username'");
    
    if ($stmt->num_rows == 0) {
        $_SESSION['login-fail'] = "Invalid username";
		header('Location: index.php');
		$stmt->close();
        return;
    }

    /* Provjera lozinke */
    $password = $_POST['password'];
    $stmt = $conn->query("SELECT * FROM users WHERE username = '$username' AND password = sha2('$password',256)");
    if ($stmt->num_rows == 0) {
		$_SESSION['login-fail'] = "Invalid password";
		header('Location: index.php');
        die();
		$stmt->close();
		$conn->close();
    }
	
    /* Provjera statusa korisnika */
    $row = mysqli_fetch_array($stmt);
	$fullname = $row['name'] . " " . $row['surname'];
    if ($row['status'] > 1) {
		$_SESSION['admin-account'] = $username;
		$_SESSION['admin-name'] = $fullname;
	    header('Location: admin.php');
    }
    else {
		$_SESSION['student-account'] = $username;
		$_SESSION['student-name'] = $fullname;
	    header('Location: student.php');
    }
    $stmt->close();
    $conn->close();	
?>