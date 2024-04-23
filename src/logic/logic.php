<?php
require_once("chesspieces/pawn.php");
require_once("chesspieces/king.php");
require_once("chesspieces/queen.php");
require_once("chesspieces/bishop.php");
require_once("chesspieces/knight.php");
require_once("chesspieces/rook.php");
class Logic
{
    protected mixed $chessboard;
    protected bool $whitesturn=true;
    protected String $error="";
    protected bool $white_in_check=false;
    protected bool $black_in_check=false;
    
    function __construct()
    {   
        if(isset($_SESSION['chessboard'])) { # submit was hit without filling inputs
            #reconstruct chessboard from json
            $this->chessboard = $this->reconstruct_chessboard_from_json($_SESSION['chessboard']);
        } else { #creation of initial board 
            $_SESSION['whitesturn']=true;
            $this->chessboard = $this->create_board();
        }

       
     
        $_SESSION['chessboard'] = json_encode($this->chessboard);
    }

    private function create_board(): mixed
    {   
        $this->whitesturn = true;
        #Creates an 8x8 array
        for ($x = 1; $x < 9; $x++) {
            for ($y = 1; $y < 9; $y++) {
                $chessboard[$x][$y] = "";
            }
        }
        #place white pawns
        for ($x = 1; $x < 9; $x++) {
            $y = 2;
            $chessboard[$x][$y] = new Pawn("white", $x, $y);
        }

        #place black pawns
        for ($x = 1; $x < 9; $x++) {
            $y = 7;
            $chessboard[$x][$y] = new Pawn("black", $x, $y);
        }

        #place white king
        $chessboard[5][1] = new King("white", 5, 1);

        #place black king
        $chessboard[5][8] = new King("black", 5, 8);
       
        #place white queen
        $chessboard[4][1] = new Queen("white", 4, 1);

        #place black queen
        $chessboard[4][8] = new Queen("black", 4, 8);

        #place white bishop
        $chessboard[3][1] = new Bishop("white", 3, 1);
        $chessboard[6][1] = new Bishop("white", 6, 1);

        #place black bishop
        $chessboard[3][8] = new Bishop("black", 3, 8);
        $chessboard[6][8] = new Bishop("black", 6, 8);

        #place white knight
        $chessboard[2][1] = new Knight("white", 2, 1);
        $chessboard[7][1] = new Knight("white", 7, 1);

        #place black knight
        $chessboard[2][8] = new Knight("black", 2, 8);
        $chessboard[7][8] = new Knight("black", 7, 8);

        #place white rook
        $chessboard[1][1] = new Rook("white", 1, 1);
        $chessboard[8][1] = new Rook("white", 8, 1);

        #place black rook
        $chessboard[1][8] = new Rook("black", 1, 8);
        $chessboard[8][8] = new Rook("black", 8, 8);

        return $chessboard;
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

    function check_inputs_filled(): bool
    {
        return !empty($_SESSION['piece_coordinates']) && !empty($_SESSION['move_to_coordinates']);
    }

    function reconstruct_chessboard_from_json(String $encoded_json): mixed
    {
        $this->whitesturn = $_SESSION['whitesturn'];
        $decoded_json = json_decode($encoded_json, true);

        for ($x = 1; $x < 9; $x++) {
            for ($y = 1; $y < 9; $y++) {
                
                if (!isset($decoded_json[$x][$y]['type'])) {
                    # no pawn on that square
                    $chessboard[$x][$y] = "";
                } elseif($decoded_json[$x][$y]['type'] == 'pawn') {
                    # pawn on that square
                    $chessboard[$x][$y] = new Pawn($decoded_json[$x][$y]['color'], $x, $y);
                } elseif($decoded_json[$x][$y]['type'] == 'king'){
                    # king on that square
                    $chessboard[$x][$y] = new King($decoded_json[$x][$y]['color'], $x, $y);
                } 
                elseif($decoded_json[$x][$y]['type'] == 'queen'){
                    # queen on that square
                    $chessboard[$x][$y] = new Queen($decoded_json[$x][$y]['color'], $x, $y);
                } 
                elseif($decoded_json[$x][$y]['type'] == 'bishop'){
                    # bishop on that square
                    $chessboard[$x][$y] = new Bishop($decoded_json[$x][$y]['color'], $x, $y);
                } 
                elseif($decoded_json[$x][$y]['type'] == 'knight'){
                    # knight on that square
                    $chessboard[$x][$y] = new Knight($decoded_json[$x][$y]['color'], $x, $y);
                } 
                elseif($decoded_json[$x][$y]['type'] == 'rook'){
                    # rook on that square
                    $chessboard[$x][$y] = new Rook($decoded_json[$x][$y]['color'], $x, $y);
                }else{
                    echo "<p class='error'>Error: Chesspiece is not defined!</p>";
                    exit;
                } 
            }
        }
        return $chessboard;
    }

    function activate_inputs():void
    {
        echo "<h3>Format to pick piece is xy</h3>";

        $encoded_json = json_encode($this->chessboard);
        echo "<form method='post' action='chessgame.php'>
                <label>Enter coordinates of the piece you want to move</label>
                <input class='textinput' name='pickedsquare' type='text'>
                <br><br>
                <label>Move to coordinates</label>
                <input class='textinput' name='movetosquare' type='text'>
                <br>
                <input name='chessboard' type='hidden' value='" . $encoded_json . "'></input>
                <input name='whitesturn' type='hidden' value='".$_SESSION['whitesturn']."'></input>
                <div><input type='submit' class='submit' value='Submit move'></div>
                </form>
             ";
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
            if($this->is_check($controll_board)){ #error here
                if($this->white_in_check){
                    return false;
                }
            }
      }elseif(!$this->whitesturn){
        $controll_board = $this->chessboard[$current_x][$current_y]->test_move($this->chessboard, (int) $move_to_x, (int) $move_to_y);   
            if($this->is_check($controll_board)){ #error here
                if($this->black_in_check){
                    return false;
                }
            }
        }
      }

        # all rules checked
        return true;
    }

    function input_move(int $current_x, int $current_y, int $move_to_x, int $move_to_y):void
    { 
        if($this->check_rules($current_x, $current_y,$move_to_x,$move_to_y)){           
            if($this->chessboard[$current_x][$current_y]->check_move_legal($this->chessboard, (int) $move_to_x, (int) $move_to_y)){     
                # move is legal           
                $this->chessboard = $this->chessboard[$current_x][$current_y]->move($this->chessboard, (int) $move_to_x, (int) $move_to_y);
                $this->whitesturn = !$this->whitesturn; # swap turns
                $_SESSION['move_number'] = ($_SESSION['move_number']+1);
                $this->is_check($this->chessboard);
                $this->is_checkmate($this->chessboard);
            }

    
            $_SESSION['chessboard'] = json_encode($this->chessboard);
            $_SESSION['whitesturn'] = $this->whitesturn;
        }
       
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
                    }elseif($chessboard[$x][$y]->get_color()=="white" && $chessboard[$x][$y]->check_move_legal($chessboard,$king_pos['black']['x'],$king_pos['black']['y'])){
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
                                       $this->healthCheck($chessboard);
                                           if($this->chessboard[$x][$y]->check_move_legal($chessboard,$move_x,$move_y)){
                                               $future_board = $chessboard[$x][$y]->test_move($chessboard,$move_x,$move_y); # error caused from this
                                               if(!$this->is_check($future_board)){ # error beginning here
                                                   # no move out of check
                                                   $move_out_of_check = true;
                                               } 
                                       }
                                   }
                               }
                           }
                        } 
                        
                        if($this->black_in_check){
                            if(is_a($chessboard[$x][$y],'ChessPiece')&&$chessboard[$x][$y]->get_color()=="black"){
                                # when finding a piece try to move it to every square on the board, if it is legal and stops check pass
                                for($move_x=1;$move_x<=8;$move_x++){
                                   for($move_y=1;$move_y<=8;$move_y++){
                                       $this->healthCheck($chessboard);
                                           if($this->chessboard[$x][$y]->check_move_legal($chessboard,$move_x,$move_y)){
                                               $future_board = $chessboard[$x][$y]->test_move($chessboard,$move_x,$move_y); # error caused from this
                                               if(!$this->is_check($future_board)){ # error beginning here
                                                   # no move out of check
                                                   $move_out_of_check = true;
                                               } 
                                       }
                                   }
                               }
                           }
                        }  
                    }                    
                }
               
                if($move_out_of_check==true){
                   $_SESSION['error'] = "There is a legal move<br>";
                }else{
                    $_SESSION['error'] = "Checkmate! Game Over<br>";
                }
            }
           
        return $move_out_of_check;

    }
    
    function healthCheck(mixed $chessboard){
        for($x=1;$x<9;$x++){
          for($y=1;$y<9;$y++){
            if(is_a($chessboard[$x][$y],"ChessPiece")){
                if($chessboard[$x][$y]->get_x()!=$x){
                print("Error on board for x");
                }
                if($chessboard[$x][$y]->get_y()!=$y){
                print("Error on board for y");
                }
        }
          }
        }
      }
}
