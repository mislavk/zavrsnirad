<?php
    require_once('config.php');
    session_start();
    
    if (!isset($_SESSION['admin-account'])) {
        header('Location: login.php');
    }
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    header('Content-Type: application/json');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $quizData = json_decode(file_get_contents('php://input'));
    $data = $quizData->questions;
    $pStmt = $conn->prepare("INSERT INTO quiz (name, accesscode, style_id, dialect_id, audio) VALUES (?,?,?,?,?)");
    $pStmt->bind_param("sssss", $quizData->name, $quizData->code, $quizData->style, $quizData->dialect, $quizData->audio);
    $pStmt->Execute();
    $quizPid = $conn->insert_id;
    $pStmt->Close();

    foreach($quizData->questions as $key=>$value){
		$pid = $conn->insert_id;
        $qStmt = $conn->prepare("INSERT INTO questions (question, quizID, num) VALUES (?,?,?)");
		$questionNum = $key+1;
        $qStmt->bind_param("sii", $value->desc, $quizPid, $questionNum);
        $qStmt->Execute();
		//$qid = $conn->insert_id;
        foreach($value->answers as $answer_key=>$answer_value){
			$answerNum = $answer_key + 1;
            if($answer_value->checked){
                $correct = 1;
                $aStmt = $conn->prepare("INSERT INTO choices (questionid,answer,correct,quizid) VALUES (?,?,?,?)");
                $aStmt->bind_param("isii", $questionNum, $answer_value->desc, $correct, $quizPid);
            }
            else{
                $correct = 0;
                $aStmt = $conn->prepare("INSERT INTO choices (questionid,answer,correct,quizid) VALUES (?,?,?,?)");
                $aStmt->bind_param("isii", $questionNum, $answer_value->desc, $correct, $quizPid);
            }
			$aStmt->execute();
        }
    }
    $qStmt->close();
    $conn->close();
    print json_encode(array("status"=>"DONE"));
?>