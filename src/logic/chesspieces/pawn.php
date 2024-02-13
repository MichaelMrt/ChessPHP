<?php
require_once("chess_piece.php");

class Pawn extends ChessPiece
{   

    function __construct(String $color, int $x, int $y)
    {
      parent::__construct($color, $x, $y);
      $this->type = 'pawn';
    }

    function check_move_legal():bool
    {
        return true;
    }

}
