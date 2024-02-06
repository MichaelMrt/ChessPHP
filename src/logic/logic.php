<?php
require_once("chesspieces/pawn.php");

class Logic
{
    function __construct()
    {
        echo "Logic constructed";
        new Pawn("w");
    }
}
