<?php
abstract class ChessPiece implements JsonSerializable
{
    protected String $color;
    protected String $type;
    protected int $x;
    protected int $y;
    protected String $icon;
    
    function __construct(String $color, int $x, int $y)
    {
        $this->color = $color;
        $this->x = $x;
        $this->y = $y;
    }

    function get_color():String
    {
        return  $this->color;
    }

    function move(mixed $chessboard, int $move_to_x, int $move_to_y):mixed
    {
        if($this->check_move_legal($chessboard, $move_to_x, $move_to_y)){
            # Coordinates from the current Piece position
            $current_x = $this->x;
            $current_y = $this->y;

            # Copy the piece to the new position
            $chessboard[$move_to_x][$move_to_y] = $chessboard[$current_x][$current_y];

            # Delete old piece position
            $chessboard[$current_x][$current_y] = "";
        }
        return $chessboard;
    }

    public function jsonSerialize():mixed {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'color' => $this->color,
            'type' => $this->type,
        ];
    }

    public function get_icon():String
    {
        return $this->icon;
    }

    # ---abstract methods---
    abstract function check_move_legal(mixed $chessboard, int $move_to_x, int $move_to_y):bool;

}