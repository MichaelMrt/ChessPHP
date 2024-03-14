<?php
require_once('traits.php');

class Rook extends ChessPiece
{   
    use RookTrait;

    function __construct(String $color, int $x, int $y)
    {
      parent::__construct($color, $x, $y);
      $this->type = 'rook';

      if($color=='white'){
        $this->icon ="<img src='../images/chesspieces/white-rook.png' class='chesspiece'>";
      }elseif($color=='black'){
        $this->icon ="<img src='../images/chesspieces/black-rook.png' class='chesspiece'>";
      }
    }

    function check_move_legal(mixed $chessboard, int $move_to_x, int $move_to_y):bool
    {
       if($this->check_legal_rookmove($chessboard, $this->x,$this->y,$move_to_x,$move_to_y)){
          return true;
       }
       
       $_SESSION['error'] = "<p class='error'>rooks can't move like that</p>";
        return false;
    }
}
?>