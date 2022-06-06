<?php

require_once("database.php");
require_once("quiz_lib.php");

$db = new DatabaseInterface();
$qm = new QuizManager($db);

$qm->getSolutionsForQuiz()

?>