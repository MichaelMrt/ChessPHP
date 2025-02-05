<?php
require_once("logic.php");
require_once("move.php");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $move = get_played_move();
    $selected_piece_x = $move[0];
    $selected_piece_y = $move[1];
    $move_to_x = $move[2];
    $move_to_y = $move[3];
    $move = new Move((int) $selected_piece_x, (int) $selected_piece_y, (int) $move_to_x, (int) $move_to_y);
    $_SESSION['chess_logic']->input_move($move);
}else{
    echo "This is not a POST request";
}

function get_played_move() : string 
{
            $move=$_POST['selected_piece_id'].$_POST['move_to_id'];
            return $move;
}
?>