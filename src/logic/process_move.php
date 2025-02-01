<?php
require_once("logic.php");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $move = get_played_move();
    $selected_piece_x = $move[0];
    $selected_piece_y = $move[1];
    $move_to_x = $move[2];
    $move_to_y = $move[3];

    $_SESSION['chess_logic']->input_move($selected_piece_x, $selected_piece_y, $move_to_x, $move_to_y);
}else{
    echo "This is not a POST request";
}

function get_played_move() : string 
{
            $move=$_POST['selected_piece_id'].$_POST['move_to_id'];
            return $move;
}
?>