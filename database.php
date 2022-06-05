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
            $this->dbconnection->close();
            $this->dbconnection = NULL;
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

        public function runQuery($query) {
            if (!$this->dbConnection->query($query)) {
                echo "\nDatabase error: " . $this->dbConnection->error;
                return false;
            }
        }

        public function add_quiz($newQuiz) {
            $this->connect();

            $quizInsertStatement = "INSERT INTO `quiz`(`title`, `description`) VALUES ('$newQuiz->title','$newQuiz->description')";
            
            $this->runQuery($quizInsertStatement);

            $thisQuizId = $this->dbConnection->insert_id;

            foreach ($newQuiz->questions as $question) {
                $questionInsertStatement = "INSERT INTO 'question'('title','opt1','opt2','opt3','opt4','correct_option') 
                                            VALUES ('$question->title','$question->options[0]','$question->options[1]','$question->options[2]','$question->options[3]','$question->correct_opion')";
                
                $this->runQuery($questionInsertStatement);
                
                $thatQuestionId = $this->dbConnection->insert_id;

                $containsInsertStatement = "INSERT INTO 'contains'('id_cuestionario','id_pregunta') 
                                            VALUES ('$thisQuizId','$thatQuestionId')";

                $this->runQuery($containsInsertStatement);
            }

            $this->disconnect();

            return true;
        }

    }

?>