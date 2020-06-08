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
?>
<!DOCTYPE html>
<html>
<head>
	<title>Student</title>
	<meta name="description" content="Listening quiz">
	<meta name="author" content="Mislav Karić">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
 	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</head>
<body>
<main class="container">
    <div>Quiz ends in
      <span id="time">30:00</span>
    </div>
	<script>
		function startTimer(duration, display) {
		var timer = duration, minutes, seconds;
		setInterval(function () {
		minutes = parseInt(timer / 60, 10)
		seconds = parseInt(timer % 60, 10);

		minutes = minutes < 10 ? "0" + minutes : minutes;
		seconds = seconds < 10 ? "0" + seconds : seconds;

		display.textContent = minutes + ":" + seconds;

		if (--timer < 0) {
			document.getElementById("automatic_submit").submit();
		}
		}, 1000);
		}

		window.onload = function () {
		var Minutes = 60 * 30,
		display = document.querySelector('#time');
		startTimer(Minutes, display);
		};
	</script>
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
	</div>
		<form action="answers.php" method="post" id="automatic_submit">
		<?php
			$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			$name = $_SESSION['name'];
			$stmt = $conn->query("SELECT * FROM $quiz WHERE name = '$name'");

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
					$qQuestion = $rowQ['question'];
					echo "<div class='userQContainer'>";
					echo "<hr/><strong>" . $num . ". " . $qQuestion . "</strong>";
					$stmtA = $conn->query("SELECT * FROM $choices WHERE questionid = '$num' AND quizid = '$qid' ORDER BY id");
					
					if ($stmtA->num_rows == 0) {
					echo 'No answers in database';
				}
				while ($aRow = $stmtA->fetch_array(MYSQLI_ASSOC)) {
					$answer = $aRow['answer'];
					echo '<div><input type="hidden" name="quizid" value="'.$qid.'">
					<input type="hidden" name="questionid" value="'.$num.'"/>
					<input type="checkbox" id="check" name="qAnswer[]" value="'. $num .'.'. $answer .'"/>
					<label class="question">'.$answer.'</label></div>';
				}
				$stmtA->close();
				echo "</div>";
			}
			$stmtQ->close();
			?>
			<input type="submit" class="btn btn-primary" value="End Quiz"/>
		</form>
	</main>
</body>
</html>
<style>
	#check:checked + .question {
		text-align: center;
		color: #5BC0DE;
		font-weight: bold;
		transform: rotateY(360deg);
		transition: 0.5s;
	}
	body{
		background-image: url("http://outweb.tvz.hr/leo/wp-content/uploads/2019/02/night_background_cartoon-wallpaper-2560x1440.jpg?_t=1550312228");
	}
	.container{
		background-color: white;

		margin: 0 auto;
	}
	#time{
		color: red;
		font-size: 200%;
	}
</style>