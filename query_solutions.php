<?php

require_once("database.php");
require_once("quiz_lib.php");

$db = new DatabaseInterface();
$qm = new QuizManager($db);

if (isset($_POST["id"])) {
    $answerArray = $qm->getSolutionsForQuiz($_POST["id"]);

    echo json_encode($answerArray);
}


?>