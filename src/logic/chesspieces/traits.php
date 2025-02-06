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
                for ($i = 1; $i <= $distance_x; $i++) {
                    # check if there is a piece on the way
                   if($distance_x==$i && $chessboard[$move->from_x + $i][$move->from_y] instanceof ChessPiece && $chessboard[$move->from_x+$i][$move->from_y]->get_color()!=$chessboard[$move->from_x][$move->from_y]->get_color()) {
                        return true;
                    }elseif(!$chessboard[$move->from_x + $i][$move->from_y] instanceof ChessPiece){
                       
                    }else{
                        return false;
                    }
                }
                # check if its moving to the left
            } elseif ($move->to_x < $move->from_x) {
                for ($i = 1; $i <= $distance_x; $i++) {
                    # check if there is a piece on the way
                    if($distance_x==$i && $chessboard[$move->from_x - $i][$move->from_y] instanceof ChessPiece && $chessboard[$move->from_x-$i][$move->from_y]->get_color()!=$chessboard[$move->from_x][$move->from_y]->get_color()) {
                        return true;
                    }elseif(!$chessboard[$move->from_x - $i][$move->from_y] instanceof ChessPiece){

                    }else{
                        return false;
                    }
                }
            }
            return true;
        }

        #check if its vertically
        if ($move->from_x == $move->to_x && $move->from_y != $move->to_y) {
            # check if moving up
            if($move->from_y<$move->to_y){
                
                for ($i=1; $i<= $distance_y; $i++) { 
                    # check if there is a piece on the way
                    if($distance_y==$i && $chessboard[$move->from_x][$move->from_y+$i] instanceof ChessPiece && $chessboard[$move->from_x][$move->from_y+$i]->get_color()!=$chessboard[$move->from_x][$move->from_y]->get_color()) {
                        return true;
                    }elseif(!$chessboard[$move->from_x][$move->from_y+$i] instanceof ChessPiece){

                    }else{
                        return false;
                    }
                }
                # check if moving down
            }elseif ($move->to_y<$move->from_y) {
                for ($i=1; $i<= $distance_y; $i++) { 
                    # check if there is a piece on the way
                    if($distance_y==$i && $chessboard[$move->from_x][$move->from_y-$i] instanceof ChessPiece && $chessboard[$move->from_x][$move->from_y-$i]->get_color()!=$chessboard[$move->from_x][$move->from_y]->get_color()) {
                        return true;
                    }elseif(!$chessboard[$move->from_x][$move->from_y-$i] instanceof ChessPiece){

                    }else{
                        return false;
                    }
                }
            }
            return true;
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
            $move_from_field = $chessboard[$move->from_x][$move->from_y];


            # top right - check if piece on the way
            if($move->from_x<$move->to_x && $move->from_y<$move->to_y){
                for ($i=1; $i <= $distance; $i++) { 
                    $move_to_field = $chessboard[$move->from_x+$i][$move->from_y+$i];
                    if($move_to_field instanceof ChessPiece){ // check if there is a piece on the way
                        if($i==$distance  && $move_to_field->get_color()!= $move_from_field->get_color()){ // target square can be taken
                            return true;
                        }else{
                            return false;
                        }
                    }elseif($i==$distance){ // no piece on the way
                        return true;
                    }
                }
            # top left
            }elseif($move->from_x>$move->to_x && $move->from_y<$move->to_y){
                for ($i=1; $i <= $distance; $i++) {
                    $move_to_field = $chessboard[$move->from_x-$i][$move->from_y+$i]; 
                    if($move_to_field instanceof ChessPiece){
                        if($i==$distance  && $move_from_field->get_color()!= $move_to_field->get_color()){
                            return true;
                        }else{
                            return false;
                        }
                    }elseif($i==$distance){
                        return true;
                    }
                }
            # bottom left
            }elseif($move->from_x>$move->to_x && $move->from_y>$move->to_y){
                for ($i=1; $i <= $distance; $i++) {
                    $move_to_field = $chessboard[$move->from_x-$i][$move->from_y-$i]; 
                    if($move_to_field instanceof ChessPiece){
                        if($i==$distance && $move_from_field->get_color()!= $move_to_field->get_color()){
                            return true;
                        }else{
                            return false;
                        }
                    }elseif($i==$distance){
                        return true;
                    }
                }
            # bottom right
            }elseif($move->from_x<$move->to_x && $move->from_y>$move->to_y){
                for ($i=1; $i <= $distance; $i++) {
                    $move_to_field = $chessboard[$move->from_x+$i][$move->from_y-$i]; 
                    if($move_to_field instanceof ChessPiece){
                        if($i==$distance  && $move_from_field->get_color()!= $move_to_field->get_color()){
                            return true;
                        }else{
                            return false;
                        }
                    }elseif($i==$distance){
                        return true;
                    }
                }
            }
            return false;
        }
    return false; # not a diagonal move
    }

}
