<?php
require_once('rook.php');
require_once('bishop.php');
class Queen extends ChessPiece
{

    use RookTrait;
    use BishopTrait;
    protected int $weight = 900;
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

    function check_move_legal(mixed $chessboard, Move $move):bool
    {
      if($this->check_legal_rookmove($chessboard, $move) || $this->check_legal_bishopmove($chessboard, $move)){
        return true;
      }
      $_SESSION['error'] = "<p class='error'>queens can't move like that</p>";
      return false;
    }
}
?>