<?php
    require_once('config.php');
    session_start();
    if (!isset($_SESSION['student-account'])) {
        header('Location: login.php');
        die();
    }
    $name = $_GET['name'];
	$_SESSION['name'] = $name;
	$student = $_SESSION['student-account'];
?>
<!DOCTYPE html>
<html>
<head>
	<title>View</title>
	<meta name="description" content="Listening quiz">
	<meta name="author" content="Mislav Karić">
	<meta charset="UTF-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script>
		function Reset() {
			var con = confirm("Are you sure?");
			if(con != true){
				event.preventDefault();
			}
		}
	</script>
	<style>
		body{
			background-image: url("http://outweb.tvz.hr/leo/wp-content/uploads/2019/02/night_background_cartoon-wallpaper-2560x1440.jpg?_t=1550312228");
		}
		main{
			background-color: white;
			margin: 0 auto;
		}
	</style>
</head>
<body>
<main class="container">
	<button type="button" class="btn btn-primary" onClick="window.close()">Close</button>
	<br/>
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
		<?php
			$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			if (isset($_POST['reset'])) {
				$query = "INSERT INTO score(user, quizid, score) VALUES ('". $_SESSION['student-account'] ."','". $_POST['quizid'] ."','". $_POST['score'] ."');";
				$query .= "DELETE FROM answers WHERE user = '".$student."' AND quizid = '".$_POST['quizid']."';";
				$conn->multi_query($query); 
				while ($conn->next_result()) {;}
				header('Location: student.php');
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

				$ic = 0;
				$iw = 0;
				$in = 0;

				while ($rowQ = $stmtQ->fetch_array(MYSQLI_ASSOC)) {
					$num = $rowQ['num'];
					$qQuestion = $rowQ['question'];
					echo "<div class='userQContainer'>";

					echo "<hr/><strong>" . $num . ". " . $qQuestion . "</strong><br/>";
					$stmtA = $conn->query("SELECT * FROM $choices WHERE questionid = '$num' AND quizid = '$qid' ORDER BY id");
					
					if ($stmtA->num_rows == 0) {
						echo 'No answers in database';
					}
					
					while ($aRow = $stmtA->fetch_array(MYSQLI_ASSOC)) {
						$correct = $aRow['correct'];
						if($aRow['correct'] == '1') ++$in;
						$answer = $aRow['answer'];					
							$statement = $conn->query("SELECT * FROM $answers WHERE user = '$student'");
						while($rows = $statement->fetch_array(MYSQLI_ASSOC)) {
								$guess = $rows['answer'];
								if($guess == $answer && $correct == '1'){
									//Correctly answered
									echo "<div class='custom-control custom-checkbox mb-3'>
									<input type='hidden' name='quizid' value='$qid'>
									<input type='hidden' name='questionid' value='$num'/>
									<input type='checkbox' class='custom-control-input' id='customCheck' name='qAnswer' value='$answer' checked disabled/>
									<label class='custom-control-label text-success' for='customCheck'>$answer</label></div>";
									++$ic;
								}
								else if($answer != $guess && $correct == '1'){
									//Not answered
									//echo "<div class='custom-control custom-checkbox mb-3'>
									//<input type='hidden' name='quizid' value='$qid'>
									//<input type='hidden' name='questionid' value='$num'/>
									//<input type='checkbox' class='custom-control-input' id='customCheck' name='qAnswer' value='$answer' disabled/>
									//<label class='custom-control-label text-warning' for='customCheck'>$answer</label></div>";
								}
								else if($guess == $answer && $correct == '0'){
									//Wrongly answered
									echo "<div class='custom-control custom-checkbox mb-3'>
									<input type='hidden' name='quizid' value='$qid'>
									<input type='hidden' name='questionid' value='$num'/>
									<input type='checkbox' class='custom-control-input' id='customCheck' name='qAnswer' value='$answer' checked disabled/>
									<label class='custom-control-label text-danger' for='customCheck'>$answer</label></div>";
									++$iw;
								}
								else{
									//echo "<div class='custom-control custom-checkbox mb-3'>
									//<input type='hidden' name='quizid' value='$qid'>
									//<input type='hidden' name='questionid' value='$num'/>
									//<input type='checkbox' class='custom-control-input' id='customCheck' name='qAnswer' value='$answer' disabled/>
									//<label class='custom-control-label' for='customCheck'>$answer</label></div>";
								}
							}
						}
						echo "</div>";
					}
					echo '<div class="alert alert-info">';
					echo 'Wrong: <b>'. $iw . '</b><br/>';
					echo 'Correct: <b>' . $ic.'</b>/<b>'. $in . '</b><br/>';
					$score = floor(($ic/$in)*100);
					echo 'Percentage: <b>' . $score . '</b>%<br/>';
					$stmtA->close();
					$stmtQ->close();
					$statement->close();
					$stmt = $conn->query("SELECT * FROM score WHERE user = '".$student."' AND quizid = '".$qid."' ORDER BY id DESC");
					$sum = 0;
					if($stmt->num_rows > 0){
						while($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
							$last_score = $row['score'];
							if($stmt->num_rows > 1)
								$sum = $sum + $last_score;
							else if($stmt->num_rows == 1)
								$sum = $row['score'];
						}
						$average = $sum / $stmt->num_rows;
						if($last_score < $score) echo '<strong>Congratulations</strong>';
						else if($last_score >= $score) echo '<strong>Too bad</strong>';
						echo ', your last score was: <b>' . $last_score . '</b>% and your average is: <b>' . $average . '</b>%.';
						echo '</div>';
					}
					else
						echo 'No previous scores</div>';
					$stmt->close();
					?>
					<br/>
					<form method="post">
						<input type="hidden" name="quizid" value="<?php echo $qid ?>">
						<input type="hidden" name="score" value="<?php echo $score ?>">
						<input id="submit" type="submit" class="btn btn-danger" name="reset" value="Reset" onClick="Reset()">
					</form>
					<?php
						$conn->close();
					?>
	</main>
</body>
</html>