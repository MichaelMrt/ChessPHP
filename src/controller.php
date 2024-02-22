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
            print("picked x:".substr($_SESSION['pickedsquare'],0,1)."<br>");
            print("picked y:".substr($_SESSION['pickedsquare'],1,2));

            $current_x = (int) substr($_SESSION['pickedsquare'],0,1);
            $current_y = (int) substr($_SESSION['pickedsquare'],1,2);
            $_SESSION['current_x'] = $current_x;
            $_SESSION['current_y'] = $current_y;

           # $logic->check_rules($current_x, $current_y);

        }

        if(isset($_SESSION['movetosquare'])){
            print("moveto x:".substr($_SESSION['movetosquare'],0,1)."<br>");
            print("moveto y:".substr($_SESSION['movetosquare'],1,2));

            $move_to_x = (int) substr($_SESSION['movetosquare'],0,1);
            $move_to_y = (int) substr($_SESSION['movetosquare'],1,2);

            # try to move the piece
            $logic->input_move($_SESSION['current_x'], $_SESSION['current_y'], $move_to_x, $move_to_y);
            $chessboard = $logic->reconstruct_chessboard_from_json($_SESSION['chessboard']);
            $ui->print_board($chessboard);
        }
        
       # print_r($_SESSION);
    }
}
