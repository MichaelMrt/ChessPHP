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


        if(isset($_SESSION['pickedsquare'])){
            $output = "Picked square: <br>x:".substr($_SESSION['pickedsquare'],0,1)."<br>y:".substr($_SESSION['pickedsquare'],1,2)."<br>";
            $current_x = (int) substr($_SESSION['pickedsquare'],0,1);
            $current_y = (int) substr($_SESSION['pickedsquare'],1,2);
            $_SESSION['current_x'] = $current_x;
            $_SESSION['current_y'] = $current_y;
        }
        if(isset($_SESSION['movetosquare'])){
            $output = "Move to <br>x:".substr($_SESSION['movetosquare'],0,1)."<br>y:".substr($_SESSION['movetosquare'],1,2)."<br>";

            $move_to_x = (int) substr($_SESSION['movetosquare'],0,1);
            $move_to_y = (int) substr($_SESSION['movetosquare'],1,2);

            # try to move the piece
            $logic->input_move($_SESSION['current_x'], $_SESSION['current_y'], $move_to_x, $move_to_y);
        }else{
            $output = "";
        }

    # contains 3 div areas: left,middel,right
    echo "<div class='container'>";
           
        if(!isset($_SESSION['error'])){
            $_SESSION['error'] ="";
        }

        if(!isset($_SESSION['check'])){
            $_SESSION['check'] = "";
        }

        if(!isset($_SESSION['checkmate'])){
            $_SESSION['checkmate'] = "";
        }else{
            $_SESSION['error']="";
        }


        # left div
        echo "
        <div class='inner-div'>
        <form method='post' action='chessgame.php'>
            <h3>left</h3>
            <input type='hidden' name='reset' value='true'>
            <input class='reset' type='submit' value='Restart'> </input>
        </form>
        $output
        ".$logic->get_rulesbroken_msg()."
        ".$_SESSION['error']."
        ".$_SESSION['check']."
        </div>";

        # middle/center
        echo "<div class='inner-div-center'>";
        print( $_SESSION['checkmate']);
        $ui->print_board($logic->get_board());
        $logic->activate_inputs();
        echo "</div>";

        # right div
        echo "<div class='inner-div'>
            <h3>right</h3>";
            # display move number
            echo "Move number: ".$_SESSION['move_number']."<br>";
            # display player on move
            if($logic->get_player_on_move()==true){
                echo "Whites move";
            }else{
                echo "Blacks move";
            }
    echo "</div>";
   echo "</div>";
    }
}
