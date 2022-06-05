<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GeoQuiz - Quiz</title>
    <meta name="author" content="Juan Torrente" />
    <meta name="keywords" content="admin, admin panel, upload">
    <meta name="description" content="Administration tab for the GeoQuiz site." />
    <meta name="viewport" content="width=device-width, initial scale=1.0">

    <link rel="stylesheet" type="text/css" href="style.css">

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
        <a href="admin.php" accesskey="a" tabindex="5">Administration</a>
        <a href="quizzes.php" accesskey="q" tabindex="6">Quizzes</a>
    </nav>

    <?php
        require_once("quiz_list.php");
        require_once("database.php");

        $databaseInterface = new DatabaseInterface();
        $quizManager = new QuizManager($databaseInterface);

        $quizManager->displaySingleQuizPage($_GET["id"]);
    ?>
</body>

</html>