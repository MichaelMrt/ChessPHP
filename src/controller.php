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

        $ui->print_board($logic->get_board());
        $logic->activate_inputs();
    }
}
