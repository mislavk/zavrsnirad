<?php
    require_once('config.php');
    session_start();
    
    if (!isset($_SESSION['admin-account'])) {
        header('Location: login.php');
        die();
    }
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
	if(isset ($_POST['status'])){
		if($_POST['status'] == 'Promote'){
			$stmt = $conn->query("UPDATE users SET status = '2' WHERE username = '".$_POST["id"]."'");
			header('Location: admin-account.php');}
		else if($_POST['status'] == 'Demote'){
			$stmt = $conn->query("UPDATE users SET status = '1' WHERE username = '".$_POST["id"]."'");
			header('Location: admin-account.php');
		}
	}
	if (isset($_POST['deluser'])) {
		$query = "DELETE FROM answers WHERE user = '".$_POST["id"]."';";
		$query .= "DELETE FROM score WHERE user = '".$_POST["id"]."';";
		$query .= "DELETE FROM users WHERE username = '".$_POST["id"]."';";
		$conn->multi_query($query);
		while ($conn->next_result()) {;}
		header('Location: admin-account.php');
	}
	if (isset($_POST['beuser'])) {
		$stmt = $conn->query("SELECT * FROM users WHERE username = '".$_POST["id"]."'");
		$row = $stmt->fetch_array(MYSQLI_ASSOC);
		$fullname = $row['name'] . " " . $row['surname'];
		unset($_SESSION['admin-account']);
		if($row['status'] == 1){
			$_SESSION['student-account'] = $_POST["id"];
			$_SESSION['student-name'] = $fullname;
			header('Location: student.php');
		}
		else if($row['status'] == 2){
			$_SESSION['admin-account'] = $_POST["id"];
			$_SESSION['admin-name'] = $fullname;
			header('Location: admin.php');
		}
	}
	$stmt->close();
	$conn->close();
?>