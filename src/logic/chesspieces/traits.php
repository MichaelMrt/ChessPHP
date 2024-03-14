<?php
trait RookTrait
{
    public function check_legal_rookmove(mixed $chessboard, int $current_x, int $current_y, int $move_to_x, int $move_to_y): bool
    {
        # distance in squares
        $distance_x = sqrt(pow(($move_to_x-$current_x),2)); 
        $distance_y = sqrt(pow(($move_to_y-$current_y),2)); 

        #check if its horizontally
        if ($current_y == $move_to_y && $current_x != $move_to_x) {

            # check if moving to the right
            if ($current_x < $move_to_x) {
                for ($i = 1; $i <= $distance_x; $i++) {
                    # check if there is a piece on the way
                   if($distance_x==$i && is_a($chessboard[$current_x + $i][$current_y], 'Chesspiece') && $chessboard[$current_x+$i][$current_y]->get_color()!=$chessboard[$current_x][$current_y]->get_color()) {
                        return true;
                    }elseif(!is_a($chessboard[$current_x + $i][$current_y], 'Chesspiece')){
                       
                    }else{
                        return false;
                    }
                }
                # check if its moving to the left
            } elseif ($move_to_x < $current_x) {
                for ($i = 1; $i <= $distance_x; $i++) {
                    # check if there is a piece on the way
                    if($distance_x==$i && is_a($chessboard[$current_x - $i][$current_y], 'Chesspiece') && $chessboard[$current_x+$i][$current_y]->get_color()!=$chessboard[$current_x][$current_y]->get_color()) {
                        return true;
                    }elseif(!is_a($chessboard[$current_x - $i][$current_y], 'Chesspiece')){

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
                    if($distance_y==$i && is_a($chessboard[$current_x][$current_y+$i], 'Chesspiece') && $chessboard[$current_x][$current_y+$i]->get_color()!=$chessboard[$current_x][$current_y]->get_color()) {
                        return true;
                    }elseif(!is_a($chessboard[$current_x][$current_y+$i], 'Chesspiece')){

                    }else{
                        return false;
                    }
                }
                # check if moving down
            }elseif ($move_to_y<$current_y) {
                for ($i=1; $i<= $distance_y; $i++) { 
                    
                    # check if there is a piece on the way
                    if($distance_x==$i && is_a($chessboard[$current_x][$current_y-$i], 'Chesspiece') && $chessboard[$current_x+$i][$current_y]->get_color()!=$chessboard[$current_x][$current_y]->get_color()) {
                        return true;
                    }elseif(!is_a($chessboard[$current_x][$current_y-$i], 'Chesspiece')){

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
                    if(is_a($chessboard[$current_x+$i][$current_y+$i],'Chesspiece')){
                        if($i==$distance  && $chessboard[$current_x+$i][$current_y+$i]->get_color()!= $chessboard[$current_x][$current_y]->get_color()){
                            return true;
                        }else{
                            return false;
                        }
                    }elseif($i==$distance && !is_a($chessboard[$current_x+$i][$current_y+$i],'Chesspiece')){
                        return true;
                    }
                }
            # top left
            }elseif($current_x>$move_to_x && $current_y<$move_to_y){
                for ($i=1; $i <= $distance; $i++) { 
                    if(is_a($chessboard[$current_x-$i][$current_y+$i],'Chesspiece')){
                        if($i==$distance  && $chessboard[$current_x-$i][$current_y+$i]->get_color()!= $chessboard[$current_x][$current_y]->get_color()){
                            return true;
                        }else{
                            return false;
                        }
                    }elseif($i==$distance && !is_a($chessboard[$current_x-$i][$current_y+$i],'Chesspiece')){
                        return true;
                    }
                }
            # bottom left
            }elseif($current_x>$move_to_x && $current_y>$move_to_y){
                for ($i=1; $i <= $distance; $i++) { 
                    if(is_a($chessboard[$current_x-$i][$current_y-$i],'Chesspiece')){
                        if($i==$distance && $chessboard[$current_x-$i][$current_y-$i]->get_color()!= $chessboard[$current_x][$current_y]->get_color()){
                            return true;
                        }else{
                            return false;
                        }
                    }elseif($i==$distance && !is_a($chessboard[$current_x-$i][$current_y-$i],'Chesspiece')){
                        return true;
                    }
                }
            # bottom right
            }elseif($current_x<$move_to_x && $current_y>$move_to_y){
                for ($i=1; $i <= $distance; $i++) { 
                    if(is_a($chessboard[$current_x+$i][$current_y-$i],'Chesspiece')){
                        if($i==$distance  && $chessboard[$current_x+$i][$current_y-$i]->get_color()!= $chessboard[$current_x][$current_y]->get_color()){
                            return true;
                        }else{
                            return false;
                        }
                    }elseif($i==$distance && !is_a($chessboard[$current_x+$i][$current_y-$i],'Chesspiece')){
                        return true;
                    }
                }
            }
            return false;
        }
    return false; # not a diagonal move
    }

}
