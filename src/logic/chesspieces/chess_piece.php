<?php
abstract class ChessPiece
{
    protected $color;
    protected $x;
    protected $y;
    function __construct($color,$x,$y)
    {   
        $this->color = $color;
        $this->x = $x;
        $this->y = $y;
    }

    function get_color(){
        return  $this->color;
    }
}
