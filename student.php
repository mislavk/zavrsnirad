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
	<meta name="description" content="Listening quiz">
	<meta name="author" content="Mislav Karić">
	<meta charset="UTF-8">
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
		.card {
			box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
			transition: 0.3s;
			padding: 5% 10% 5% 10%;
			margin: 5% 10% 5% 10%;
		}

		.card:hover {
			box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
		}
		</style>
</head>
<body>
	<main>
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
						  echo "<li class='active'><a href='student.php'>".$_SESSION['student-name']."</a></li>";
						  echo "<li><a href='account.php'>Account</a></li>";
						  echo "<li><a href='logout.php'>Logout</a></li>";
						echo '</ul>';
						echo '</div>';
					 echo '</div>';
					echo '</div>';
				}
		?>
		<div class="clearfix"></div>
		<div class="container">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#about">Listening</a></li>
				<li><a data-toggle="tab" href="#home">Choose a quiz</a></li>
				<li><a data-toggle="tab" href="#analysis">Quiz analysis</a></li>
			</ul>
			<div class="tab-content">
				<div id="about" class="tab-pane fade in active">
					<div class="card">
						<p>"Listening is yet another necessitate in language. The more efficient a listener you are… the more successful and satisfied
						you will be. Listening…is not merely hearing: it is a state of receptivity that permits understanding of what is heard and
						grants the listener full partnership in the communication process. We need to develop a keen interest in making ourselves
						better ears. The fact that we listen more than our ears and we listen far more than the sound is very true. As any member
						in a society, listening is one important skill to possess as good listening is an integral part of communication process. A
						good listener shows readiness and possesses an ability to manipulate the sound into words and their contextual meaning.
						Then the good listener relates given meanings to other experiences and he shares responsibility with the speaker.
						Academically, listening skills plays a vital role in the teaching-learning cycle. A student learns better when he can listen
						better. A teacher is also in need of a good listening skill. All the way, listening should be enhanced in your life as to be a
						greater speaker. The attitude of the listener is another stepping stone to achieve this skill."</p>
						<p>Lorena Manaj Sadiku, 2015., European Journal of Language and Literature Studies</p>
						<p>ISSN 2411-4103</p>
					</div>
				</div>
				<div id="home" class="tab-pane fade">
					<?php 
					$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

					if ($conn->connect_error) {
						die("Connection failed: " . $conn->connect_error);
					}

					if (isset($_POST['name'])) {
						$name = $_POST['name'];
						$accessCode = $_POST['code'];
						//$stmt = $conn->query("SELECT * FROM $quiz WHERE name = '$name' AND accessCode = '$accessCode'");
						
						//if ($stmt->num_rows != 0) {
							$_SESSION['name'] = $name;
							header('Location:questions.php');
							//$stmt->close();
							$conn->close();
							//die();
						//}
						//else {
							//echo "<p>Invalid access code</p>";
						//}
						//$stmt->close();
					}

					$stmt = $conn->query("SELECT * FROM $quiz ORDER BY id");

					if ($stmt->num_rows == 0) {
						$stmt->close();
						$conn->close();
						return;
					}
					?>
					<table class="table table-bordered table-hover">
					<!-- <tr><th>Quiz</th><th>Type</th><th>Code</th><th></th></tr> -->
					<tr><th>Choose a quiz</th><th>Type</th><th></th></tr>
					<?php
					$statement = $conn->query("SELECT dialect.dialect_name, style.style_name FROM quiz JOIN dialect ON quiz.dialect_id = dialect.id JOIN style ON quiz.style_id = style.id");
					while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
					$id = $row['id'];
					$name = $row['name'];
					$rows = mysqli_fetch_array($statement);
					$type = $rows['dialect_name'].' - '.$rows['style_name'];
					?>
					<form method='post' action='student.php'>
						<input type='hidden' name='name' value='<?php echo $name; ?>' />
						<tr>
						<td><?php echo $name; ?></td>
						<td><?php echo $type; ?></td>
						<!-- <td><input type='text' class="form-control" name='code'/></td> -->
						<td>
						<?php
						$query = $conn->query("SELECT * FROM answers WHERE quizid = '".$id."' AND user = '".$_SESSION['student-account']."'");
						if($query->num_rows == 0){
							echo "<input type='submit' class='btn btn-primary' name='submit' value='Start'/>";
						}
						else{
							echo "<input type='submit' class='btn btn-primary' name='submit' value='Done' disabled/>";
							}
						?>
						</td>
						</tr>
					</form>
					<?php
					}
					$statement->close();
					$query->close();
					$stmt->close();
					?>
					</table>
				</div>
				<div id="analysis" class="tab-pane fade">
				<h3> Success analysis can be checked in this tab. </h3>
					<?php
						$stmt = $conn->query('SELECT * FROM users WHERE username ="'. $_SESSION["student-account"].'"');
						while($row = mysqli_fetch_array($stmt)) {
							echo '<table class="table table-bordered table-hover">';
							echo '<th>' . $row['name'] . ' ' . $row['surname'] . ' (' . $row['username'] . ')</th>';
							$query = $conn->query("SELECT * FROM quiz ORDER BY id");
							while($rows = mysqli_fetch_array($query)) {
								$id = $rows['id'];
								$name = $rows['name'];
								$statement = $conn->query('SELECT * FROM answers');
								while($rowss = mysqli_fetch_array($statement)) {
									if($rowss['user'] == $row['username'] && $rowss['quizid'] == $id){
										echo '<tr><td><a href="student-view.php?name='.$name.'" target=”_blank”>'. $rows['name'] .'</a></td></tr>';
										break;
									}
									}	
							}
							echo '</table>';
						}
						$query->close();
						$stmt->close();
						$conn->close();
					?>
				</div>
			</div>
	</main>
</body>
</html>