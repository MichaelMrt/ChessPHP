<?php
require_once("chesspieces/pawn.php");

class Logic
{
    function __construct()
    {
        echo "Logic constructed";
        new Pawn("w");
        $this->print_board($this->create_board());
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

    private function print_board($chessboard){
        for ($x = 1; $x < 9; $x++) {
            echo "<br>";
            for ($y = 0; $y < 9; $y++) {
                if($chessboard[$x][$y] == ""){
                    echo "[ ]";
                }
            }
        }
    }
}
