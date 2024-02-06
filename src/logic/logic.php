<?php
require_once("chesspieces/pawn.php");

class Logic
{
    function __construct()
    {
        echo "Logic constructed";
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
        #place white pawns
        for ($x = 1; $x < 9; $x++) {
            $y = 7;
            $chessboard[$x][$y] = new Pawn("white");
        }

        #place black pawns
        for ($x = 1; $x < 9; $x++) {
            $y = 2;
            $chessboard[$x][$y] = new Pawn("black");
        }

        return $chessboard;
    }

    private function print_board($chessboard)
    {
        echo "<div class='square-container center'>";
        for ($y = 1; $y < 9; $y++) {
            for ($x = 1; $x < 9; $x++) {
                if ($chessboard[$x][$y] == "") { #No piece in that square
                    echo "<div class='square'>x</div>";
                } elseif (is_a($chessboard[$x][$y], 'Pawn')) { # Pawn in that square
                    if ($chessboard[$x][$y]->get_color() == "white") { # White Pawn
                        echo "<div class='square'>w</div>";
                    }
                    if ($chessboard[$x][$y]->get_color() == "black") { # Black Pawn
                        echo "<div class='square'>b</div>";
                    }
                }
            }
        }
        echo "</div>";
    }
}
