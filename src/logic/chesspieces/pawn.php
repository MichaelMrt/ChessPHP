<?php
require_once("chess_piece.php");

class Pawn extends ChessPiece implements JsonSerializable
{
    function check_move_legal():bool
    {
        return true;
    }

    public function jsonSerialize():mixed {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'color' => $this->color,
        ];
    }
}
