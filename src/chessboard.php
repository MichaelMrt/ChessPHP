<?php
class Chessboard
{
    function __construct()
    {   
        $this->print_board();
    }

    function print_board() : void
    {
        echo "<div class='board'>";
        // Generate the board
        for ($row = 0; $row < 8; $row++) {
            for ($column = 0; $column < 8; $column++) {
                // Background color
                if (($row + $column) % 2 == 0) {
                    echo '<div class="field white"></div>';
                } else {
                    echo '<div class="field black"></div>';
                }
            }
        }
       echo "</div>";
    }   
    }
?>
