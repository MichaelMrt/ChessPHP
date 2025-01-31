<?php
require_once("chesspieces/pawn.php");
require_once("chesspieces/king.php");
require_once("chesspieces/queen.php");
require_once("chesspieces/bishop.php");
require_once("chesspieces/knight.php");
require_once("chesspieces/rook.php");
require_once("chessboard.php");
require_once("logic.php");

class Chessboard
{
    public mixed $chessboard;
    public $castling=false;

    function __construct()
    {   
        $this->chessboard = $this->create_board();
        $this->print_board($this->chessboard);
    }

    public function print_board($chessboard) : void
    {
       echo "<div class='board'>";
        $this->render_board($chessboard);
       echo "</div>";
       echo "<div id='ajax_response'></div>";
       echo "<div id='status'></div>";
       echo "<div id='white-queen-template' class='hidden'><img src='../images/chesspieces/white-queen.png' class='chesspiece'></div>";
       echo "<div id='black-queen-template' class='hidden'><img src='../images/chesspieces/black-queen.png' class='chesspiece'></div>";
    }   

    private function render_board($chessboard):void
    {   
        for ($row = 8; $row > 0; $row--) {
            for ($column = 1; $column < 9; $column++) {
                $piece = $chessboard[$column][$row];
                $background_color = $this->get_square_background_color($row, $column);
                $square_id = $column.$row;
                $chesspiece_icon = $this->get_chesspiece_icon($piece);
                $this->render_square($piece, $background_color, $square_id, $chesspiece_icon);
            }
        }
    }

    private function create_board(): mixed
    {   
        # Creates an 8x8 array
        for ($x = 1; $x < 9; $x++) {
            for ($y = 1; $y < 9; $y++) {
                $chessboard[$x][$y] = "";
            }
        }
        # place white pawns
        for ($x = 1; $x < 9; $x++) {
            $y = 2;
            $chessboard[$x][$y] = new Pawn("white", $x, $y);
        }

        # place black pawns
        for ($x = 1; $x < 9; $x++) {
            $y = 7;
            $chessboard[$x][$y] = new Pawn("black", $x, $y);
        }

        # place white king
        $chessboard[5][1] = new King("white", 5, 1);

        # place black king
        $chessboard[5][8] = new King("black", 5, 8);
       
        # place white queen
        $chessboard[4][1] = new Queen("white", 4, 1);

        # place black queen
        $chessboard[4][8] = new Queen("black", 4, 8);

        # place white bishops
        $chessboard[3][1] = new Bishop("white", 3, 1);
        $chessboard[6][1] = new Bishop("white", 6, 1);

        # place black bishops
        $chessboard[3][8] = new Bishop("black", 3, 8);
        $chessboard[6][8] = new Bishop("black", 6, 8);

        # place white knights
        $chessboard[2][1] = new Knight("white", 2, 1);
        $chessboard[7][1] = new Knight("white", 7, 1);

        # place black knights
        $chessboard[2][8] = new Knight("black", 2, 8);
        $chessboard[7][8] = new Knight("black", 7, 8);

        # place white rooks
        $chessboard[1][1] = new Rook("white", 1, 1);
        $chessboard[8][1] = new Rook("white", 8, 1);

        # place black rooks
        $chessboard[1][8] = new Rook("black", 1, 8);
        $chessboard[8][8] = new Rook("black", 8, 8);

        return $chessboard;
    }

    private function get_square_background_color(int $row, int $column):string
    {
        if (($row + $column) % 2 == 0) {
            $background_color = 'black';
        } else {
            $background_color = 'white';
        }

        return $background_color;
    }

    private function get_chesspiece_icon($piece):string
    {
        if($piece instanceof ChessPiece){
            $chesspiece_icon = $piece->get_icon();
        }else{
            $chesspiece_icon = '';
        }

        return $chesspiece_icon;
    }

    private function render_square(mixed $piece, string $background_color, string $square_id, string $chesspiece_icon):void
    {
        $encoded_piece = json_encode($piece);
        echo "<div id='$square_id' class='square $background_color' onclick='handle_SquareSelection(\"$square_id\",$encoded_piece)'>$chesspiece_icon</div>";

    }

    public function move(mixed $chessboard, int $current_x, int $current_y, int $move_to_x, int $move_to_y):mixed
    {
            $piece = $this->chessboard[$current_x][$current_y];
            $piece->update_position($move_to_x,$move_to_y);
            # Copy the piece to the new position
            $this->chessboard[$move_to_x][$move_to_y] = $chessboard[$current_x][$current_y];

            # Delete old piece position
            $this->chessboard[$current_x][$current_y] = "";

        return $this->chessboard;
    }

    public function test_move(mixed $chessboard, int $current_x, int $current_y, int $move_to_x, int $move_to_y):mixed
    {
            # Copy the piece to the new position
            $chessboard[$move_to_x][$move_to_y] = $chessboard[$current_x][$current_y];
             
            $piece = $chessboard[$move_to_x][$move_to_y];
            if($this->can_promote($piece, $move_to_y)){
                $chessboard[$move_to_x][$move_to_y] = new Queen($piece->get_color(), $move_to_x, $move_to_y);
            }

            # Delete old piece position
            $chessboard[$current_x][$current_y] = "";
            $chessboard[$current_x][$current_y] = "";
        return $chessboard;
    }


    public function get_board():mixed
    {
        return $this->chessboard;
    }

    public function remove_piece(int $x, int $y):void
    {
        $this->chessboard[$x][$y] = "";
    }

    public function can_promote($piece, $y){
        if($piece instanceof Pawn){
            if($piece->get_color() == "white" && $y==8){
                return true;
            }
            if($piece->get_color() == "black" && $y==1){
                return true;
            }
        }
        return false;
    }

    public function promote($x, $y, $color){
        $this->chessboard[$x][$y] = new Queen($color, $x, $y);
        return $this->chessboard;
    }
}
?>
