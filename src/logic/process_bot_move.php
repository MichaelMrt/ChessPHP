<?php
require_once("logic.php");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['chess_game']->input_move(4, 7, 4, 5);
}
?>