<?php
require_once("chesspieces/pawn.php");
require_once("chesspieces/king.php");
require_once("chesspieces/queen.php");
require_once("chesspieces/bishop.php");
require_once("chesspieces/knight.php");
require_once("chesspieces/rook.php");
require_once("chessboard.php");
require_once("bot.php");

class Logic
{
    protected Chessboard $chessboard_obj;
    protected mixed $chessboard;
    protected bool $whitesturn=true;
    protected mixed $gamestatus_json;
    protected string $castling_status = "none";
    protected string $gamemode;
    
    function __construct(string $gamemode)
    {   
        $this->chessboard_obj = new Chessboard();
        $this->chessboard = $this->chessboard_obj->get_board();
        $this->gamemode = $gamemode;
    }


    public function input_move(Move $move):void
    {   
        if($this->chessboard[$move->from_x][$move->from_y]==""){
            throw new Exception("No piece on this square");
        }
        if($this->check_rules($this->chessboard,$move,$this->whitesturn)){           
                # move is legal
                $this->gamestatus_json = json_encode(['status' => 'legal', 'from' =>"$move->from_x$move->from_y", 'to' => "$move->to_x$move->to_y", 'castling' => $this->castling_status]);
                $this->handle_enpassant($move);

                if($this->chessboard[$move->to_x][$move->to_y] instanceof ChessPiece){
                    $status = json_decode($this->gamestatus_json, true);
                    $status['movetype'] = 'capture';
                    $this->gamestatus_json = json_encode($status);
                }

                $this->handle_castling();

                $this->castling_status = "none";  
                $this->chessboard = $this->chessboard_obj->move($move);

                $piece = $this->chessboard[$move->to_x][$move->to_y];
                if($this->chessboard_obj->can_promote($piece, $move->to_y)){
                    $this->chessboard  = $this->chessboard_obj->promote($move, $piece->get_color());
                    $status = json_decode($this->gamestatus_json, true);
                    $status['promote'] = "$move->to_x$move->to_y";
                    $this->gamestatus_json = json_encode($status);
                }

                $this->whitesturn = !$this->whitesturn; # swap turns
                $this->is_check($this->chessboard, "white");
                $this->is_check($this->chessboard, "black");
                $this->handle_checkmate($this->chessboard, 'white');
                $this->handle_checkmate($this->chessboard, 'black');
                $this->handle_stalemate($this->chessboard);
                echo $this->gamestatus_json;
        }else{
            echo $this->gamestatus_json;
        }
    } 

    private function check_rules(mixed $chessboard,Move $move, bool $whitesturn):bool
    {
        $this->castling_status="None";

        if($chessboard[$move->from_x][$move->from_y]==""){
            throw new Exception("No piece on this square");
        }

        if($chessboard[$move->to_x][$move->to_y] instanceof King){
            $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Cannot capture King!', 'from' =>"$move->from_x$move->from_y", 'to' => "$move->to_x$move->to_y"]);    
            return false;
        }

        if($this->wrong_turn_order($chessboard, $move, $whitesturn)){
            $this->gamestatus_json =  json_encode(['status' => 'illegal', 'message' => 'Wrong move order', 'from' =>"$move->from_x$move->from_y", 'to' => "$move->to_x$move->to_y"]);    
            return false;
        }

        $piece = $chessboard[$move->from_x][$move->from_y];
        if($piece->check_move_legal($chessboard,$move)==false){
          $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Piece cannot move like that', 'from' =>"$move->from_x$move->from_y", 'to' => "$move->to_x$move->to_y"]);    
          return false;
        }

        if($whitesturn){
            $color = "white";
        }else{
            $color = "black";
        }

      if($this->still_check($chessboard, $move, $color)){
        $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Still in check', 'from' =>"$move->from_x$move->from_y", 'to' => "$move->to_x$move->to_y"]);    
        return false;
      }

      if($this->self_check($chessboard, $move)){
        $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Cannot move into check', 'from' =>"$move->from_x$move->from_y", 'to' => "$move->to_x$move->to_y"]);    
        return false;
      }

      if($this->is_castling_move($chessboard, $move)){
        if($this->castling_legal($chessboard,$move)==false){
            $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Castling illegal', 'from' =>"$move->from_x$move->from_y", 'to' => "$move->to_x$move->to_y"]);    
            return false;
        }
      }

      return true;
    }

    private function is_check(mixed $chessboard, string $color):bool
    {   $king_pos = $this->get_king_pos($chessboard);

        if($color == 'white'){
            $opponent_color = 'black';
        }elseif($color == 'black'){
            $opponent_color = 'white';
        }else{
            throw new Exception("unkown color");
        }

        # check if king is in check
        for ($x=1; $x < 9; $x++) { 
            for ($y=1; $y < 9; $y++) { 
                if($chessboard[$x][$y] instanceof ChessPiece){
                    $move_to_king = new Move($x,$y,$king_pos[$color]['x'],$king_pos[$color]['y']);
                    if($chessboard[$x][$y]->get_color()==$opponent_color && $chessboard[$x][$y]->check_move_legal($chessboard, $move_to_king)){
                        $gamestatus = json_decode($this->gamestatus_json, true);
                        $gamestatus['check'] = ''.$color.' in check';
                        $this->gamestatus_json = json_encode($gamestatus);      
                        return true;
                    }
            }
        }
    }
        return false;
    }

    private function get_king_pos(mixed $chessboard):mixed
    {   $king_pos=null;

        // Optimization: Check if Kings are on their homesquare 
        if($chessboard[5][1] instanceof King && $chessboard[5][1]->get_color() == 'white'){
            $king_pos['white']['x']=5;
            $king_pos['white']['y']=1;
        }
        if($chessboard[5][8] instanceof King && $chessboard[5][8]->get_color() == 'black'){
            $king_pos['black']['x']=5;
            $king_pos['black']['y']=8;
        }

        // Scan the whole board to find the King
        for ($x=1; $x < 9; $x++) { 
            for ($y=1; $y < 9; $y++) { 
              if($chessboard[$x][$y] instanceof King && $chessboard[$x][$y]->get_color()=="white"){ #check if king is on board
                $king_pos['white']['x']=$x;
                $king_pos['white']['y']=$y;
              }  
              if($chessboard[$x][$y] instanceof King && $chessboard[$x][$y]->get_color()=="black"){
                $king_pos['black']['x']=$x;
                $king_pos['black']['y']=$y;
              }
              if(isset($king_pos['white']) && isset($king_pos['black'])){
                break;
              } 
            }    
        }
        if($king_pos==null){
            throw new Exception("King is not on the board");
        }
        if($king_pos['white']==null){
            throw new Exception("White King is not on the board");
        }	
        if($king_pos['black']==null){
            throw new Exception("Black King is not on the board");
        }
     return $king_pos;
    }

    # Check if the game is over for the given color
    private function handle_checkmate(mixed $chessboard, String $color):bool
    {
        $move_out_of_check = true;
        if($this->is_check($chessboard, $color)){    
            $move_out_of_check = false;         
            # first scan all pieces on the board
            for($x=1;$x<=8;$x++){
                for($y=1;$y<=8;$y++){
                    
                        if($chessboard[$x][$y] instanceof ChessPiece && $chessboard[$x][$y]->get_color()==$color){
                            # when finding a piece try to move it to every square on the board, if it is legal and stops check pass
                            for($move_x=1;$move_x<=8;$move_x++){
                                for($move_y=1;$move_y<=8;$move_y++){
                                    $test_move = new Move($x,$y,$move_x,$move_y);
                                    if($chessboard[$x][$y]->check_move_legal($chessboard, $test_move)){
                                        $future_board = $this->chessboard_obj->test_move($chessboard, $test_move); 
                                        if(!$this->is_check($future_board, $color)){ # if the move stops the check
                                            $move_out_of_check = true;
                                            break;
                                        } 
                                    }
                                }
                            }
                        }
                    
                }                    
            }
        } 
               
        if($move_out_of_check==true){
            $gamestatus_array = json_decode($this->gamestatus_json, true);
            $gamestatus_array['info'] = "There is a legal move";
            $this->gamestatus_json = json_encode($gamestatus_array);
        }else{
            $gamestatus_array = json_decode($this->gamestatus_json, true);
            $gamestatus_array['checkmate'] = "Checkmate! $color lost!";
            $this->gamestatus_json = json_encode($gamestatus_array);
        }
            
           
        return $move_out_of_check;

    }

    private function wrong_turn_order(mixed $chessboard, Move $move, bool $whitesturn):bool
    {
         # check if white moves only his pieces
         if($whitesturn && $chessboard[$move->from_x][$move->from_y]->get_color()=="black"){
            return true;
        }

        # check if black only moves his pieces
        if(!$whitesturn && $chessboard[$move->from_x][$move->from_y]->get_color()=="white"){
            return true;
        }

        return false;
    }

    private function still_check(mixed $chessboard, Move $move, string $color):bool
    {
        # When a player is in check he has to make sure his next move stops the check
      if($this->is_check($chessboard, $color)){
        $controll_board = $this->chessboard_obj->test_move($chessboard, $move);   
        if($this->is_check($controll_board, $color)){
            return true;
        }
     } 
     return false;
    }


    private function self_check(mixed $chessboard, Move $move):bool
    {
      # make sure the next move does not result in check for the same color
      # => King cannot move into check
      if($this->whitesturn){
            $controll_board = $this->chessboard_obj->test_move($chessboard, $move);   
            if($this->is_check($controll_board, "white")){
                    return true;
            }
      
      }else{ # blacksturn
        $controll_board = $this->chessboard_obj->test_move($chessboard, $move);   
            if($this->is_check($controll_board, "black")){ 
                    return true;
            }
        }
     return false;
    }

    private function handle_stalemate(mixed $chessboard):void
    {
        $gamestatus_backup = $this->gamestatus_json;
        $legal_moves_amount = count($this->get_legal_moves($chessboard, !$this->whitesturn)); //Gives json output!
        $this->gamestatus_json = $gamestatus_backup;
        if($legal_moves_amount==0){
            $status = json_decode($this->gamestatus_json, true);
            if($status['checkmate']!=""){
                return;
            }
            $status['stalemate'] = "The Game ends in a draw!";
            $this->gamestatus_json = json_encode($status);
        }
    }

    private function handle_castling():void
    {
        if($this->castling_status=="white_castling_short"){
            $rook_move = new Move(8,1,6,1);
            $this->chessboard = $this->chessboard_obj->move($rook_move);
        }
        elseif($this->castling_status=="white_castling_long"){
            $rook_move = new Move(1,1,4,1);
            $this->chessboard = $this->chessboard_obj->move($rook_move);
        }
        elseif($this->castling_status=="black_castling_short"){
            $rook_move = new Move(8,8,6,8);
            $this->chessboard = $this->chessboard_obj->move($rook_move);
        }
        elseif($this->castling_status=="black_castling_long"){
            $rook_move = new Move(1,8,4,8);
            $this->chessboard = $this->chessboard_obj->move($rook_move);
        }
    }

    private function check_short_castle_white(mixed $chessboard, Move $move):bool
    {
        if($move->from_x==5 && $move->from_y==1 && $move->to_x==7 && $move->to_y==1 && $this->not_castling_through_check_white_short($chessboard)){
            if($chessboard[6][1]=="" && $chessboard[7][1]=="" && $chessboard[5][1] instanceof King && $chessboard[8][1] instanceof Rook){
                if($chessboard[5][1]->get_has_moved_status()==false&& $chessboard[8][1]->get_has_moved_status()==false){
                    $rook_move = new Move(8,1,6,1);
                    $this->chessboard_obj->test_move($chessboard, $rook_move);
                    $this->castling_status = "white_castling_short";
                    return true;
                }
            }
        }
        return false;
    }


    private function check_long_castle_white(mixed $chessboard, Move $move):bool
    {
        if($move->from_x==5 && $move->from_y==1 && $move->to_x==3 && $move->to_y==1 && $this->not_castling_through_check_white_long($chessboard)){
            if($chessboard[2][1]=="" && $chessboard[3][1]=="" && $chessboard[4][1]=="" && $chessboard[5][1] instanceof King && $chessboard[1][1] instanceof Rook){
                if($chessboard[5][1]->get_has_moved_status()==false && $chessboard[1][1]->get_has_moved_status()==false){
                    $rook_move = new Move(1,1,4,1);
                    $this->chessboard_obj->test_move($chessboard, $rook_move);
                    $this->castling_status = "white_castling_long";
                    return true;
                }
            }
        }
        return false;
    }


    private function check_short_castle_black(mixed $chessboard, Move $move):bool
    {
        if($move->from_x==5 && $move->from_y==8 && $move->to_x==7 && $move->to_y==8 && $this->not_castling_through_check_black_short($chessboard)){
            if($chessboard[6][8]=="" && $chessboard[7][8]=="" && $chessboard[5][8] instanceof King && $chessboard[8][8] instanceof Rook){
                if($chessboard[5][8]->get_has_moved_status()==false && $chessboard[8][8]->get_has_moved_status()==false){
                    $rook_move = new Move(8,8,6,8);
                    $this->chessboard_obj->test_move($chessboard, $rook_move);
                    $this->castling_status = "black_castling_short";
                    return true;
                }
            }
        }
        return false;
    }


    private function check_long_castle_black(mixed $chessboard, Move $move):bool
    {
        if($move->from_x==5 && $move->from_y==8 && $move->to_x==3 && $move->to_y==8 && $this->not_castling_through_check_black_long($chessboard)){
            if($chessboard[2][8]=="" && $chessboard[3][8]=="" && $chessboard[4][8]=="" && $chessboard[5][8] instanceof King && $chessboard[1][8] instanceof Rook){
                if($chessboard[5][8]->get_has_moved_status()==false && $chessboard[1][8]->get_has_moved_status()==false){
                    $rook_move = new Move(1,8,4,8);
                    $this->chessboard_obj->test_move($chessboard, $rook_move);
                    $this->castling_status = "black_castling_long";
                    return true;
                }
            }
        }
        return false;
    }

    
    private function piece_can_move_to_square(mixed $chessboard, int $target_x, int $target_y, String $piececolor):bool
    {
        # first scan all pieces on the board
        for($x=1;$x<=8;$x++){
            for($y=1;$y<=8;$y++){
                    if($chessboard[$x][$y] instanceof ChessPiece && $chessboard[$x][$y]->get_color()==$piececolor){
                        $test_move = new Move($x,$y,$target_x,$target_y);
                        if($chessboard[$x][$y]->check_move_legal($chessboard, $test_move)){
                            return true;
                        }
                } 
            }                    
        }
        return false;
    }





    private function not_castling_through_check_white_short(mixed $chessboard):bool
    {
        if($this->piece_can_move_to_square($chessboard, 6,1, 'black') || $this->piece_can_move_to_square($chessboard, 7, 1,'black')){
            return false;
        }
        return true;
    }


    private function not_castling_through_check_white_long(mixed $chessboard):bool
    {
        if($this->piece_can_move_to_square($chessboard, 4,1, 'black') || $this->piece_can_move_to_square($chessboard, 3, 1, 'black') || $this->piece_can_move_to_square($chessboard, 2, 1,'black')){
            return false;
        }
        return true;
    }


    private function not_castling_through_check_black_short(mixed $chessboard):bool
    {
        if($this->piece_can_move_to_square($chessboard, 6, 8, 'white') || $this->piece_can_move_to_square($chessboard, 7, 8, 'white')){
            return false;
        }
        return true;
    }


    private function not_castling_through_check_black_long(mixed $chessboard):bool
    {
        if($this->piece_can_move_to_square($chessboard, 4, 8, 'white') || $this->piece_can_move_to_square($chessboard, 3, 8, 'white') || $this->piece_can_move_to_square($chessboard, 2, 8, 'white')){
            return false;
        }
        return true;
    }


    private function is_castling_move(mixed $chessboard, Move $move):bool
    {
        if($chessboard[$move->from_x][$move->from_y] instanceof King){
            # white short
            if($move->from_x==5 && $move->from_y==1 && $move->to_x==7 && $move->to_y==1){
                return true;
            }
            # white long
            if($move->from_x==5 && $move->from_y==1 && $move->to_x==3 && $move->to_y==1){
                return true;
            }
            # black short
            if($move->from_x==5 && $move->from_y==8 && $move->to_x==7 && $move->to_y==8){
                return true;
            }
            # black long
            if($move->from_x==5 && $move->from_y==8 && $move->to_x==3 && $move->to_y==8){
                return true;
            }
        }
        return false;
    }

    private function castling_legal(mixed $chessboard, Move $move):bool
    {
        // Cannot castle when in check
        if($this->is_check($chessboard, "white") || $this->is_check($chessboard, "black")){
            return false;
        }

        $legal = $this->check_short_castle_white($chessboard,$move) ||
                 $this->check_long_castle_white($chessboard,$move) ||
                 $this->check_short_castle_black($chessboard,$move) ||
                 $this->check_long_castle_black($chessboard,$move);

        return $legal;
    }

    private function handle_enpassant(Move $move):void
    {
        $current_square = $this->chessboard[$move->from_x][$move->from_y];
        $move_to_square = $this->chessboard[$move->to_x][$move->to_y];

        $this->play_enpassant($current_square, $move, $move_to_square);
        $this->reset_enpassant_possible();
        $this->enable_enpassant_possible($current_square, $move);
    }  
    

    private function play_enpassant(mixed $current_square, Move $move, mixed $move_to_square):void
    {
        if($current_square instanceof Pawn && abs($move->from_x-$move->to_x)==1 && $move_to_square==""){
            $this->chessboard_obj->remove_piece($move->to_x, $move->from_y);
            $this->gamestatus_json = json_encode(['status' => 'legal', 'from' =>"$move->from_x$move->from_y", 'to' => "$move->to_x$move->to_y",
             'castling' => $this->castling_status, 'enpassant' => 'true', 'remove_piece' => "$move->to_x$move->from_y"]);
        }
    }

    private function reset_enpassant_possible():void
    {
        for($x=1;$x<=8;$x++){
            for($y=1;$y<=8;$y++){
                if($this->chessboard[$x][$y] instanceof Pawn){
                    if($this->chessboard[$x][$y]->get_color()=="black" && $this->whitesturn){
                        if($this->chessboard[$x][$y]->get_enpassant_left_possible()==true){
                           $this->chessboard[$x][$y]->set_enpassant_left_possible(false);
                        }
                        if($this->chessboard[$x][$y]->get_enpassant_right_possible()==true){
                            $this->chessboard[$x][$y]->set_enpassant_right_possible(false);
                        }
                    }elseif($this->chessboard[$x][$y]->get_color()=="white" && !$this->whitesturn){
                        if($this->chessboard[$x][$y]->get_enpassant_left_possible()==true){
                            $this->chessboard[$x][$y]->set_enpassant_left_possible(false);
                        }
                        if($this->chessboard[$x][$y]->get_enpassant_right_possible()==true){
                            $this->chessboard[$x][$y]->set_enpassant_right_possible(false);
                        }
                    }
                }
            }                    
        }
    }


    private function enable_enpassant_possible(mixed $current_square, Move $move):void
    {
        if($move->to_x-1>0){
            $left_square = $this->chessboard[$move->to_x-1][$move->to_y];
        }else{
            $left_square = null;
        }
        if($move->to_x+1<9){
            $right_square = $this->chessboard[$move->to_x+1][$move->to_y];
        }else{
            $right_square = null;
        }

        if($current_square instanceof Pawn && abs($move->from_y-$move->to_y)==2){
            if($right_square instanceof Pawn){
                $right_square->set_enpassant_left_possible(true);
            }
            if($left_square instanceof Pawn){
                $left_square->set_enpassant_right_possible(true);
            }
        }
    }

    public function get_chessboard():mixed
    {
        return $this->chessboard;
    }

    public function get_chessboard_obj():Chessboard
    {
        return $this->chessboard_obj;
    }

    public function get_legal_moves(mixed $chessboard, bool $isbotmove):mixed
    {
        $legal_moves = [];
        if($isbotmove){
            $color = "black";
        }else{
            $color = "white";
        }

        for($x=1;$x<=8;$x++){
            for($y=1;$y<=8;$y++){
                if($chessboard[$x][$y] instanceof ChessPiece && $chessboard[$x][$y]->get_color()==$color){
                    for($move_x=1;$move_x<=8;$move_x++){
                        for($move_y=1;$move_y<=8;$move_y++){
                            $test_move = new Move($x,$y,$move_x,$move_y);
                            if($chessboard[$x][$y]->check_move_legal($chessboard, $test_move) ){
                                if($this->check_rules($chessboard, $test_move, !$isbotmove)){
                                    $legal_moves[] = $test_move;
                                }
                                
                            }
                        }
                    }
                }
            }
        }
        return $legal_moves;
    }

}
