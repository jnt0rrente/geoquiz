<?php
class QuizManager {
        private $dbInterface;

        public function __construct(DatabaseInterface $dbInterface) {
            $this->dbInterface = $dbInterface;
        }

        private function getQuizObjects() {
            try {
                $quizAssocArray = $this->dbInterface->read_quizzes();
            } catch (Exception $e) {
                echo "Database error: " . $e->getMessage() . "\n";
                exit;
            }
            $quizArray = array();

            foreach ($quizAssocArray as $eachQuiz) {
                try {
                    $readQuestionsArray = $this->dbInterface->readQuestionsByQuizId($eachQuiz["id"]);
                } catch (Exception $e) {
                    echo "Database error: " . $e->getMessage() . "\n";
                    exit;
                }
                
                $quizQuestionsArray = array();

                foreach ($readQuestionsArray as $eachQuestion) {
                    $options = array($eachQuestion["opt1"], $eachQuestion["opt2"], $eachQuestion["opt3"], $eachQuestion["opt4"]);
                    $quizQuestionsArray[] = new Question($eachQuestion["text"], $options, $eachQuestion["correct_option"]);
                }
                $quizArray[] = new Quiz($eachQuiz["id"], $eachQuiz["title"], $eachQuiz["description"], $quizQuestionsArray);
            }

            return $quizArray;
        }

        //devuelve un objeto quiz con sus preguntas y sus atributos. si no existe ninguno con la ID, devuelve directamente NULL.
        private function getSingleQuizById($id) {
            try {
                $quiz = $this->dbInterface->readQuizById($id);
                if ($quiz == NULL) {
                    return NULL;
                }
            } catch (Exception $e) {
                echo "Database error: " . $e->getMessage() . "\n";
                exit;
            }

            try {
                $readQuestionsArray = $this->dbInterface->readQuestionsByQuizId($id);
            } catch (Exception $e) {
                echo "Database error: " . $e->getMessage() . "\n";
                exit;
            }
            
            $quizQuestionsArray = array();

            foreach ($readQuestionsArray as $eachQuestion) {
                $options = array($eachQuestion["opt1"], $eachQuestion["opt2"], $eachQuestion["opt3"], $eachQuestion["opt4"]);
                $quizQuestionsArray[] = new Question($eachQuestion["text"], $options, $eachQuestion["correct_option"]);
            }

            return new Quiz($eachQuiz["id"], $eachQuiz["title"], $eachQuiz["description"], $quizQuestionsArray);
        }

        public function showQuizzes() {
            $username = $_SESSION['username'];
            
            $quizzes = $this->getQuizObjects();

            echo "<h2>These are all our quizzes, $username </h2>";
            echo "<ul>";
            foreach ($quizzes as $quiz) {
                echo "<li> <a href='/quiz.php?id=$quiz->id'> Quiz $quiz->id: $quiz->title</a> - $date </li>";
            }
            echo "</ul>";
        }

        public function displaySingleQuizPage($id) {
            $quiz = $this->getSingleQuizById($id);


            echo "<h2> $quiz->title </h2>";
            echo "<p> $quiz->description </p>";

            for ($i = 0; $i < count($quiz->questions); $i++) {
                echo "<p>" . $quiz->questions[$i]->text . "</p>";
            }
        }
    }

    class Quiz {
        public $id;
        public $title;
        public $description;
        public $questions = array();

        public function __construct($id, $title, $description, $questions) {
            $this->id = $id;
            $this->title = $title;
            $this->description = $description;
            $this->date = $date;
            $this->questions = $questions;
        }
    }

    class Question {
        public $text;
        public $options = array();
        public $correct_option;

        public function __construct($text, $options, $correct_option) {
            $this->text = $text;
            $this->options = $options;
            $this->correct_option = $correct_option;
        }
    }
?>