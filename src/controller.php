<?php
set_time_limit(10);
require_once("logic/logic.php");
require_once("ui/ui.php");

class Controller
{

    function __construct()
    {

        $logic = new Logic();
        $ui = new Ui();

        $logic->activate_inputs();
        $ui->print_board($logic->get_board());

        if(isset($_SESSION['pickedsquare'])){
            #print_r($_SESSION['pickedsquare']);
            print("x:".substr($_SESSION['pickedsquare'],0,1)."<br>");
            print("y:".substr($_SESSION['pickedsquare'],1,2));
        }
       
    }
}
