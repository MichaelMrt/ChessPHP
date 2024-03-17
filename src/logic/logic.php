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
    
    function __construct()
    {   

        #check if inputs were filled out
        if ($this->check_inputs_filled()) {
            #reconstruct chessboard from json
            $this->chessboard = $this->reconstruct_chessboard_from_json($_SESSION['chessboard']);
            # get the coordinates
            $current_x = (int) substr($_SESSION['piece_coordinates'], 0, 1);
            $current_y = (int) substr($_SESSION['piece_coordinates'], 1, 2);
            $move_to_x = (int) substr($_SESSION['move_to_coordinates'], 0, 1);
            $move_to_y = (int) substr($_SESSION['move_to_coordinates'], 1, 2);

            #check if there is a piece on the selected field, move the piece if there is one
            if ($this->check_rules($current_x, $current_y)) {
                $this->chessboard = $this->chessboard[$current_x][$current_y]->move($this->chessboard, (int) $move_to_x, (int) $move_to_y);
                $_SESSION['whitesturn'] = !$_SESSION['whitesturn']; # swap turns
                $_SESSION['move_number'] = ($_SESSION['move_number']+1);
            } else {
                $this->error .= "<p class='error'>Chess rules broken</p><br>";
            }
        } else if (isset($_SESSION['chessboard'])) {
            #reconstruct chessboard from json
            $this->chessboard = $this->reconstruct_chessboard_from_json($_SESSION['chessboard']);

            #creation of initial board 
        } else {
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
                    echo "<p class='error'>Error: Chessboard is not defined!</p>";
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
                <input class='textinput' name='piece_coordinates' type='text'>
                <br><br>
                <label>Move to coordinates</label>
                <input class='textinput' name='move_to_coordinates' type='text'>
                <br>
                <input name='chessboard' type='hidden' value='" . $encoded_json . "'></input>
                <input name='whitesturn' type='hidden' value='".$_SESSION['whitesturn']."'></input>
                <div><input type='submit' class='submit' value='Submit move'></div>
                </form>
             ";
    }

    function check_rules(int $current_x, int $current_y):bool
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

        # all rules checked
        return true;
    }

    function input_move(int $current_x, int $current_y, int $move_to_x, int $move_to_y):void
    {   
        if($this->check_rules($current_x, $current_y)){
            if($this->chessboard[$current_x][$current_y]->check_move_legal($this->chessboard, (int) $move_to_x, (int) $move_to_y)){
                $this->chessboard = $this->chessboard[$current_x][$current_y]->move($this->chessboard, (int) $move_to_x, (int) $move_to_y);
                $this->whitesturn = !$this->whitesturn; # swap turns
                $_SESSION['move_number'] = ($_SESSION['move_number']+1);
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

}
