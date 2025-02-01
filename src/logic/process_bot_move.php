<?php
require_once("logic.php");
require_once("bot.php");
session_start();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chessboard_obj = $_SESSION['chess_logic']->get_chessboard_obj();
    $chessboard = $_SESSION['chess_logic']->get_chessboard();
    $bot = new Bot();
    $best_node = $bot->alpha_beta_pruning($chessboard_obj, $chessboard, 3, 0, -100000, 100000, true);
    $best_move = $best_node[0];
    $counter = $bot->get_counter();
    //print($counter); // print number of board evaluations
    $_SESSION['chess_logic']->input_move($best_move[0], $best_move[1], $best_move[2], $best_move[3]);
}else{
    echo "This is not a POST request";
}
?>