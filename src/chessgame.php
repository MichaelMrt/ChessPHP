<?php
    require_once("controller.php");

    session_start();

    if(isset($_SESSION['current_x']) & isset($_SESSION['current_y']))
    {
        $_POST['current_x'] = $_SESSION['current_x'];
        $_POST['current_y'] = $_SESSION['current_y'];
    }

    if(isset($_SESSION['chessboard'])){
        $_POST['chessboard'] = $_SESSION['chessboard'];
    }

    $_SESSION = $_POST;


    print_r($_SESSION);
    echo "<link rel='stylesheet' href='style.css'>";
    echo "<h1>Chessgame</h1>";
    
    $controller = new Controller();
?>