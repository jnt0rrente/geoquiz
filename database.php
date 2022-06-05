<?php
    class DatabaseInterface {
        private $dbAddress;
        private $dbName;
        private $dbUsername;
        private $dbPassword;

        private $dbConnection;

        public function __construct() {
            $this->dbAddress = "localhost";
            $this->dbName = "geoquiz";
            $this->dbUsername = "DBUSER2021";
            $this->dbPassword = "DBPSWD2021";
        }

        private function executeStatement($stmt) {
            if (!$stmt->execute()) {
                echo "\nDatabase error: " . $stmt->error;
                exit;
            }
        }

        //devuelve el resultado de la query antes de llamar a fetch_*()
        private function executePreparedQuery($preparedQuery) {
            $res = $preparedQuery->execute();
            if (!$res) {
                echo "\nDatabase error: " . $preparedQuery->error;
                exit;
            }

            return $res->get_result();            
        }

        private function disconnect() {
            $this->dbConnection->close();
            $this->dbConnection = NULL;
        }

        private function connect() {
            $this->dbConnection = new mysqli(
                $this->dbAddress,
                $this->dbUsername,
                $this->dbPassword,
                $this->dbName
            );

            if ($this->dbConnection->connect_errno) {
                echo "database error: " . $this->dbConnection->connect_error;
            }
        }

        public function readQuestionsByQuizId($quizId) {
            $this->connect();
            echo "FF";
            $selectQuestionPrepared = "select q.* from question q, quiz z where z.id = ? AND (select count(*) from contains c where id_cuestionario = z.id and id_pregunta = q.id )";
            $selectQuestionPrepared->bind_param("s", $quizId);
            $questionsArray = array();

            echo "EE";

            $queryResult = $this->executePreparedQuery($selectQuestionPrepared);

            echo "DD";
            if ($queryResult -> fetch_assoc() != NULL) {
                $queryResult->data_seek(0);
                while($row = $queryResult->fetch_assoc()) {
                    $questionsArray[] = $row;
                    echo "CC";
                }
            }

            echo "BB";

            $this->disconnect();
            return $questionsArray;
        }

        public function read_quizzes() {
            $this->connect();

            $quizSelectQuery = "SELECT * FROM quiz ORDER BY id";

            $queryResult = $this->dbConnection->query($quizSelectQuery);
            $quizArray = array();

            if ($queryResult -> fetch_assoc() != NULL) {
                $queryResult->data_seek(0);
                while($row = $queryResult->fetch_assoc()) {
                    $quizArray[] = $row; //devolveremos las filas como array asociativo, no un objeto Quiz
                }
            }

            $this->disconnect();
            return $quizArray;
        }

        public function add_quiz($newQuiz) {
            $this->connect();

            //statements
            $quizInsertStatement = "INSERT INTO quiz(title, description) VALUES (?, ?)";
            $questionInsertStatement = "INSERT INTO question(title, opt1, opt2, opt3, opt4, correct_option) VALUES (?, ?, ?, ?, ?, ?)";
            $containsInsertStatement = "INSERT INTO contains(id_cuestionario, id_pregunta) VALUES (?, ?)";

            //preparing and inserting the quiz object
            $quizInsertPrepared = $this->dbConnection->prepare($quizInsertStatement);
            $quizInsertPrepared->bind_param("ss", $newQuiz->title, $newQuiz->description);
            $this->executeStatement($quizInsertPrepared);

            //save the new quiz id
            $thisQuizId = $this->dbConnection->insert_id;

            foreach ($newQuiz->questions as $question) {
                //preparing and inserting each question object
                $questionInsertPrepared = $this->dbConnection->prepare($questionInsertStatement);
                $questionInsertPrepared->bind_param("ssssss", $question->text, $question->options[0], $question->options[1], $question->options[2], $question->options[3], $question->correct_option);
                $this->executeStatement($questionInsertPrepared);
                
                //save the new question id
                $thatQuestionId = $this->dbConnection->insert_id;
                
                //preparing and inserting the contains object
                $containsInsertPrepared = $this->dbConnection->prepare($containsInsertStatement);
                $containsInsertPrepared->bind_param("ii", $thisQuizId, $thatQuestionId);
                $this->executeStatement($containsInsertPrepared);
            }
            
            $this->disconnect();
            
            return;
        }

    }

?>