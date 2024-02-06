<?php
require_once("chesspieces/pawn.php");

class Logic
{
    function __construct()
    {   
        $chessboard = $this->create_board();
        $this->print_board($chessboard);
        $chessboard = $chessboard[2][2]->move($chessboard);
        print_r($chessboard[1][1]);
        $this->print_board($chessboard);
    }

    private function create_board()
    {
        #Creates an 8x8 array
        for ($x = 1; $x < 9; $x++) {
            for ($y = 1; $y < 9; $y++) {
                $chessboard[$x][$y] = "";
            }
        }
        #place white pawns
        for ($x = 1; $x < 9; $x++) {
            $y = 7;
            $chessboard[$x][$y] = new Pawn("white",$x,$y);
        }

        #place black pawns
        for ($x = 1; $x < 9; $x++) {
            $y = 2;
            $chessboard[$x][$y] = new Pawn("black",$x,$y);
        }

        return $chessboard;
    }

    # prints the board by checking each array/square content, temporary output for working in logic
    # and setting up the structure
    private function print_board($chessboard)
    {
        echo "<div class='square-container center'>";
        for ($y = 1; $y < 9; $y++) {
            for ($x = 1; $x < 9; $x++) {
                if ($chessboard[$x][$y] == "") { #No piece in that square
                    echo "<div class='square'> </div>";
                } elseif (is_a($chessboard[$x][$y], 'Pawn')) { # Pawn in that square
                    if ($chessboard[$x][$y]->get_color() == "white") { # White Pawn
                        echo "<div class='square'>wp</div>";
                    }
                    if ($chessboard[$x][$y]->get_color() == "black") { # Black Pawn
                        echo "<div class='square'>bp</div>";
                    }
                }
            }
        }
        echo "</div>";
    }

        #just for testing purpose
        function debug_output_board($chessboard){
            echo "<pre>";
            print_r($chessboard);
            echo "</pre>";
        }

        function get_board(){
            global $chessboard;
            return $chessboard;
        }
}
