<?php

session_start();

require_once("database.php");

$decoded_quiz = json_decode(file_get_contents("php://input"), false)->quiz;
echo var_dump($decoded_quiz);
echo "\nTitle: " . $decoded_quiz->title;

class QuizUploadReceiver {
    private $db;

    public function __construct() {
        $this->db = new DatabaseInterface();
    }

    public function receive($data) {
        echo $data;
        echo "\n";
        $decoded = json_decode($data,false); //false para poder utilizar el objeto como un objeto de PHP, en lugar de un array asociativo
        echo $decoded;
    }
}

?>