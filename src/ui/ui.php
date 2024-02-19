<?php
class Ui
{
    function __construct()
    {
    }

    # prints the board by checking each array/square content, temporary output for working in logic
    # and setting up the structure
    public function print_board(mixed $chessboard): void
    {
        $boardnumeration = 8;

        echo "<div class='square-container center'>";
       
        for ($y = 8; $y > 0; $y--) {

            for ($x = 1; $x < 9; $x++) {
                # set color based on position
                if ((($y % 2 == 1 && $x % 2 == 1) || ($y % 2 == 0 && $x % 2 == 0))) {
                    $area_color = "brown";
                } else {
                    $area_color = "white";
                }
                if ($chessboard[$x][$y] == "") { #No piece in that square
                    echo "<div class='square $area_color'> </div>";
                } elseif (is_a($chessboard[$x][$y], 'Pawn')) { # Pawn in that square
                    if ($chessboard[$x][$y]->get_color() == "white") { # White Pawn
                        echo "<div class='square $area_color'><input class='square' type='submit' name='b' value='a'></input></div>";
                    }
                    if ($chessboard[$x][$y]->get_color() == "black") { # Black Pawn
                        echo "<div class='square $area_color'><img src='../images/chesspieces/black-pawn.png' class='chesspiece'></div>";
                    }
                }
                #... ToDo check for more pieces

                # bordnumeration right side
                if ($x == 8) {
                    echo "<div class='square'>$boardnumeration</div>";
                    $boardnumeration--;
                }
            }
        }

        #boardnumeration on the bottom
        for ($boardnumeration = 1; $boardnumeration < 9; $boardnumeration++) {
            echo "<div class='square'>$boardnumeration</div>";
        }
        echo "</div>";
        echo "<div><input type='submit' class='submit' value='Submit move'></div>";
        echo "</form>";
    }
}
