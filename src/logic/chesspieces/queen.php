<?php
require_once('rook.php');
require_once('bishop.php');
class Queen extends ChessPiece
{

    use RookTrait;
    use BishopTrait;

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
      if($this->check_legal_rookmove($chessboard, $this->x, $this->y,  $move_to_x,  $move_to_y) || $this->check_legal_bishopmove($chessboard, $this->x, $this->y,  $move_to_x,  $move_to_y)){
        return true;
      }
      $_SESSION['error'] = "<p class='error'>queens can't move like that</p>";
      return false;
    }
}
?>