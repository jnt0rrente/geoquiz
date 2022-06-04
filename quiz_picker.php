<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GeoQuiz - Economic geology</title>
    <meta name="author" content="Juan Torrente" />
    <meta name="keywords" content="admin, admin panel, upload">
    <meta name="description" content="Administration tab for the GeoQuiz site." />
    <meta name="viewport" content="width=device-width, initial scale=1.0">

    <link rel="stylesheet" type="text/css" href="style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts/admin.js"></script>
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
        <a href="quiz_picker.php" accesskey="q" tabindex="6">Quizzes</a>
    </nav>

    <?php
    session_start();

    $databaseInterface = new DatabaseInterface();
    $quizManager = new QuizManager($databaseInterface);

    if (isset($_POST["username"])) {
        $username = $_POST["username"];
        $_SESSION["username"] = $username;
        
        echo "All quizzes";
    }
    if (isset($_SESSION["username"])) {
        $username = $_SESSION["username"];

        echo "All quizzes";
    } else {
        echo "
            <h2>Enter your username:</h2>
            <form action='#'>
                <input type=text name='txtUsername' />
                <input type=submit name='username' value='Log in' />
            </form>
        ";
    }



    class QuizManager {
        public function __construct(DatabaseInterface $dbInterface) {
            $this->dbInterface = $dbInterface;
        }

        public function showQuizzes() {
            //$quizzes = $this->dbInterface->retrieveQuizzes();
            
            $question1 = new Question("Question One", ["One", "Two", "Three", "Four"], "a");
            $questions = [$question1];

            $quiz1 = new Quiz(0, "Quiz zero", "Test, burn and retry", 1654170356000, $questions);
            $quizzes = [$quiz1];


            echo "<ul>";
            foreach ($quizzes as $quiz) {
                echo "<li> Quiz $quiz->id: $quiz->name </li>";
            }
            echo "</ul>";
        }
    }

    class Quiz {
        public $id;
        public $name;
        public $description;
        public $timestamp;
        public $questions = [];

        public function __construct($id, $name, $description, $timestamp, $questions) {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->date = $date;
            $this->questions = $questions;
        }
    }

    class Question {
        public $title;
        public $options = [];
        public $correct_option;

        public function __construct($title, $options, $correct_option) {
            $this->title = $title;
            $this->options = $options;
            $this->correct_coption = $correct_option;
        }
    }

    class DatabaseInterface {
        public function __construct() {
            
        }
    }
    ?>
</body>

</html>