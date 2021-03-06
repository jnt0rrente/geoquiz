<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>GeoQuiz - All quizzes</title>
    <meta name="author" content="Juan Torrente" />
    <meta name="keywords" content="quiz, list">
    <meta name="description" content="All the quizzes on our site." />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="styles/style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts/quiz_list.js"></script>
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
    session_start(); //para guardar y recuperar el username y su ubicación
    require_once("database.php");
    require_once("quiz_lib.php");

    $databaseInterface = new DatabaseInterface();
    $quizManager = new QuizManager($databaseInterface);


    if (isset($_POST["logout"])) {
        unset($_SESSION["username"]);
        unset($_SESSION["region"]);

    } 
    
    if (isset($_POST["username"]) && isset($_POST["region"])) {
        $_SESSION["region"] = $_POST["region"];
        $_SESSION["username"] = $_POST["username"];

        $quizManager->showQuizzesAndLogout($_SESSION["region"]);
        

    } else if (isset($_SESSION["username"]) && isset($_SESSION["region"])) {
        $region = $_SESSION["region"];

        $quizManager->showQuizzesAndLogout($region);
        
    } else {
        $quizManager->showLoginAndLocation();
    }

    ?>
</body>

</html>