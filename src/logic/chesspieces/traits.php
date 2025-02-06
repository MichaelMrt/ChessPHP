<?php
trait RookTrait
{
    public function check_legal_rookmove(mixed $chessboard, Move $move): bool
    {
        # distance in squares
        $distance_x = sqrt(pow(($move->to_x-$move->from_x),2)); 
        $distance_y = sqrt(pow(($move->to_y-$move->from_y),2)); 

       # check if its horizontally
        if ($move->from_y == $move->to_y && $move->from_x != $move->to_x) {

            # check if moving to the right
            if ($move->from_x < $move->to_x) {
                if(check_direction($chessboard, $move, $distance_x, "positiv", "x")){
                    return true;
                }
                
            } elseif ($move->to_x < $move->from_x) { # check if it a move to the left
                if(check_direction($chessboard, $move, $distance_x, "negativ", "x")){
                    return true;
                }
            }
        }

        #check if its vertically
        if ($move->from_x == $move->to_x && $move->from_y != $move->to_y) {
            # check if moving up
            if($move->from_y<$move->to_y){
                if(check_direction($chessboard, $move, $distance_y, "positiv", "y")){
                    return true;
                }
                # check if moving down
            }elseif ($move->to_y<$move->from_y) {
                if(check_direction($chessboard, $move, $distance_y, "negativ", "y")){
                    return true;
                }
            }
            return false;
        }
        return false;
    }



}

trait BishopTrait{
    public function check_legal_bishopmove(mixed $chessboard, Move $move):bool
    {
        # check if its diagonal move
        if(pow($move->from_x-$move->to_x,2) == pow($move->from_y-$move->to_y,2)){

            $distance = sqrt(pow($move->from_x-$move->to_x,2)); # distance in squares

            # top right - check if piece on the way
            if($move->from_x<$move->to_x && $move->from_y<$move->to_y){
                for ($i=1; $i <= $distance; $i++) { 
                    $move_to_field = $chessboard[$move->from_x+$i][$move->from_y+$i];
                    if(no_piece_in_way($move_to_field, $i, $distance)){
                        return true;
                    }else{
                        return false;
                    }
                }
                
            # top left
            }elseif($move->from_x>$move->to_x && $move->from_y<$move->to_y){
                for ($i=1; $i <= $distance; $i++) {
                    $move_to_field = $chessboard[$move->from_x-$i][$move->from_y+$i]; 
                    if(no_piece_in_way($move_to_field, $i, $distance)){
                        return true;
                    }else{
                        return false;
                    }
                }
            # bottom left
            }elseif($move->from_x>$move->to_x && $move->from_y>$move->to_y){
                for ($i=1; $i <= $distance; $i++) {
                    $move_to_field = $chessboard[$move->from_x-$i][$move->from_y-$i]; 
                    if(no_piece_in_way($move_to_field, $i, $distance)){
                        return true;
                    }else{
                        return false;
                    }
                }
                return false;
            # bottom right
            }elseif($move->from_x<$move->to_x && $move->from_y>$move->to_y){
                for ($i=1; $i <= $distance; $i++) {
                    $move_to_field = $chessboard[$move->from_x+$i][$move->from_y-$i]; 
                    if(no_piece_in_way($move_to_field, $i, $distance)){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
        }
    return false; # not a diagonal move
    }

}

function no_piece_in_way(mixed $move_to_field, int $index, int $distance):bool
{
    if($move_to_field instanceof ChessPiece){ // check if there is a piece on the way
        if($index==$distance){ // target square can be taken
            return true;
        }else{ // piece on the way and not last move
            return false;
        }
    }elseif($index==$distance){ // no piece on the way
        return true;
    } 
    return true;
}

function check_direction($chessboard, Move $move, int $distance, String $sign, String $direction): bool
{
    for ($i = 1; $i <= $distance; $i++) {
        if($sign == "negativ"){
            $i = -$i;
        }

        if($direction == "x"){
            $move_to_field = $chessboard[$move->from_x + $i][$move->from_y];
        }elseif($direction == "y"){
            $move_to_field = $chessboard[$move->from_x][$move->from_y + $i];
        }
        
        if(no_piece_in_way($move_to_field, $i, $distance)){
            
        }else{
            return false;
        }
        
        return true;
    }
}
