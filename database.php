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

        //desconecta la base de datos
        private function disconnect() {
            $this->dbConnection->close();
            $this->dbConnection = NULL;
        }

        //establece la conexión a la base de datos.
        private function connect() {
            $this->dbConnection = new mysqli(
                $this->dbAddress,
                $this->dbUsername,
                $this->dbPassword,
                $this->dbName
            );

            if ($this->dbConnection->connect_errno) {
                echo "Database error: " . $this->dbConnection->connect_error;
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

        //dado el ID de un cuestionario, lo extrae de la base de datos y devuelve como un array asociativo (no un objeto)
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


        //dado el ID de un cuestionario, lee las preguntas que contiene
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

        //dado el ID de un cuestionario, devuelve un array no asociativo con las regiones en las que está bloqueado
        public function readRestrictionsByQuizId($quizId) {
            $this->connect();

            $selectRestrictionQuery = "select r.continente from restriction r, quiz q where q.id = ? AND r.id_cuestionario = q.id";
            $selectRestrictionPrepared = $this->dbConnection->prepare($selectRestrictionQuery);
            $selectRestrictionPrepared->bind_param("i", $quizId);
            
            $restrictionsArray = array();

            $queryResult = $this->executePreparedQuery($selectRestrictionPrepared);

            if ($queryResult -> fetch_array() != NULL) {
                $queryResult->data_seek(0);
                while ($row = $queryResult->fetch_row()) {
                    $restrictionsArray[] = $row[0];
                }
            }

            $this->disconnect();
            return $restrictionsArray;
        }

        //devuelve un array asociativo con todos los quizzes que existen en la base de datos
        public function readQuizzes() {
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

        //recibe un objeto Quiz y lo introduce en la base de datos
        public function writeQuiz($newQuiz) {
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
                $restrictionInsertPrepared = $this->dbConnection->prepare($restrictionInsertStatement);
                $restrictionInsertPrepared->bind_param("si", $restriction, $thisQuizId);

                $this->executeStatement($restrictionInsertPrepared);

            }
            
            $this->disconnect();
            
            return;
        }

        // recibiendo un usuario, una puntuación y el ID de un quiz, crea un intento con esos valores
        public function writeAttempt($id_quiz, $username, $score) {
            $this->connect();

            $attemptInsertStatement = "INSERT INTO attempt(user, score, id_quiz) VALUES (?, ?, ?)";

            $attemptInsertPrepared = $this->dbConnection->prepare($attemptInsertStatement);
            $attemptInsertPrepared->bind_param("sii", $username, $score, $id_quiz);

            $this->executeStatement($attemptInsertPrepared);

            $this->disconnect();
        }


        // devuelve un array asociativo con los intentos que tiene un determinado cuestionario
        public function readAttemptsForQuiz($id_quiz) {
            $this->connect();

            $attemptSelectQuery = "SELECT * FROM attempt WHERE id_quiz = ? ORDER BY SCORE DESC";
            $attemptSelectPrepared = $this->dbConnection->prepare($attemptSelectQuery);
            $attemptSelectPrepared->bind_param("i", $id_quiz);

            $queryResult = $this->executePreparedQuery($attemptSelectPrepared);

            $attempts = array();

            if ($queryResult -> fetch_assoc() != NULL) {
                $queryResult->data_seek(0);
                while($row = $queryResult->fetch_assoc()) {
                    $attempts[] = $row;
                }
            } else {
                $this->disconnect();
                return NULL;
            }

            $this->disconnect();

            return $attempts;
        }

    }

?>