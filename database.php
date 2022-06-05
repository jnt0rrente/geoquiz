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

        public function read_quizzes() {
            
        }

        public function executeStatement($stmt) {
            if (!$stmt->execute()) {
                echo "\nDatabase error: " . $stmt->error;
                exit;
            }
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