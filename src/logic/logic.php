<?php
require_once("chesspieces/pawn.php");

class Logic
{
    protected mixed $chessboard;
    protected bool $whitesturn=true;
    
    function __construct()
    {   

        #check if inputs were filled out
        if ($this->check_inputs_filled()) {
            #reconstruct chessboard from json
            $this->chessboard = $this->reconstruct_chessboard_from_json($_SESSION['chessboard']);
            # get the coordinates
            $current_x = (int) substr($_SESSION['piece_coordinates'], 0, 1);
            $current_y = (int) substr($_SESSION['piece_coordinates'], 2, 1);
            $move_to_x = (int) substr($_SESSION['move_to_coordinates'], 0, 1);
            $move_to_y = (int) substr($_SESSION['move_to_coordinates'], 2, 1);

            #check if there is a piece on the selected field, move the piece if there is one
            if ($this->check_rules($current_x, $current_y)) {
                $this->chessboard = $this->chessboard[$current_x][$current_y]->move($this->chessboard, (int) $move_to_x, (int) $move_to_y);
                $this->whitesturn = !$this->whitesturn; # swap turns
            } else {
                print("<p class='error'>Chess rules broken</p>");
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

    /** @return array<int, array<int, Pawn|string>>*/
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
                if ($decoded_json[$x][$y] == "") {
                    # no pawn on that square
                    $chessboard[$x][$y] = "";
                } else {
                    # pawn on that square
                    $chessboard[$x][$y] = new Pawn($decoded_json[$x][$y]['color'], $x, $y);
                }
            }
        }

        return $chessboard;
    }

    function activate_inputs():void
    {
        echo "<h3>Format to pick piece is x,y</h3>";

        $encoded_json = json_encode($this->chessboard);
        echo "<form method='post' action='chessgame.php'>
                <label>Enter coordinates of the piece you want to move</label>
                <input name='piece_coordinates' type='text'>
                <br><br>
                <label>Move to coordinates</label>
                <input name='move_to_coordinates' type='text'>
                <br>
                <input name='chessboard' type='hidden' value='" . $encoded_json . "'></input>
                <input name='whitesturn' type='hidden' value='".$this->whitesturn."'></input>
                <div><input type='submit' class='submit' value='Submit move'></div>
                </form>
             ";
    }

    function check_rules(int $current_x, int $current_y):bool
    {
        # check if selected square has a piece
        if(is_a($this->chessboard[$current_x][$current_y], "ChessPiece")){

        }else{
            echo "<br>Not a Chesspiece on that square";
            return false;
        }

        # check if it is whites turn
        if($this->whitesturn && $this->chessboard[$current_x][$current_y]->get_color()=="black"){
            echo "<p class='error'>It is whites move</p>";
            return false;
        }

        # check if it is blacks turn
        if(!$this->whitesturn && $this->chessboard[$current_x][$current_y]->get_color()=="white"){
            echo "<p class='error'>It is blacks move</p>";
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
            }

    
            $_SESSION['chessboard'] = json_encode($this->chessboard);
            $_SESSION['whitesturn'] = $this->whitesturn;
        }
       
    }
}
