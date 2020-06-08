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
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
						  echo "<li><a href='admin.php'>".$_SESSION['admin-name']."</a></li>";
						  echo "<li><a href='admin-account.php'>Account</a></li>";
						  echo "<li class='active'><a href='add-quiz.php'>Add a quiz</a></li>";
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
				<li class="active"><a data-toggle="tab" href="#quiz">Create quiz</a></li>
				<li><a data-toggle="tab" href="#mod">Style/Dialect</a></li>
			</ul>
			<div class="tab-content">
				<div id="quiz" class="tab-pane fade in active">
					<form id="quizForm" action="quiz-creation.php" method="POST">
						<div class="form-group">
							<label for="quizName">Quiz name</label>
							<input type="text" name="quizname" class="form-control" placeholder="Enter quiz name" id="quizName" required />
						</div>
						<div class="form-group">
							<label for="accessCode">Access code</label>
							<input type="text" id="accessCode" class="form-control" placeholder="Enter access code" name="accessCode" required/>
						</div>
						<div class="form-group">
							<label for="style">Style</label>
							<select name="style" class="form-control" id="style" required>
								<option value="" disabled selected> </option>
								<?php
									$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
									if ($conn->connect_error) {
										die("Connection failed: " . $conn->connect_error);
									}
									$stmt = $conn->query("SELECT * FROM style");
									while($row = mysqli_fetch_array($stmt)) {
										echo '<option value="'.$row['id'].'">' . $row['style_name'] . '</option>';
									}
									$stmt->close();
								?>
							</select>
						</div>
						<div class="form-group">
							<label for="dialect">Dialect</label>
							<select name="dialect" class="form-control" id="dialect" required>
								<option value="" disabled selected> </option>
								<?php
									$stmt = $conn->query("SELECT * FROM dialect");
									while($row = mysqli_fetch_array($stmt)) {
										echo '<option value="'.$row['id'].'">' . $row['dialect_name'] . '</option>';
									}
									$stmt->close();
								?>
							</select>
						</div>
						<div class="form-group">
							<label for="audio">Audio link</label>
							<input type="text" class="form-control" placeholder="Enter audio link" name="audio" id="audio" required />
						</div>
						<hr/>
						<div class="form-group">
							<div id="questionsContainer"></div><br/>
							<button type="button" id="addQuestionBtn" class="btn btn-default">Add question</button>
						</div>
						<input type="submit" value="Submit" class="btn btn-primary">
					</form>
					<script src="add-quiz.js"></script>
				</div>
				<div id="mod" class="tab-pane fade">
					<form method="POST">
						<div class="form-group">
							<label for="addstylefield">New style name</label>
							<input type="text" name="addstylefield" class="form-control" placeholder="Enter new style" id="addstylefield">
						</div>
						<div class="form-group">
							<label for="adddialectfield">New dialect name</label>
							<input type="text" name="adddialectfield" class="form-control" placeholder="Enter new dialect" id="adddialectfield">
						</div>
						<input type="submit" name="submit2" class="btn btn-primary" value="Apply">
						<?php
							echo '<div class="form-group">';
								echo '<label for="style">Choose style to delete</label>';
								echo '<select name="delstyle" class="form-control" id="style">';
									echo '<option value="" disabled selected> </option>';
									$stmt = $conn->query("SELECT * FROM style");
									while($row = mysqli_fetch_array($stmt)) {
										echo '<option value="'.$row['id'].'">' . $row['style_name'] . '</option>';
									}
									$stmt->close();
								echo '</select>';
							echo '</div>';
							echo '<input type="submit" class="btn btn-danger" value="Delete Style" name="delstylebutton" onClick="StyleDel()">';
							echo '<div class="form-group">';
								echo '<label for="dialect">Choose dialect to delete</label>';
								echo '<select name="deldialect" class="form-control" id="dialect">';
									echo '<option value="" disabled selected> </option>';
									$stmt = $conn->query("SELECT * FROM dialect");
									while($row = mysqli_fetch_array($stmt)) {
										echo '<option value="'.$row['id'].'">' . $row['dialect_name'] . '</option>';
									}
									$stmt->close();
								echo '</select>';
							echo '</div>';
							echo '<input type="submit" class="btn btn-danger" value="Delete Dialect" name="deldialectbutton" onClick="DialectDel()">';
							$conn->close();
						?>
					</form>
					<?php
						$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
						if ($conn->connect_error) {
							die("Connection failed: " . $conn->connect_error);
						}
						if(isset($_POST['submit2'])){
							if($_POST['addstylefield'] != ''){
								$stmt = $conn->query("INSERT INTO style (style_name) VALUES ('".$_POST['addstylefield']."')");
							}
							if($_POST['adddialectfield'] != ''){
								$stmt = $conn->query("INSERT INTO dialect (dialect_name) VALUES ('".$_POST['adddialectfield']."')");
							}
						}
						if(isset($_POST['delstylebutton'])){
							$stmt = $conn->query("DELETE FROM style WHERE id = '".$_POST["delstyle"]."'") or die(mysql_error());
						}
						if(isset($_POST['deldialectbutton'])){
							$stmt = $conn->query("DELETE FROM dialect WHERE id = '".$_POST["deldialect"]."'") or die(mysql_error());
						}	
						$conn->close();
					?>
				</div>
</body>
</html>
<script>
	function StyleDel() {
				var con = confirm("Are you sure you want to delete that style?");
				if(con != true){
					event.preventDefault();
				}
			}
	function DialectDel() {
		var con = confirm("Are you sure you want to delete that dialect?");
		if(con != true){
			event.preventDefault();
		}
	}
</script>