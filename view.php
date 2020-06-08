<?php
    require_once('config.php');
    session_start();
    if (!isset($_SESSION['admin-account'])) {
        header('Location: login.php');
        die();
    }
    $name = $_GET['name'];
    $_SESSION['name'] = $name;
?>
<!DOCTYPE html>
<html>
<head>
	<title>View</title>
	<meta name="description" content="Listening quiz">
	<meta name="author" content="Mislav Karić">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
 	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
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
<main class="container">
	<video controls loop name="media" width="100%" height="60px">
		<source src="<?php
						$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
						if ($conn->connect_error) {
							die("Connection failed: " . $conn->connect_error);
						}
						$stmt = $conn->query("SELECT * FROM quiz WHERE name = '". $_SESSION['name'] ."'");
						while($row = mysqli_fetch_assoc($stmt)){
						  echo $row['audio'];
						}
						$stmt->close();
						$conn->close();
					?>" type="audio/mpeg">
	</video>
	<br/>
		<?php
			$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			$stmt = $conn->query("SELECT * FROM $quiz WHERE name = '$name'");

			$name = $_SESSION['name'];
				if ($stmt->num_rows == 0) {
					$stmt->close();
					$conn->close();
					die();
				}
				
				$row = $stmt->fetch_array(MYSQLI_ASSOC);
				$qid = $row['id'];
				$stmtQ = $conn->query("SELECT * FROM $questions WHERE quizID = '$qid' ORDER BY id");
				if ($stmtQ->num_rows == 0) {
					echo 'No questions in database';
				}

				while ($rowQ = $stmtQ->fetch_array(MYSQLI_ASSOC)) {
					$num = $rowQ['num'];
					echo "<hr/><strong>".$num . ". ";
					$qQuestion = $rowQ['question'];
					echo $qQuestion . "</strong>";
					$stmtA = $conn->query("SELECT * FROM $choices WHERE questionid = '$num' AND quizid = '$qid' ORDER BY id");

					if ($stmtA->num_rows == 0) {
					echo 'No answers in database';
				}
				while ($aRow = $stmtA->fetch_array(MYSQLI_ASSOC)) {
                    $answer = $aRow['answer'];
                    if($aRow['correct'] == '1'){
							echo "<div class='custom-control custom-checkbox mb-3'>
							<input type='hidden' name='quizid' value='$qid'>
							<input type='hidden' name='questionid' value='$num'/>
							<input type='checkbox' class='custom-control-input' id='customCheck' name='qAnswer' value='$answer' checked disabled/>
							<label class='custom-control-label text-success' for='customCheck'>$answer</label></div>";
                    }
                    else{
							echo "<div class='custom-control custom-checkbox mb-3'>
							<input type='hidden' name='quizid' value='$qid'>
							<input type='hidden' name='questionid' value='$num'/>
							<input type='checkbox' class='custom-control-input' id='customCheck' name='qAnswer' value='$answer' disabled/>
							<label class='custom-control-label' for='customCheck'>$answer</label></div>";
                    }
				}
				echo "</div>";
				$stmtA->close();
			}
			$stmtQ->close();
			$conn->close();
			?>
			<button type="button" class="btn btn-primary" onClick="window.close()">Close</button>
	</main>
</body>
</html>