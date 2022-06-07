<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GeoQuiz - Quiz</title>
    <meta name="author" content="Juan Torrente" />
    <meta name="keywords" content="quiz" />
    <meta name="description" content="A quiz on the GeoQuiz site." />
    <meta name="viewport" content="width=device-width, initial scale=1.0" />

    <link rel="stylesheet" type="text/css" href="styles/style.css">
    <link rel="stylesheet" type="text/css" href="styles/single_quiz.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts/quiz.js"></script>
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
        require_once("quiz_lib.php");
        require_once("database.php");

        $databaseInterface = new DatabaseInterface();
        $quizManager = new QuizManager($databaseInterface);

        $quizManager->displaySingleQuizSection($_GET["id"]);

        echo "<input type=button value='Solve quiz' onclick='sqp.solve( " . $_GET["id"] . " )' />";
    ?>
</body>

</html>