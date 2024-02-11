<?php
abstract class ChessPiece
{
    protected $color;
    protected $x;
    protected $y;
    function __construct($color, $x, $y)
    {
        $this->color = $color;
        $this->x = $x;
        $this->y = $y;
    }

    function get_color()
    {
        return  $this->color;
    }

    abstract function check_move_legal($chessboard, $move_to_x, $move_to_y);

    function move($chessboard, $move_to_x, $move_to_y)
    {
        if($this->check_move_legal($chessboard, $move_to_x, $move_to_y)){
            # Coordinates from the current Piece position
            $current_x = $this->x;
            $current_y = $this->y;

            # Copy the piece to the new position
            $chessboard[$move_to_x][$move_to_y] = $chessboard[$current_x][$current_y];

            # Delete old piece position
            $chessboard[$current_x][$current_y] = "";
            return $chessboard;
        }
    }
}
