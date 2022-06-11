<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GeoQuiz - Economic geology</title>
    <meta name="author" content="Juan Torrente" />
    <meta name="keywords" content="admin, admin panel, upload">
    <meta name="description" content="Administration tab for the GeoQuiz site." />
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


    <h2>Quizzes</h2>
    <?php
    session_start();
    require_once("database.php");
    require_once("quiz_lib.php");

    $databaseInterface = new DatabaseInterface();
    $quizManager = new QuizManager($databaseInterface);


    if (isset($_POST["logout"])) {
        unset($_SESSION["username"]);
    }


    if (isset($_POST["username"])) {
        $username = $_POST["username"];
        $_SESSION["username"] = $username;
        
        $quizManager->showQuizzes();
        echo "
            <form action='#' method='post'>
                <input type='submit' name='logout' value='Log out' $logoutAble />
            </form>
        ";

    } else if (isset($_SESSION["username"])) {
        $username = $_SESSION["username"];
        $region = $_POST["region"];

        $quizManager->showQuizzes($region);
        echo "
            <form action='#' method='post'>
                <input type='submit' name='logout' value='Log out' $logoutAble />
            </form>
        ";

    } else {
        $quizManager->showLoginForm();
        $quizManager->showLocationStatusSection();
    }

    ?>
</body>

</html>