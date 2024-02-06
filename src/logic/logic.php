<?php
require_once("chesspieces/pawn.php");

class Logic
{
    function __construct()
    {
        echo "Logic constructed";
        new Pawn("w");
        echo "<pre>";
        print_r($this->create_board());
        echo "</pre>";
    }

    private function create_board()
    {
        #Creates an 8x8 array
        for ($x = 1; $x < 9; $x++) {
            for ($y = 0; $y < 9; $y++) {
                $chessboard[$x][$y] = "";
            }
        }
        return $chessboard;
    }
}
