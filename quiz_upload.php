<?php

session_start();

require_once("database.php");

if (isset($_POST["quiz"])) {
    $receiver = new QuizUploadReceiver();
    $receiver->receive($_POST["quiz"]);
    echo "recibido";
} else {
    echo "no recibido";
    echo "\nPOST: ";
    print_r($_POST);
    echo "\nGET: ";
    print_r($_GET);
}

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