<?php
    require_once("controller.php");

    session_start();

    # Values that are stored in the SESSION get moved to the $_POST array
    # Data from the submits is stored in the POST array

    # Maybe swap loading the data from SESSION into POST and making SESSION=POST in the end
    # to just add data from POST into SESSION one by one

    if(isset($_SESSION['current_x']) & isset($_SESSION['current_y']))
    {
        $_POST['current_x'] = $_SESSION['current_x'];
        $_POST['current_y'] = $_SESSION['current_y'];
    }

    /* I think it isn't necessary anymore
    if(isset($_SESSION['chessboard'])){
        $_POST['chessboard'] = $_SESSION['chessboard'];
    }    */

    if(isset($_SESSION['whitesturn'])){
        $_POST['whitesturn'] = $_SESSION['whitesturn'];
    }

    if(isset($_SESSION['move_number'])){
       $_POST['move_number'] = $_SESSION['move_number']; 
    }else{
        $_POST['move_number'] = 0;
    }

    # resets the board in logic constructor
    if(isset($_POST['reset'])){
        $_POST = array();
        $_SESSION = array();
        $_POST['move_number'] = 0;
    }

    $_SESSION = $_POST;

    echo "<link rel='stylesheet' href='style.css'>";
   # echo "<h1>Chessgame</h1>";

   # header
    echo "<div><h1>ChessPHP</h1>";
    echo "Select a chess piece by clicking on the square it occupies, then click on the destination square to move it<br>
    or input your move down below and hit submit<br><br></div>";
    
    $controller = new Controller();

    # footer
    echo "<div><h1>footer</h1></div>";
?>