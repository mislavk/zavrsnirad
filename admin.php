<?php
	require_once('config.php');
	session_start();

	if (!isset($_SESSION['admin-account'])) {
		header('Location: login.php');
		die();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Admin</title>
		<meta name="description" content="Listening quiz">
		<meta name="author" content="Mislav Karić">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			function QuizDel() {
				var con = confirm("Are you sure you want to delete a quiz?");
				if(con != true){
					event.preventDefault();
				}
			}
			function AnsDel() {
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
		.container{
			background-color: white;
			margin: 0 auto;
		}
		</style>
	</head>
	<body>
		<main>
			<?php
				if (isset($_SESSION['admin-account'])) {
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
						  echo "<li class='active'><a href='admin.php'>".$_SESSION['admin-name']."</a></li>";
						  echo "<li><a href='admin-account.php'>Account</a></li>";
						  echo "<li><a href='add-quiz.php'>Add a quiz</a></li>";
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
					<li class="active"><a data-toggle="tab" href="#home">Quiz</a></li>
					<li><a data-toggle="tab" href="#users">Users</a></li>
				</ul>

				<div class="tab-content">

					<div id="home" class="tab-pane fade in active">
					<?php
						$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
						if ($conn->connect_error) {
							die("Connection failed: " . $conn->connect_error);
						}
						if (isset($_POST['deletequiz'])) {	
							$statement = "DELETE FROM answers WHERE quizid = '".$_POST['qid']."';";
							$statement .= "DELETE FROM choices WHERE quizid = '".$_POST['qid']."';";
							$statement .= "DELETE FROM questions WHERE quizID = '".$_POST['qid']."';";
							$statement .= "DELETE FROM score WHERE quizid = '".$_POST['qid']."';";
							$statement .= "DELETE FROM quiz WHERE id = '".$_POST['qid']."';";
							$conn->multi_query($statement); 
							while ($conn->next_result()) {;}
						}
						$stmt = $conn->query("SELECT * FROM quiz ORDER BY id");
						$statement = $conn->query("SELECT dialect.dialect_name, style.style_name FROM quiz JOIN dialect ON quiz.dialect_id = dialect.id JOIN style ON quiz.style_id = style.id");
					?>
					<table class="table table-bordered table-hover">
						<tr><th>Quiz name</th><th>Access code</th><th>Type</th><th>Details</th><th>Delete</th></tr>
						<?php
							while($row = mysqli_fetch_array($stmt)) {
								$name = $row['name'];
								$rows = mysqli_fetch_array($statement);
								$type = $rows['dialect_name'].' - '.$rows['style_name'];
								$accessCode = $row['accessCode'];
						?>
								<tr>
								<td style="word-break:break-all;"><?php echo $name; ?></td>
								<td style="word-break:break-all;"><?php echo $accessCode; ?></td>
								<td style="word-break:break-all;"><?php echo $type; ?></td>
								<td style="word-break:break-all;">
										<a href="view.php?name=<?php echo $name; ?>" target=”_blank” class="btn btn-primary">View</a>
									</td>
									<td style="word-break:break-all;">
									<form method="post">
										<?php
										echo '<input type="hidden" name="qid" value="' . $row['id'] . '">';
										echo '<input id="submit" type="submit" name="deletequiz" class="btn btn-danger" value="Delete Quiz" onclick="QuizDel()">';
										?>
									</form>
									</td>
								</tr>
						<?php
							}
							$statement->close();
							$stmt->close();
						?>
					</table>
					</div>
					<div id="users" class="tab-pane fade">
						<?php
							$stmt = $conn->query('SELECT * FROM users WHERE status < 2');
							while($row = mysqli_fetch_array($stmt)) {
								echo '<table class="table table-bordered table-hover"><form method="post">';
								echo '<th>' . $row['name'] . ' ' . $row['surname'] . ' (' . $row['username'] . ')</th>';
								$query = $conn->query("SELECT * FROM quiz ORDER BY id");
								while($row1 = mysqli_fetch_array($query)) {
									$id = $row1['id'];
									$name = $row1['name'];
									$statement = $conn->query('SELECT * FROM answers');
									while($row2 = mysqli_fetch_array($statement)) {
										if($row2['user'] == $row['username'] && $row2['quizid'] == $id){
											echo '<tr><td><a href="admin-view.php?name='.$name.'&student='.$row['username'].'" target=”_blank”>'. $row1['name'] .'</a></td></tr>';
											echo '<input type="hidden" name="id" value="' . $row['username'] . '">';
											echo '<input type="hidden" name="quiz" value="' . $row1['id'] . '">';
											echo '<tr><td><input id="del" type="submit" name="del" class="btn btn-danger" value="Delete" onClick="AnsDel()"></td></tr>';
											break;
										}
									}	
								}
								echo '</form></table>';
							}

							if (isset( $_POST['del'])) {
								$statement = $conn->query("DELETE FROM answers WHERE user = '".$_POST['id']."' AND quizid = '".$_POST['quiz']."'") or die(mysql_error());
								header('Location: admin.php');
								$statement->close();
							}
							$query->close();
							$stmt->close();
							$conn->close();
						?>
					</div>
				</div>
			</div>
		</main>
	</body>
</html>