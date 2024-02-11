<?php
require_once("chess_piece.php");

class Pawn extends ChessPiece implements JsonSerializable
{
    function check_move_legal($chessboard, $move_to_x, $move_to_y):bool
    {
        # Coordinates from the current Piece position
        $current_x = $this->x;
        $current_y = $this->y;
        
        #check if there is something on the move_to_field
        if($chessboard[$move_to_x][$move_to_y] == ""){
            #check if pawn is moving two fields forwards, check which color is moving, according to the color check if it is the start position of the pawn, x position should stay the same
            if(((($current_y-2 == $move_to_y) and ($this->color == "black") and ($current_y == 7))  or  (($current_y+2 == $move_to_y) and ($this->color == "white") and ($current_y == 2))) and $current_x == $move_to_x){
                return true;
            #check if pawn is only moving one field forwards
            }elseif(((($current_y-1 == $move_to_y) and ($this->color == "black"))  or  (($current_y+1 == $move_to_y) and ($this->color == "white"))) and $current_x == $move_to_x){
                return true;
            }else{
                return false;
            }
        #check if pawn is moving one field diagonal
        }elseif(((($current_y-1 == $move_to_y) and ($this->color == "black"))  or  (($current_y+1 == $move_to_y) and ($this->color == "white"))) and (($current_x+1 == $move_to_x) or ($current_x-1 == $move_to_x))){
            return true;
        }else{
            return false;
        }
    }

    public function jsonSerialize():mixed {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'color' => $this->color,
        ];
    }
}
