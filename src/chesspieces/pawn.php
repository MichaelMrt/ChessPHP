<?php
require_once("chess_piece.php");

class Pawn extends ChessPiece
{
    function move($chessboard){
        $chessboard[1][1] = new Pawn("white"); 
        return $chessboard;
    }

    
}
