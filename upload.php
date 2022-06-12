<?php

require_once("database.php");

$decoded_quiz = json_decode(file_get_contents("php://input"), false)->quiz;


if (isset($decoded_quiz)) {
    $receiver = new QuizUploadReceiver();
    $receiver->receive($decoded_quiz);
} else {
    echo "Error: No quiz.";
}


class QuizUploadReceiver {
    private $db;

    public function __construct() {
        $this->db = new DatabaseInterface();
    }

    public function receive($quiz) {
        $this->validateQuiz($quiz);

        try {
            $this->db->writeQuiz($quiz);
            echo "Quiz added successfully.";
        } catch (Exception $e) {
            echo "Database error: " . $e->getMessage() . "\n";
            exit;
        }
    }

    //enforces that any given string is declared, not null and longer than 0 characters
    private function validateRequiredString($string, $name) {
        if (!isset($string)) {
            echo "Error: No " . $name .  ".";
            exit;
        } elseif (strlen($string) == 0) {
            echo "Error: " . $name . " is too short.";
            exit;
        }
    }

    //enforces that the passed array is declared, not null and within the specified size range
    //min and max are both inclusive, so [min, max]
    //negative for unbounded: [min, inf)
    private function validateRequiredArray($array, $name, $min, $max) {
        if (!isset($array)) {
            echo "Error: No " . $name .  ".";
            exit;
        } 

        if ($max >= 0) {
            if (count($array) > $max) {
                echo "Error: " . $name . " array is too long.";
                exit;
            }
        }

        if ($min >= 0) {
            if (count($array) < $min) {
                echo "Error: " . $name . " array is too short.";
                exit;
            }
        }
    }

    //validator aggregator
    private function validateQuiz($quiz) {
        $this->validateRequiredString($quiz->title, "title");
        $this->validateRequiredString($quiz->description, "description");
        $this->validateRequiredArray($quiz->questions, "questions", 1, -1); //at least one
        
        foreach ($quiz->questions as $question) {
            $this->validateRequiredString($question->text, "text");
            $this->validateRequiredString($question->correct_option, "correct option");
            $this->validateRequiredArray($question->options, "options", 4, 4);
        }
    }
}

?>