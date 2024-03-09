<?php
class Queen extends ChessPiece
{
    function __construct(String $color, int $x, int $y)
    {
      parent::__construct($color, $x, $y);
      $this->type = 'queen';

      if($color=='white'){
        $this->icon ="<img src='../images/chesspieces/white-queen.png' class='chesspiece'>";
      }elseif($color=='black'){
        $this->icon ="<img src='../images/chesspieces/black-queen.png' class='chesspiece'>";
      }
    }

    function check_move_legal(mixed $chessboard, int $move_to_x, int $move_to_y):bool
    {
        return true;
    }
}
?>