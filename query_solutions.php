<?php

require_once("database.php");
require_once("quiz_lib.php");

$db = new DatabaseInterface();
$qm = new QuizManager($db);

$id = json_decode(file_get_contents("php://input"), false)->id;
if (isset($id)) {
    $answerArray = $qm->getSolutionsForQuiz($_POST["id"]);

    echo json_encode($answerArray);
} else {
    echo "No ID queried.\n";
    echo var_dump(json_decode(file_get_contents("php://input"), false));
}


?>