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

        if(isset($_SESSION['pickedsquare'])){
            #print_r($_SESSION['pickedsquare']);
            print("picked x:".substr($_SESSION['pickedsquare'],0,1)."<br>");
            print("picked y:".substr($_SESSION['pickedsquare'],1,2));

            $current_x = (int) substr($_SESSION['pickedsquare'],0,1);
            $current_y = (int) substr($_SESSION['pickedsquare'],1,2);

            # try to move the piece
            $logic->input_move($current_x, $current_y);
        }
        if(isset($_SESSION['movetosquare'])){
            #print_r($_SESSION['pickedsquare']);

            print("moveto x:".substr($_SESSION['movetosquare'],0,1)."<br>");
            print("moveto y:".substr($_SESSION['movetosquare'],1,2));

            $current_x = (int) substr($_SESSION['movetosquare'],0,1);
            $current_y = (int) substr($_SESSION['movetosquare'],1,2);

            # try to move the piece
            $logic->input_move($current_x, $current_y);
        }
       
    }
}
