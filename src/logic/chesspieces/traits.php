<?php
trait RookTrait
{
    public function check_legal_rookmove(mixed $chessboard, int $current_x, int $current_y, int $move_to_x, int $move_to_y): bool
    {
        # distance in squares
        $distance_x = sqrt(pow(($move_to_x-$current_x),2)); 
        $distance_y = sqrt(pow(($move_to_y-$current_y),2)); 

       # check if its horizontally
        if ($current_y == $move_to_y && $current_x != $move_to_x) {

            # check if moving to the right
            if ($current_x < $move_to_x) {
                for ($i = 1; $i <= $distance_x; $i++) {
                    # check if there is a piece on the way
                   if($distance_x==$i && $chessboard[$current_x + $i][$current_y] instanceof ChessPiece && $chessboard[$current_x+$i][$current_y]->get_color()!=$chessboard[$current_x][$current_y]->get_color()) {
                        return true;
                    }elseif(!$chessboard[$current_x + $i][$current_y] instanceof ChessPiece){
                       
                    }else{
                        return false;
                    }
                }
                # check if its moving to the left
            } elseif ($move_to_x < $current_x) {
                for ($i = 1; $i <= $distance_x; $i++) {
                    # check if there is a piece on the way
                    if($distance_x==$i && $chessboard[$current_x - $i][$current_y] instanceof ChessPiece && $chessboard[$current_x-$i][$current_y]->get_color()!=$chessboard[$current_x][$current_y]->get_color()) {
                        return true;
                    }elseif(!$chessboard[$current_x - $i][$current_y] instanceof ChessPiece){

                    }else{
                        return false;
                    }
                }
            }
            return true;
        }

        #check if its vertically
        if ($current_x == $move_to_x && $current_y != $move_to_y) {
            # check if moving up
            if($current_y<$move_to_y){
                
                for ($i=1; $i<= $distance_y; $i++) { 
                    # check if there is a piece on the way
                    if($distance_y==$i && $chessboard[$current_x][$current_y+$i] instanceof ChessPiece && $chessboard[$current_x][$current_y+$i]->get_color()!=$chessboard[$current_x][$current_y]->get_color()) {
                        return true;
                    }elseif(!$chessboard[$current_x][$current_y+$i] instanceof ChessPiece){

                    }else{
                        return false;
                    }
                }
                # check if moving down
            }elseif ($move_to_y<$current_y) {
                for ($i=1; $i<= $distance_y; $i++) { 
                    # check if there is a piece on the way
                    if($distance_y==$i && $chessboard[$current_x][$current_y-$i] instanceof ChessPiece && $chessboard[$current_x][$current_y-$i]->get_color()!=$chessboard[$current_x][$current_y]->get_color()) {
                        return true;
                    }elseif(!$chessboard[$current_x][$current_y-$i] instanceof ChessPiece){

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
    public function check_legal_bishopmove(mixed $chessboard, int $current_x, int $current_y, int $move_to_x, int $move_to_y):bool
    {
        # check if its diagonal move
        if(pow($current_x-$move_to_x,2) == pow($current_y-$move_to_y,2)){

            $distance = sqrt(pow($current_x-$move_to_x,2)); # distance in squares

            # top right - check if piece on the way
            if($current_x<$move_to_x && $current_y<$move_to_y){
                for ($i=1; $i <= $distance; $i++) { 
                    $field = $chessboard[$current_x+$i][$current_y+$i];
                    if($field instanceof ChessPiece){ // check if there is a piece on the way
                        if($i==$distance  && $field->get_color()!= $field->get_color()){ // target square can be taken
                            return true;
                        }else{
                            return false;
                        }
                    }elseif($i==$distance){ // no piece on the way
                        return true;
                    }
                }
            # top left
            }elseif($current_x>$move_to_x && $current_y<$move_to_y){
                for ($i=1; $i <= $distance; $i++) {
                    $field = $chessboard[$current_x-$i][$current_y+$i]; 
                    if($field instanceof ChessPiece){
                        if($i==$distance  && $field->get_color()!= $field->get_color()){
                            return true;
                        }else{
                            return false;
                        }
                    }elseif($i==$distance){
                        return true;
                    }
                }
            # bottom left
            }elseif($current_x>$move_to_x && $current_y>$move_to_y){
                for ($i=1; $i <= $distance; $i++) {
                    $field = $chessboard[$current_x-$i][$current_y-$i]; 
                    if($field instanceof ChessPiece){
                        if($i==$distance && $field->get_color()!= $field->get_color()){
                            return true;
                        }else{
                            return false;
                        }
                    }elseif($i==$distance){
                        return true;
                    }
                }
            # bottom right
            }elseif($current_x<$move_to_x && $current_y>$move_to_y){
                for ($i=1; $i <= $distance; $i++) {
                    $field = $chessboard[$current_x+$i][$current_y-$i]; 
                    if($field instanceof ChessPiece){
                        if($i==$distance  && $field->get_color()!= $field->get_color()){
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
