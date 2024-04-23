<?php
require_once("chess_piece.php");

class Pawn extends ChessPiece
{   

    function __construct(String $color, int $x, int $y)
    {
      parent::__construct($color, $x, $y);
      $this->type = 'pawn';
      
      if($color=='white'){
        $this->icon ="<img src='../images/chesspieces/white-pawn.png' class='chesspiece'>";
      }elseif($color=='black'){
        $this->icon ="<img src='../images/chesspieces/black-pawn.png' class='chesspiece'>";
      }

    }

    function check_move_legal(mixed $chessboard, int $move_to_x, int $move_to_y):bool
    {
        # Coordinates from the current Piece position
        $current_x = $this->x;
        $current_y = $this->y;
        
        #check if there is nothing on the move_to_field
        if($chessboard[$move_to_x][$move_to_y] == ""){
            #check if pawn is moving two fields forwards, check which color is moving, according to the color check if it is the start position of the pawn, x position should stay the same
            if(((($current_y-2 == $move_to_y) and ($this->color == "black") and ($current_y == 7))  or  
                (($current_y+2 == $move_to_y) and ($this->color == "white") and ($current_y == 2))) and 
                  $current_x == $move_to_x){
                return true;
            #check if pawn is only moving one field forwards
            }elseif(((($current_y-1 == $move_to_y) and ($this->color == "black"))  or  
                     (($current_y+1 == $move_to_y) and ($this->color == "white"))) and 
                       $current_x == $move_to_x and $this->check_target_square($chessboard,$move_to_x,$move_to_y))
                       {
                    return true;
            }
        #check if pawn is moving one field diagonal
        }elseif((((($current_y-1 == $move_to_y) and ($this->color == "black"))  or  
                 (($current_y+1 == $move_to_y) and ($this->color == "white"))) and 
                 (($current_x+1 == $move_to_x) or ($current_x-1 == $move_to_x))) and $this->check_target_square($chessboard,$move_to_x,$move_to_y)){
                return true;
        }
            #echo "<p class='error'>pawns can't move like that</p>";
            $_SESSION['error'] = "<p class='error'>pawns can't move like that</p>";
            return false;
        
    }

    public function check_target_square($chessboard,$move_to_x,$move_to_y):bool
    {
      # check if there is a piece on the move to square and if it is opposite color
      if(is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece') &&  $chessboard[$this->x][$this->y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
        return true;
       }elseif(!is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece')){
        return true;
       }else{
        return false;
       }
    }

}
