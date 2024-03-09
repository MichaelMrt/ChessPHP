<?php
trait RookTrait{
    public function check_legal_rookmove(int $current_x, int $current_y, int $move_to_x, int $move_to_y):bool
    {
        #check if its horizontally
        if($current_y==$move_to_y && $current_x != $move_to_x){
            # toDo: check if there is a piece on the way
            return true;
        }

        #check if its vertically
        if($current_x==$move_to_x && $current_y != $move_to_y){
            # toDo: check if there is a piece on the way
            return true;
        }

        return false;
    }
}
?>