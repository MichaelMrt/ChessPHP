<?php
abstract class ChessPiece
{
    protected $color;

    function __construct($color)
    {
        $this->$color = $color;
        echo "<br>ChessPiece constructed";
    }
}
