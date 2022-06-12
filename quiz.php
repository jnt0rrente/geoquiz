<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GeoQuiz - Quiz</title>
    <meta name="author" content="Juan Torrente" />
    <meta name="keywords" content="quiz" />
    <meta name="description" content="A quiz on the GeoQuiz site." />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" type="text/css" href="styles/style.css">
    <link rel="stylesheet" type="text/css" href="styles/quiz.css">
    <link rel="stylesheet" type="text/css" href="styles/table.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <h1>
        GeoQuiz
    </h1>
    <nav>
        <a href="index.html" accesskey="w" tabindex="1">Welcome</a>
        <a href="history.html" accesskey="h" tabindex="2">Historical geology</a>
        <a href="mineralogy.html" accesskey="c" tabindex="3">Mineralogy</a>
        <a href="economic.html" accesskey="e" tabindex="4">Economic geology</a>
        <a href="admin.html" accesskey="a" tabindex="5">Administration</a>
        <a href="quiz_list.php" accesskey="q" tabindex="6">Quizzes</a>
    </nav>

    <?php
        session_start();
        require_once("quiz_lib.php");
        require_once("database.php");

        $databaseInterface = new DatabaseInterface();
        $quizManager = new QuizManager($databaseInterface);

        echo "A";

        if (isset($_GET["id"])) {
            if (!isset($_POST["q1"])) {
                echo "B";
                $quizManager->displaySingleQuizSection($_GET["id"]);
            } else {
                echo "C";
                $answers = array();
                foreach ($_POST as $answer) {
                    $answers[] = $answer;
                }
    
                $quizManager->displayResultsForQuiz($_GET["id"], $answers);
            }
        }
    ?>
</body>

</html>