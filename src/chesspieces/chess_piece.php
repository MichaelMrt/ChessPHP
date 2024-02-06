<?php
abstract class ChessPiece
{
    protected $color;

    function __construct($color)
    {   
        $this->color = $color;

    }

    function get_color(){
        return  $this->color;
    }
}
