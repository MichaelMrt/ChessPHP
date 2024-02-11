<?php
require_once("chess_piece.php");

class Pawn extends ChessPiece implements JsonSerializable
{
    function check_move_legal($chessboard, $move_to_x, $move_to_y)
    {
        # Coordinates from the current Piece position
        $current_x = $this->x;
        $current_y = $this->y;
        
        #check if there is something on the move_to_field
        if($chessboard[$move_to_x][$move_to_y] == ""){
            #check if pawn is moving two fields
            if(twofiedsmoving){
                #check if pawn is on the right field to be allowed to move two fields
                if(pawnonrightfield?){
                    return true;
                }else{
                    return false;
                }
            #check if pawn is only moving one field
            }elseif(onefieldmoving){
                return true;
            }else{
                return false
            }
        #check if pawn is moving diagonal
        }elseif(){
            return true;
        }else{
            return false;
        }
    }

    public function jsonSerialize() {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'color' => $this->color,
        ];
    }
}
