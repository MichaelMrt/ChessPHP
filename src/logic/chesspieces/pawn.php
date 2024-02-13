<?php
require_once("chess_piece.php");

class Pawn extends ChessPiece
{
    function check_move_legal():bool
    {
        return true;
    }

}
