<?php

require_once("database.php");

if (isset($_POST["quiz"])) {
    $receiver = new QuizUploadReceiver();
    $receiver->receive($_POST["quiz"]);
}

class QuizUploadReceiver {
    private $db;

    public function __construct() {
        $this->db = new DatabaseInterface();
    }

    public function receive($data) {
        $decoded_quiz = json_decode($data,false)->quiz; //false para poder utilizar el objeto como un objeto de PHP, en lugar de un array asociativo
        echo $decoded_quiz;
    }
}

?>