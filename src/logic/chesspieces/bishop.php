<?php
class Bishop extends ChessPiece
{
    function __construct(String $color, int $x, int $y)
    {
      parent::__construct($color, $x, $y);
      $this->type = 'bishop';
    }

    function check_move_legal(mixed $chessboard, int $move_to_x, int $move_to_y):bool
    {
        return true;
    }
}
?>