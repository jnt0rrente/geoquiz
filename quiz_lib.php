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

        public function getSolutionsForQuiz($id) {
            $quiz = $this->getSingleQuizById($id);
            $response_array = array();

            foreach($quiz->questions as $question) {
                $response_array[] = $question->correct_option;
            }

            return $response_array;
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
            return new Quiz($quiz["id"], $quiz["title"], $quiz["description"], $quizQuestionsArray);
        }

        public function showQuizzes() {
            $username = $_SESSION['username'];
            
            $quizzes = $this->getQuizObjects();

            echo "<h2>These are all our quizzes, $username </h2>";
            echo "<ul>";
            foreach ($quizzes as $quiz) {
                echo "<li> <a href='/quiz.php?id=$quiz->id'> Quiz $quiz->id: $quiz->title</a> </li>";
            }
            echo "</ul>";
        }

        public function displaySingleQuizSection($id) {
            $quiz = $this->getSingleQuizById($id);

            echo "<section>";

            if ($quiz == NULL) {
                echo "<h2>Error: this quiz does not exist</h2>";
                echo "</section>";
                exit;
            }

            echo "<h2> $quiz->title </h2>";
            echo "<p> $quiz->description </p>";

            echo "<form action='#' method='post'>";

            for ($i = 0; $i < count($quiz->questions); $i++) {
                $text = $quiz->questions[$i]->text;
                $options = $quiz->questions[$i]->options;
                $correct_option = $quiz->questions[$i]->correct_option;
                $question_name = "q" . ($i+1);

                echo "<fieldset>";

                echo "<legend> " . ($i+1) . ". " . $text . " </legend>";

                for ($j = 0; $j < count($options); $j++) {
                    $optionText = $options[$j];
                    $id = $question_name . $j;
                    $value = array("a", "b", "c", "d")[$j];

                    echo "<label for='$id'>";
                    echo "<input type='radio' id='$id' name='$question_name' value='$value' required/>";
                    echo $optionText . "</label>";
                }

                echo "</fieldset>";
            }
            echo "<input type=submit value='Enviar' />";
            echo "</form>";
            echo "</section>";
        }

        public function displayResultsForQuiz($id, $answers) {
            $correctAnswers = $this->getSolutionsForQuiz($id);
            $counter = 0;

            for ($i = 0; $i < count($correctAnswers); $i++) {
                if ($correctAnswers[$i] == $answers[$i]) {
                    $counter++;
                }
            }

            $scoreString = $counter . "/" . count($correctAnswers);
            echo "  <section>
                        <h2> Results </h2>
                        <p> You have scored: " . $scoreString  . "</p>";

            if (isset($_SESSION["username"])) {
                $this->recordQuizAttempt($id, $_SESSION["username"], $counter);
            } else {
                echo "<p>No hemos podido obtener tu username. No hemos registrado tu puntuación.</p>";
            }

            $leaderboard = $this->getLeaderboard($id);
            $limit = 10;
            if (count($leaderboard) < $limit) {
                $limit = count($leaderboard);
            }

            echo    "<table>
                        <caption>Ranking</caption>
                        <tr>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th>Puntuación</th>
                        </tr>";
            for ($i = 0; $i < limit; $i++) {
                $username = $leaderboard[$i]->username;
                $score = $leaderboard[$i]->score;
                $date = $leaderboard[$i]->date;

                echo "  <tr>
                            <td>$username</td>
                            <td>$score</td>
                            <td>$date</td>
                        </tr>";
            }

            echo "</section>";
        }

        private function recordQuizAttempt($id_quiz, $username, $counter) {
            if ($id_quiz != NULL && $username != NULL && $counter != NULL) {
                $this->dbInterface->add_attempt($id_quiz, $username, $counter);
            }
        }

        private function getLeaderboard($id) {
            $leaderboard = $this->dbInterface->read_attempts_for_quiz($id);
            echo "LB: " . var_dump($leaderboard);
            $returnArray = array();

            foreach ($leaderboard as $entry) {
                $returnArray[] = new Attempt($entry["user"], $entry["id_quiz"], $entry["score"], $entry["date"]);
            }

            return $returnArray;
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

    class Attempt {
        public $username;
        public $id_quiz;
        public $score;
        public $date;

        public function __construct($username, $id_quiz, $score, $date) {
            $this->username = $username;
            $this->id_quiz = $id_quiz;
            $this->score = $score;
            $this->date = $date; 
        }
    }
?>