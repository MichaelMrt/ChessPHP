<?php
class King extends ChessPiece
{
    function __construct(String $color, int $x, int $y)
    {
      parent::__construct($color, $x, $y);
      $this->type = 'king';

      if($color=='white'){
        $this->icon ="<img src='../images/chesspieces/white-king.png' class='chesspiece'>";
      }elseif($color=='black'){
        $this->icon ="<img src='../images/chesspieces/black-king.png' class='chesspiece'>";
      }
    }

    function check_move_legal(mixed $chessboard, int $move_to_x, int $move_to_y):bool
    {
      $distance_x = sqrt(pow(($move_to_x-$this->x),2)); 
      $distance_y = sqrt(pow(($move_to_y-$this->y),2)); 

      if($distance_x <= 1 && $distance_y <= 1){
        # check if there is a piece on the move to square and if it is opposite color
         if(is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece') &&  $chessboard[$this->x][$this->y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
          return true;
         }elseif(!is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece')){
          return true;
         }
      }

      # castling short as white
      if($this->color=='white' && $move_to_x==7 && $move_to_y==1){
          if(!is_a($chessboard[6][1], 'Chesspiece') && !is_a($chessboard[7][1],'Chesspiece')){
            if(is_a($chessboard[8][1], 'Rook')){
              return true;
            }
          }
      }

      # castling long as white
      if($this->color=='white' && $move_to_x==3 && $move_to_y==1){
        if(!is_a($chessboard[2][1], 'Chesspiece') && !is_a($chessboard[3][1],'Chesspiece')&& !is_a($chessboard[4][1],'Chesspiece')){
          if(is_a($chessboard[8][1], 'Rook')){
            return true;
          }
        }
    }


      $_SESSION['error'] = "<p class='error'>kings can't move like that</p>";
        return false;
    }

    function castle_rightside(mixed $chessboard):mixed {
      #$chessboard = $chessboard[8][1]->move($chessboard,6,1);
       # Copy the piece to the new position
       $chessboard[6][1] = $chessboard[8][1];
       # Delete old piece position
       $chessboard[8][1] = "";
       # Update position vars
       $chessboard[6][1]->x = 6;
       $chessboard[6][1]->y = 1;

       return $chessboard;
    }
}
?>