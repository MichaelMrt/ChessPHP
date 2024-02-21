<?php
require_once("chess_piece.php");

class Pawn extends ChessPiece
{   

    function __construct(String $color, int $x, int $y)
    {
      parent::__construct($color, $x, $y);
      $this->type = 'bishop';
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
                       $current_x == $move_to_x){
                    return true;
            }
        #check if pawn is moving one field diagonal
        }elseif(((($current_y-1 == $move_to_y) and ($this->color == "black"))  or  
                 (($current_y+1 == $move_to_y) and ($this->color == "white"))) and 
                 (($current_x+1 == $move_to_x) or ($current_x-1 == $move_to_x))){
                return true;
        }
            echo "<p class='error'>pawns can't move like that</p>";
            return false;
        
    }


    function getNextPiece(mixed $chessboard, int $current_x, int $current_y, int $moving_to_x,  int $moving_to_y): mixed{
        if($moving_to_x=="+" &&  $moving_to_y=="+"){

            for($i=0; $current_y+$i<8 && $current_x+$i<8; $i++){
                    if($chessboard[$current_x+$i][$current_y+$i] != null){
                        $coordinates[0] = $current_x+$i;
                        $coordinates[1] = $current_y+$i;
                        return $coordinates;
                    }
            }
            return null;
        }

        if($moving_to_x=="-" &&  $moving_to_y=="+"){
            for($i=0; $current_y+$i<8 && $current_x-$i>-1; $i++){
                $lookingat = $current_x+$i;
                if(array_key_exists("$lookingat", $chessboard)){
                    if($chessboard[$lookingat] != null){
                        return $chessboard[$lookingat];
                    }
                }
            }
            return null;
        }

        if($moving_to_x=="+" &&  $moving_to_y=="-"){
            for($i=0; $current_y-$i>-1 && $current_x+$i<8; $i++){
                $lookingat = $current_x+$i;
                if(array_key_exists("$lookingat", $chessboard)){
                    if($chessboard[$lookingat] != null){
                        return $chessboard[$lookingat];
                    }
                }
            }
            return null;
        }

        if($moving_to_x=="-" &&  $moving_to_y=="-"){
            for($i=0; $current_y-$i>-1 && $current_x-$i>-1; $i++){
                $lookingat = $current_x+$i;
                if(array_key_exists("$lookingat", $chessboard)){
                    if($chessboard[$lookingat] != null){
                        return $chessboard[$lookingat];
                    }
                }
            }
            return null;
        }
    }
}
