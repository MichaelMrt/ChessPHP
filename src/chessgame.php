<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Chessboard</title>
</head>
<body>
<?php
    require_once("logic/logic.php");
    session_start();

    if(!game_started()){
        $logic = new logic(); //start game
        $_SESSION['chess_game'] = $logic;
        # footer
        echo "<div class='center'>
        <h1>footer</h1>
        <p> Contribute on GitHub:<a href='https://github.com/MichaelMrt03/ChessPHP'>https://github.com/MichaelMrt03/ChessPHP</a></p>
        </div>";
    }else{
    get_played_move();
    $_SESSION['chess_game']->input_move(2,2,2,4);
}       
    


function game_started():bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function get_played_move() : string 
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $move = isset($_POST['move']) ? $_POST['move'] : '';
        if(isset($move)){
            $move=$_POST['move'];
        }else{
            $move = 'Error while processing the move';
        }
    
        echo "Move played: ".$move;
        return $move;
    }
}
?>
<script src="javascript.js"></script>
</body>
</html>