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

        private function executeStatement($stmt) {
            if (!$stmt->execute()) {
                echo "\nDatabase error: " . $stmt->error;
            }
        }

        //devuelve el resultado de la query antes de llamar a fetch_*()
        private function executePreparedQuery($preparedQuery) {
            $res = $preparedQuery->execute();
            if (!$res) {
                echo "\nDatabase error: " . $preparedQuery->error;
            }

            return $preparedQuery->get_result();            
        }

        public function readQuizById($id) {
            $this->connect();

            $selectQuizQuery = "SELECT * FROM quiz where id = ?";
            $selectQuizPrepared = $this->dbConnection->prepare($selectQuizQuery);
            $selectQuizPrepared->bind_param("i", $id);

            $queryResult = $this->executePreparedQuery($selectQuizPrepared);

            if ($queryResult -> fetch_assoc() != NULL) {
                $queryResult->data_seek(0);
                $quizAsAssocArray = $queryResult->fetch_assoc();
            } else {
                $this->disconnect();
                return NULL;
            }

            $this->disconnect();
            return $quizAsAssocArray;
        }

        public function readQuestionsByQuizId($quizId) {
            $this->connect();

            $selectQuestionQuery = "select q.* from question q, quiz z where z.id = ? AND (select count(*) from contains c where id_cuestionario = z.id and id_pregunta = q.id )";
            $selectQuestionPrepared = $this->dbConnection->prepare($selectQuestionQuery);
            $selectQuestionPrepared->bind_param("i", $quizId);
            
            $questionsArray = array();
            $queryResult = $this->executePreparedQuery($selectQuestionPrepared);

            if ($queryResult -> fetch_assoc() != NULL) {
                $queryResult->data_seek(0);
                while($row = $queryResult->fetch_assoc()) {
                    $questionsArray[] = $row;
                }
            }

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
            $questionInsertStatement = "INSERT INTO question(text, opt1, opt2, opt3, opt4, correct_option) VALUES (?, ?, ?, ?, ?, ?)";
            $containsInsertStatement = "INSERT INTO contains(id_cuestionario, id_pregunta) VALUES (?, ?)";
            $restrictionInsertStatement = "INSERT INTO restriction(continente, id_cuestionario) VALUES (?, ?)";

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

            foreach ($newQuiz->restrictions as $restriction) {
                echo $restriction;
                $restrictionInsertPrepared = $this->dbConnection->prepare($restrictionInsertStatement);
                $restrictionInsertPrepared->bind_param("si", $restriction, $thisQuizId);

                $this->executeStatement($restrictionInsertPrepared);

            }
            
            $this->disconnect();
            
            return;
        }

    }

?>