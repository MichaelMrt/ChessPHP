<?php

require_once('traits.php');

class Bishop extends ChessPiece
{
    use BishopTrait;
    protected int $weight = 320;

    function __construct(String $color, int $x, int $y)
    {
      parent::__construct($color, $x, $y);
      $this->type = 'bishop';

      if($color=='white'){
        $this->icon ="<img src='../images/chesspieces/white-bishop.png' class='chesspiece'>";
      }elseif($color=='black'){
        $this->icon ="<img src='../images/chesspieces/black-bishop.png' class='chesspiece'>";
      }
    }

    function check_move_legal(mixed $chessboard, Move $move):bool
    {
      if($this->check_legal_bishopmove($chessboard, $move)){
        return true;
      }

      $_SESSION['error'] = "<p class='error'>bishops can't move like that</p>";
        return false;
    }
}
?>