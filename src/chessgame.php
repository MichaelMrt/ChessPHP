<?php
    require_once("controller.php");

    session_start();
    $_SESSION = $_POST;

    echo "<link rel='stylesheet' href='style.css'>";
    echo "<h1>Chessgame</h1>";
    
    $controller = new Controller();
?>