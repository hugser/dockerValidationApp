<?php

class session {

    function __construct() {
        
    }
    
    function __destruct() {
       
    }

    public function GET_acc() {
        if ( !isset($_GET['acc']) || !in_array($_GET['acc'],array('R1','R3'))) {
            $_SESSION['error'] = "Missing acc";
            header('Location: ../views/index.html.php');
            return;
        }
    }

}

