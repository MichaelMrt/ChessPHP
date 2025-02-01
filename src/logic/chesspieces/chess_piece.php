<?php
abstract class ChessPiece implements JsonSerializable
{
    protected String $color;
    protected String $type;
    protected int $x;
    protected int $y;
    protected String $icon;
    protected String $id;
    protected bool $has_moved = false;
    protected int $weight;
    
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

    public function jsonSerialize():mixed 
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'color' => $this->color,
            'type' => $this->type,
        ];
    }

    public function update_position(int $pos_x, int $pos_y):void
    {
        $this->x = $pos_x;
        $this->y = $pos_y;
        $this->has_moved = true;
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

    public function get_weight():int
    {
        return $this->weight;
    }

    public function get_has_moved_status():bool
    {
        return $this->has_moved;
    }

    protected function check_target_square(mixed $chessboard,int $current_x, int $current_y, int $move_to_x, int $move_to_y):bool
    {
        if($chessboard[$move_to_x][$move_to_y]==""){
            return true;
        }
        if($chessboard[$current_x][$current_y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
            return true;
        }else{
            return false;
        }
    }
    # ---abstract methods---
    abstract function check_move_legal(mixed $chessboard, int $current_x, int $current_y, int $move_to_x, int $move_to_y):bool;

}