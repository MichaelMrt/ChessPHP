<?php
abstract class ChessPiece implements JsonSerializable
{
    protected String $color;
    protected String $type;
    protected int $x;
    protected int $y;
    protected String $icon;
    protected String $id;
    
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

            # Coordinates from the current Piece position
            $current_x = $this->x;
            $current_y = $this->y;

            if(is_a($chessboard[$current_x][$current_y],'King')){
                # check for castling
                if($this->color=='white' && $move_to_x==7 && $move_to_y==1){
                    if(!is_a($chessboard[6][1], 'Chesspiece') && !is_a($chessboard[7][1],'Chesspiece')){
                      if(is_a($chessboard[8][1], 'Rook')){
                        $chessboard = $chessboard[5][1]->castle_rightside($chessboard);
                      }
                    }
                }
            }


            # Copy the piece to the new position
            $chessboard[$move_to_x][$move_to_y] = $chessboard[$current_x][$current_y];

            # Delete old piece position
            $chessboard[$current_x][$current_y] = "";

            # Update position vars
            $this->x = $move_to_x;
            $this->y = $move_to_y;

            
        
        return $chessboard;
    }

    # Same as move function but doesnt mess with the class var x and y
    # used for checking positions in the future
    function test_move(mixed $chessboard, int $move_to_x, int $move_to_y):mixed
    {

            # Coordinates from the current Piece position
            $current_x = $this->x;
            $current_y = $this->y;

            # Copy the piece to the new position
            $chessboard[$move_to_x][$move_to_y] = $chessboard[$current_x][$current_y];

            # Delete old piece position
            $chessboard[$current_x][$current_y] = "";
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

    public function get_x():int
    {
        return $this->x;
    }

    public function get_y():int
    {
        return $this->y;
    }

    public function get_type():String
    {
        return $this->type;
    }
    # ---abstract methods---
    abstract function check_move_legal(mixed $chessboard, int $move_to_x, int $move_to_y):bool;

}