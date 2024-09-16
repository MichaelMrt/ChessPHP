<?php
require_once("chesspieces/pawn.php");
require_once("chesspieces/king.php");
require_once("chesspieces/queen.php");
require_once("chesspieces/bishop.php");
require_once("chesspieces/knight.php");
require_once("chesspieces/rook.php");
require_once("chessboard.php");

class Chessboard
{
    private bool $whites_turn=true;
    public mixed $chessboard;
    function __construct()
    {   
        $this->chessboard = $this->create_board();
        $this->print_board();
        echo "<div id='ajax_response'></div>";
    }

    function print_board() : void
    {
       echo "<div class='board'>";
        $this->render_board();
       echo "</div>";
    }   

    private function render_board():void
    {   
        $column_characters = ['boardnumeration','a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        for ($row = 8; $row > 0; $row--) {
            for ($column = 1; $column < 9; $column++) {
                $piece = $this->chessboard[$column][$row];
                $background_color = $this->get_square_background_color($row, $column);
                $square_id = $column_characters[$column].$row;
                $chesspiece_icon = $this->get_chesspiece_icon($piece);
                $this->render_square($piece, $background_color, $square_id, $chesspiece_icon);
            }
        }
    }

    private function create_board(): mixed
    {   
        $this->whites_turn = true;
        #Creates an 8x8 array
        for ($x = 1; $x < 9; $x++) {
            for ($y = 1; $y < 9; $y++) {
                $chessboard[$x][$y] = "";
            }
        }
        #place white pawns
        for ($x = 1; $x < 9; $x++) {
            $y = 2;
            $chessboard[$x][$y] = new Pawn("white", $x, $y);
        }

        #place black pawns
        for ($x = 1; $x < 9; $x++) {
            $y = 7;
            $chessboard[$x][$y] = new Pawn("black", $x, $y);
        }

        #place white king
        $chessboard[5][1] = new King("white", 5, 1);

        #place black king
        $chessboard[5][8] = new King("black", 5, 8);
       
        #place white queen
        $chessboard[4][1] = new Queen("white", 4, 1);

        #place black queen
        $chessboard[4][8] = new Queen("black", 4, 8);

        #place white bishop
        $chessboard[3][1] = new Bishop("white", 3, 1);
        $chessboard[6][1] = new Bishop("white", 6, 1);

        #place black bishop
        $chessboard[3][8] = new Bishop("black", 3, 8);
        $chessboard[6][8] = new Bishop("black", 6, 8);

        #place white knight
        $chessboard[2][1] = new Knight("white", 2, 1);
        $chessboard[7][1] = new Knight("white", 7, 1);

        #place black knight
        $chessboard[2][8] = new Knight("black", 2, 8);
        $chessboard[7][8] = new Knight("black", 7, 8);

        #place white rook
        $chessboard[1][1] = new Rook("white", 1, 1);
        $chessboard[8][1] = new Rook("white", 8, 1);

        #place black rook
        $chessboard[1][8] = new Rook("black", 1, 8);
        $chessboard[8][8] = new Rook("black", 8, 8);

        return $chessboard;
    }

    private function get_square_background_color(int $row, int $column):string
    {
        if (($row + $column) % 2 == 0) {
            $background_color = 'white';
        } else {
            $background_color = 'black';
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

    public function get_board():mixed
    {
        return $this->chessboard;
    }
}
?>
