<?php
abstract class ChessPiece
{
    protected $color;

    function __construct($color)
    {
        $color = $this->$color;
    }
}
