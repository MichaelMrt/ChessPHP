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
    protected $weight;
    
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

    public function jsonSerialize():mixed {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'color' => $this->color,
            'type' => $this->type,
        ];
    }

    public function update_position($pos_x, $pos_y){
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

    protected function check_target_square($chessboard, $move_to_x, $move_to_y):bool
    {
    if($chessboard[$move_to_x][$move_to_y]==""){
        return true;
    }
    if($chessboard[$this->x][$this->y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
        return true;
      }else{
        return false;
      }
    }
    # ---abstract methods---
    abstract function check_move_legal(mixed $chessboard, int $move_to_x, int $move_to_y):bool;

}