<?php
    require_once('config.php');
    session_start();
    if (!isset($_SESSION['student-account'])) {
        header('Location: login.php');
        die();
    }
	if (!isset($_SESSION['name'])) {
        header('Location: student.php');
        die();
    }
	header('Location: student.php');
	$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    foreach($_POST['qAnswer'] as $answer){
        echo $qid = strstr($answer, '.' , true);
        echo $ans = substr(strstr($answer, '.'), 1);
        $stmt = $conn->query("INSERT INTO answers (questionID, answer, USER, quizid) VALUES ('$qid','$ans','".$_SESSION['student-account']."','".$_POST['quizid']."')");	
    }
	$conn->close();
?>