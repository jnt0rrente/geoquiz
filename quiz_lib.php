<?php
class QuizManager {
    private $dbInterface;

    public function __construct(DatabaseInterface $dbInterface) {
        $this->dbInterface = $dbInterface;
    }

    //lee los quizzes de la interfaz de la BD y los transforma en objetos Quiz
    private function getQuizObjects() {
        try {
            $quizAssocArray = $this->dbInterface->readQuizzes();
        } catch (Exception $e) {
            echo "Database error: " . $e->getMessage() . "\n";
            exit;
        }

        $quizArray = array();

        foreach ($quizAssocArray as $eachQuiz) {
            try {
                $readQuestionsArray = $this->dbInterface->readQuestionsByQuizId($eachQuiz["id"]);
                $quizQuestionsArray = array();

                foreach ($readQuestionsArray as $eachQuestion) {
                    $options = array($eachQuestion["opt1"], $eachQuestion["opt2"], $eachQuestion["opt3"], $eachQuestion["opt4"]);
                    $quizQuestionsArray[] = new Question($eachQuestion["text"], $options, $eachQuestion["correct_option"]);
                }
            } catch (Exception $e) {
                echo "Database error: " . $e->getMessage() . "\n";
                exit;
            }

            
            try {
                $readRestrictionsArray = $this->dbInterface->readRestrictionsByQuizId($eachQuiz["id"]);
            } catch (Exception $e) {
                echo "Database error: " . $e->getMessage() . "\n";
                exit;
            }
            
            $quizArray[] = new Quiz($eachQuiz["id"], $eachQuiz["title"], $eachQuiz["description"], $quizQuestionsArray, $readRestrictionsArray);
        }

        return $quizArray;
    }

    //devuelve un array no asociativo con las soluciones de un determinado cuestionario
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

        $readRestrictionsArray = array();
        try {
            $readRestrictionsArray = $this->dbInterface->readRestrictionsByQuizId($eachQuiz["id"]);
        } catch (Exception $e) {
            echo "Database error: " . $e->getMessage() . "\n";
            exit;
        }
        
        return new Quiz($quiz["id"], $quiz["title"], $quiz["description"], $quizQuestionsArray, $readRestrictionsArray);
    }

    //imprime en el HTML los cuestionarios disponibles para el usuario. para ello, recibe un parámetro $region con la región en la que se encuentra el usuario.
    public function showQuizzes($region) {
        $username = $_SESSION['username'];
        $quizzes = $this->getQuizObjects();

        echo "<h3>These are all our quizzes, $username </h3>";
        echo "<ul>";
        foreach ($quizzes as $quiz) {
            if ($quiz->isAllowedOnRegion($region)) {
                echo "<li> <a href='/quiz.php?id=$quiz->id'> Quiz $quiz->id: $quiz->title</a> </li>";
            } else {
                echo "<li> Quiz $quiz->id: $quiz->title (access restricted) </li>";
            }
            
        }
        echo "</ul>";
    }

    //imprime el botón que permite al usuario salir de la plataforma
    public function showLogoutButton() {
        echo "
        <form action='#' method='post'>
            <input type='submit' name='logout' value='Log out' $logoutAble />
        </form>
    ";
    }

    //imprime el formulario que contiene la lógica para que el usuario entre en la plataforma
    public function showLoginForm() {
        echo "<p>In order to use this application, you must provide an username and your region (continent). The continent will be automatically retrieved from your location, so you need to give us permission to read it. Do not manually tamper with the Region field.</p>";
        echo "  <form action='#' method='post'>
                    <label for='username'>Username</label>
                        <input type=text name='username' id='username' required />

                    <label for='region'>Region</label>
                        <input type=text name='region' id='region' required/>
                    <input type=button value='Load location' onclick=qlm.loadContinent() disabled/>
                    
                    <input type=submit value='Log in' disabled />
                </form>
    ";
    }

    //imprime una sección para mostrar en vivo el estado de la gelocalización del usuario
    public function showLocationStatusSection() {
        echo "  <section>
                    <h3>Location service status</h3>
                    <p>Awaiting...</p>
                </section>
        ";
    }

    //imprime un cuestionario rellenable, con sus preguntas y la lógica de envío
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
        echo "<input type=submit name='finishQuiz' value='Send' />";
        echo "</form>";
        echo "</section>";
    }

    //tras rellenar el cuestionario, imprime la página de resultados
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

        for ($i = 0; $i < $limit; $i++) {
            $username = $leaderboard[$i]->username;
            $score = $leaderboard[$i]->score;
            $date = $leaderboard[$i]->date;

            echo "  <tr>
                        <td>$username</td>
                        <td>$date</td>
                        <td>$score</td>
                    </tr>";
        }

        echo "</section>";
    }

    //guarda un intento en la base de datos, comprobando que los parámetros sean correctos
    private function recordQuizAttempt($id_quiz, $username, $counter) {
        if ($id_quiz != NULL && $username != NULL) {
            $this->dbInterface->writeAttempt($id_quiz, $username, $counter);
        }
    }

    //obtiene de la base de datos el ranking de un cuestionario
    private function getLeaderboard($id) {
        $leaderboard = $this->dbInterface->readAttemptsForQuiz($id);
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
    public $restricted = array();

    public function __construct($id, $title, $description, $questions, $restricted) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->date = $date;
        $this->questions = $questions;
        $this->restricted = $restricted;
    }

    //comprueba si el cuestionario puede mostrarse en la región que se le pasa como parámetro
    public function isAllowedOnRegion($region) {    
        echo "You come from " . $region;        
        foreach ($this->restricted as $banned_region) {
            echo "\nThis quiz is restricted on: " . $banned_region;
            if (strcasecmp($region, $banned_region) == 0) {
                return false;
            }
        }

        return true;
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