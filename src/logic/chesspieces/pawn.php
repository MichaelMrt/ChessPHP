<?php
require_once("chess_piece.php");

class Pawn extends ChessPiece implements JsonSerializable
{
    function check_move_legal()
    {
        
    }

    public function jsonSerialize() {
        return [
            'x' => $this->x,
            'y' => $this->x,
            'color' => $this->color,
        ];
    }
}
