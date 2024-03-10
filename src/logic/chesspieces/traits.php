<?php
trait RookTrait
{
    public function check_legal_rookmove(mixed $chessboard, int $current_x, int $current_y, int $move_to_x, int $move_to_y): bool
    {
        #check if its horizontally
        if ($current_y == $move_to_y && $current_x != $move_to_x) {
            # check if moving to the right
            if ($current_x < $move_to_x) {
                for ($i = $current_x; $i < $move_to_x; $i++) {

                    # check if there is a piece on the way
                    if (is_a($chessboard[$current_x + $i][$current_y], 'Chesspiece')) {
                        echo "Chesspiece on the way";
                        return false;
                    }
                }
            }
            return true;
        }

        #check if its vertically
        if ($current_x == $move_to_x && $current_y != $move_to_y) {
            # toDo: check if there is a piece on the way
            return true;
        }

        return false;
    }
}
