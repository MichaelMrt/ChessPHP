<?php
trait RookTrait
{
    public function check_legal_rookmove(mixed $chessboard, Move $move): bool
    {   
        # distance in squares
        $distance_x = (int) sqrt(pow(($move->to_x-$move->from_x),2)); 
        $distance_y = (int) sqrt(pow(($move->to_y-$move->from_y),2)); 

       # check if its horizontally
        if ($move->from_y == $move->to_y && $move->from_x != $move->to_x) {
            # check if moving to the right
            if ($move->from_x < $move->to_x) {
                if(check_direction($chessboard, $move, $distance_x, "+x")){
                    return true;
                }else{
                    return false;
                }  
            } elseif ($move->to_x < $move->from_x) { # check if it a move to the left
                if(check_direction($chessboard, $move, $distance_x, "-x")){
                    return true;
                }else{
                    return false;
                }
            }
        }

        #check if its vertically
        if ($move->from_x == $move->to_x && $move->from_y != $move->to_y) {
            # check if moving up
            if($move->from_y<$move->to_y){
                if(check_direction($chessboard, $move, $distance_y, "+y")){
                    return true;
                }else{
                    return false;
                }
                # check if moving down
            }elseif ($move->to_y<$move->from_y) {
                if(check_direction($chessboard, $move, $distance_y, "-y")){
                    return true;
                }else{
                    return false;
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

            $distance = (int) sqrt(pow($move->from_x-$move->to_x,2)); # distance in squares
            # top right 
            if($move->from_x<$move->to_x && $move->from_y<$move->to_y){
                if(check_direction($chessboard, $move, $distance, "++")){
                    return true;
                }else{
                    return false;
                } 
            # top left
            }elseif($move->from_x>$move->to_x && $move->from_y<$move->to_y){
                if(check_direction($chessboard, $move, $distance, "-+")){
                    return true;
                }else{
                    return false;
                }
            # bottom left
            }elseif($move->from_x>$move->to_x && $move->from_y>$move->to_y){
                if(check_direction($chessboard, $move, $distance, "--")){
                    return true;
                }else{
                    return false;
                }
            # bottom right
            }elseif($move->from_x<$move->to_x && $move->from_y>$move->to_y){
                if(check_direction($chessboard, $move, $distance, "+-")){
                    return true;
                }else{
                    return false;
                }
            }
        }
    return false; # not a diagonal move
    }
}

function piece_in_way(mixed $move_to_field, int $index, int $distance):bool
{
    if($move_to_field instanceof ChessPiece){ // check if there is a piece on the way
        if($index==$distance){ // target square can be taken
            return false;
        }else{ // piece on the way and not last move
            return true;
        }
    }
    return false;
}

function check_direction(mixed $chessboard, Move $move, int $distance, String $direction): bool
{
    for ($i=1; $i <= $distance; $i++) { 
        $move_to_field = get_move_to_field($chessboard, $move, $direction, $i);

        if(piece_in_way($move_to_field, $i, $distance)){
            return false;
        }
    }
    return true;
}

function get_move_to_field(mixed $chessboard, Move $move, String $direction, int $index):mixed
{
    if($direction == "+x"){
        $move_to_field = $chessboard[$move->from_x + $index][$move->from_y];
    }elseif($direction == "+y"){
        $move_to_field = $chessboard[$move->from_x][$move->from_y + $index];
    }elseif($direction == "-x"){
        $move_to_field = $chessboard[$move->from_x - $index][$move->from_y];
    }elseif($direction == "-y"){
        $move_to_field = $chessboard[$move->from_x][$move->from_y - $index];
    }elseif($direction == "++"){
        $move_to_field = $chessboard[$move->from_x + $index][$move->from_y + $index];
    }elseif($direction == "--"){
        $move_to_field = $chessboard[$move->from_x - $index][$move->from_y - $index];
    }elseif($direction == "+-"){
        $move_to_field = $chessboard[$move->from_x + $index][$move->from_y - $index];
    }elseif($direction == "-+"){
        $move_to_field = $chessboard[$move->from_x - $index][$move->from_y + $index];
    }else{
        throw new Exception("Invalid direction");
    }
    return $move_to_field;
}
