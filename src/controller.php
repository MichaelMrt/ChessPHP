<?php
set_time_limit(10);
require_once("logic/logic.php");
require_once("ui/ui.php");

class Controller
{

    function __construct()
    {

        $logic = new Logic();
        $logic->activate_inputs();
        $ui = new Ui();

        $ui->print_board($logic->get_board());
        
        print_r($_SESSION);
    }
}
