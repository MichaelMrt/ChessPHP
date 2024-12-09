<?php
require_once("chesspieces/pawn.php");
require_once("chesspieces/king.php");
require_once("chesspieces/queen.php");
require_once("chesspieces/bishop.php");
require_once("chesspieces/knight.php");
require_once("chesspieces/rook.php");
require_once("chessboard.php");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $move = get_played_move();
    $selected_piece_x = $move[0];
    $selected_piece_y = $move[1];
    $move_to_x = $move[2];
    $move_to_y = $move[3];
    $_SESSION['chess_game']->input_move($selected_piece_x,$selected_piece_y,$move_to_x,$move_to_y);
}



function get_played_move() : string 
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $move = isset($_POST['move_to_id']) ? $_POST['move_to_id'] : '';
        if(isset($move)){
            $move=$_POST['selected_piece_id'].$_POST['move_to_id'];
        }else{
            $move = 'Error while processing the move';
        }
            return $move;
    }
}

class Logic
{
    protected Chessboard $chessboard_obj;
    protected mixed $chessboard;
    protected bool $whitesturn=true; // has to be removed
    protected String $error="";
    protected bool $white_in_check=false;
    protected bool $black_in_check=false;
    
    function __construct()
    {   
        $this->chessboard_obj = new chessboard();
        $this->chessboard = $this->chessboard_obj->get_board();
    }


    function input_move(int $current_x, int $current_y, int $move_to_x, int $move_to_y):void
    {   
        $piece = $this->chessboard[$current_x][$current_y];
        
        if($this->check_rules($current_x, $current_y,$move_to_x,$move_to_y)){           
            if($piece->check_move_legal($this->chessboard, (int) $move_to_x, (int) $move_to_y)){     
                # move is legal           
                $this->chessboard = $this->chessboard_obj->move($this->chessboard, (int) $current_x, (int) $current_y,(int) $move_to_x, (int) $move_to_y);
                $this->whitesturn = !$this->whitesturn; # swap turns
                $this->is_check($this->chessboard);
                $this->is_checkmate($this->chessboard);
                $this->chessboard_obj->update_board($this->chessboard, $current_x, $current_y, $move_to_x, $move_to_y);
                echo json_encode(['status' => 'legal', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);
            }else{
                echo json_encode(['status' => 'illegal', 'message' => 'Illegal move', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);
            }

    
             $_SESSION['chessboard'] = json_encode($this->chessboard);
            // $_SESSION['whitesturn'] = $this->whitesturn;
        }else{
            echo json_encode(['status' => 'illegal', 'message' => 'Game rules broken', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);    
        }
       
    }



    #just for testing purpose
    function debug_output_board(mixed $chessboard): void
    {
        echo "<pre>";
        print_r($chessboard);
        echo "</pre>";
    }

    function get_board(): mixed
    {
        return $this->chessboard;
    }
 

    function check_rules(int $current_x, int $current_y, int $move_to_x, int $move_to_y):bool
    {
        # check if selected square has a piece
        if(is_a($this->chessboard[$current_x][$current_y], "ChessPiece")){

        }else{
             $_SESSION['error'] = "Not a Chess piece on that Square";
            return false;
        }

        # check if it is whites turn
        if($this->whitesturn && $this->chessboard[$current_x][$current_y]->get_color()=="black"){
            $this->error .= "<p class='error'>It is whites move</p>";
            return false;
        }

        # check if it is blacks turn
        if(!$this->whitesturn && $this->chessboard[$current_x][$current_y]->get_color()=="white"){
            $this->error .=  "<p class='error'>It is blacks move</p>";
            return false;
        }

        # look for checks and finds out if the next move stops the check
      if($this->is_check($this->chessboard)){
            $controll_board = $this->chessboard[$current_x][$current_y]->test_move($this->chessboard, (int) $move_to_x, (int) $move_to_y);   
            if($this->is_check($controll_board)){
                # In check and the move doesn't block the check or move out of it
                return false;
            }
      } 

      # make sure the next move does not result in check for the same color
      # => King cannot move into check
      if($this->whitesturn){
        if($this->chessboard[$current_x][$current_y]->check_move_legal($this->chessboard,$move_to_x,$move_to_y)){
            $controll_board = $this->chessboard[$current_x][$current_y]->test_move($this->chessboard, (int) $move_to_x, (int) $move_to_y);   
            if($this->is_check($controll_board)){
                if($this->white_in_check){
                    return false;
                }
            }
      }
      }else{ # blacksturn
        if($this->chessboard[$current_x][$current_y]->check_move_legal($this->chessboard,$move_to_x,$move_to_y)){
        $controll_board = $this->chessboard[$current_x][$current_y]->test_move($this->chessboard, (int) $move_to_x, (int) $move_to_y);   
            if($this->is_check($controll_board)){ 
                if($this->black_in_check){
                    return false;
                }
            }
         }
        }

        # all rules checked
        return true;
    }


    function get_player_on_move():bool
    {
        return $this->whitesturn;
    }

    function get_rulesbroken_msg():String
    {
        return $this->error;
    }


    function is_check(mixed $chessboard):bool
    {   $king_pos = $this->get_king_pos($chessboard);
        # check if king is in check
        for ($x=1; $x < 9; $x++) { 
            for ($y=1; $y < 9; $y++) { 
                
                if(is_a($chessboard[$x][$y],'ChessPiece')){
                    if($chessboard[$x][$y]->get_color()=="black" && $chessboard[$x][$y]->check_move_legal($chessboard,$king_pos['white']['x'],$king_pos['white']['y'])){
                        $_SESSION['check'] = "White king in check!";
                        $_SESSION['error'] = "";
                        $this->white_in_check = true;
                        return true;
                    }
                    if($chessboard[$x][$y]->get_color()=="white" && $chessboard[$x][$y]->check_move_legal($chessboard,$king_pos['black']['x'],$king_pos['black']['y'])){
                        $_SESSION['error'] = "";
                        $_SESSION['check'] = "Black king in check!";
                        $this->black_in_check = true;
                       return true;
                    }
                }
            }
        }
        $_SESSION['error'] = "";
        $this->white_in_check = false;
        $this->black_in_check = false;
        return false;
    }

    function get_king_pos(mixed $chessboard):mixed
    {   $king_pos=null;
        for ($x=1; $x < 9; $x++) { 
            for ($y=1; $y < 9; $y++) { 
              if(is_a($chessboard[$x][$y],'King') && $chessboard[$x][$y]->get_color()=="white"){ #check if king is on board
                $king_pos['white']['x']=$x;
                $king_pos['white']['y']=$y;
              }  
              if(is_a($chessboard[$x][$y],'King') && $chessboard[$x][$y]->get_color()=="black"){
                $king_pos['black']['x']=$x;
                $king_pos['black']['y']=$y;
              } 
            }    
        }
        if($king_pos==null){
            print("NO KING FOUND");
        }
     return $king_pos;
    }

    function is_checkmate(mixed $chessboard):bool
    {
        $move_out_of_check = false;
        $white_checkmated = false;
        $black_checkmated = false;
            if($this->is_check($chessboard)){
                # check if black has a move             
                # first scan all pieces on the board
                for($x=1;$x<=8;$x++){
                    for($y=1;$y<=8;$y++){
                        # only white moves need to be scanned when white is in check
                        if($this->white_in_check){
                            if(is_a($chessboard[$x][$y],'ChessPiece')&&$chessboard[$x][$y]->get_color()=="white"){
                                # when finding a piece try to move it to every square on the board, if it is legal and stops check pass
                                for($move_x=1;$move_x<=8;$move_x++){
                                   for($move_y=1;$move_y<=8;$move_y++){
                                           if($this->chessboard[$x][$y]->check_move_legal($chessboard,$move_x,$move_y)){
                                               $future_board = $chessboard[$x][$y]->test_move($chessboard,$move_x,$move_y); 
                                               if(!$this->is_check($future_board)){ 
                                                   # no move out of check
                                                   $move_out_of_check = true;
                                               } 
                                       }
                                   }
                               }
                               $white_checkmated = !$move_out_of_check;
                           }
                        } 
                         # only black moves need to be scanned when black is in check
                        if($this->black_in_check){
                            if(is_a($chessboard[$x][$y],'ChessPiece')&&$chessboard[$x][$y]->get_color()=="black"){
                                # when finding a piece try to move it to every square on the board, if it is legal and stops check pass
                                for($move_x=1;$move_x<=8;$move_x++){
                                   for($move_y=1;$move_y<=8;$move_y++){
                                           if($this->chessboard[$x][$y]->check_move_legal($chessboard,$move_x,$move_y)){
                                               $future_board = $chessboard[$x][$y]->test_move($chessboard,$move_x,$move_y); 
                                               if(!$this->is_check($future_board)){ 
                                                   # no move out of check
                                                   $move_out_of_check = true;
                                               } 
                                       }
                                   }
                               }
                               $black_checkmated = !$move_out_of_check;
                           }
                        }  
                    }                    
                }
               
                if($move_out_of_check==true){
                   $_SESSION['checkmate'] = "There is a legal move<br>";
                }else{
                    if($white_checkmated){
                        $_SESSION['checkmate'] = "<h1>Checkmate! Black won!</h1><br>";
                    }
                    if($black_checkmated){
                        $_SESSION['checkmate'] = "<h1>Checkmate! White won!</h1><br>";
                    }
                    
                }
            }
           
        return $move_out_of_check;

    }
}
