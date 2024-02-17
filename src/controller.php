<?php
set_time_limit(10);
require_once("logic/logic.php");

class Controller
{

    function __construct()
    {
        echo "<h1>Controller</h1>";
        echo "<h3>bp=black pawn<br>wp=whitepawn</h3>";

        $logic = new Logic();
    }
}
