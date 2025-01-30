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
    protected $gamestatus_json;
    protected string $castling_status = "none";
    protected string $gamemode;
    
    function __construct($gamemode)
    {   
        $this->chessboard_obj = new chessboard();
        $this->chessboard = $this->chessboard_obj->get_board();
        $this->gamemode = $gamemode;
    }


    public function input_move(int $current_x, int $current_y, int $move_to_x, int $move_to_y):void
    {   
        if($this->chessboard[$current_x][$current_y]==""){
            throw new Exception("No piece on this square");
        }
        if($this->check_rules($this->chessboard,$current_x, $current_y,$move_to_x,$move_to_y,$this->whitesturn)){           
                # move is legal
                $this->gamestatus_json = json_encode(['status' => 'legal', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y", 'castling' => $this->castling_status]);
                $this->handle_enpassant($current_x, $current_y, $move_to_x, $move_to_y);

                if($this->chessboard[$move_to_x][$move_to_y] instanceof ChessPiece){
                    $status = json_decode($this->gamestatus_json, true);
                    $status['movetype'] = 'capture';
                    $this->gamestatus_json = json_encode($status);
                }

                $this->handle_castling();

                $this->castling_status = "none";  
                $this->chessboard = $this->chessboard_obj->move($this->chessboard, (int) $current_x, (int) $current_y,(int) $move_to_x, (int) $move_to_y);

                $piece = $this->chessboard[$move_to_x][$move_to_y];
                if($this->chessboard_obj->can_promote($piece, $move_to_y)){
                    $this->chessboard  = $this->chessboard_obj->promote($move_to_x, $move_to_y, $piece->get_color());
                    $status = json_decode($this->gamestatus_json, true);
                    $status['promote'] = "$move_to_x$move_to_y";
                    $this->gamestatus_json = json_encode($status);
                }

                $this->whitesturn = !$this->whitesturn; # swap turns
                $this->is_check($this->chessboard, "white");
                $this->is_check($this->chessboard, "black");
                $this->is_checkmate($this->chessboard);
                $this->is_stalemate($this->chessboard);
                echo $this->gamestatus_json;
        }else{
            echo $this->gamestatus_json;
        }
    } 

    private function check_rules($chessboard,int $current_x, int $current_y, int $move_to_x, int $move_to_y, $whitesturn):bool
    {
        $this->castling_status="None";

        if($chessboard[$current_x][$current_y]==""){
            throw new Exception("No piece on this square");
        }

        if($chessboard[$move_to_x][$move_to_y] instanceof King){
            $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Cannot capture King!', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);    
            return false;
        }

        if($this->wrong_turn_order($chessboard,$current_x,$current_y,$whitesturn)){
            $this->gamestatus_json =  json_encode(['status' => 'illegal', 'message' => 'Wrong move order', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);    
            return false;
        }

        $piece = $chessboard[$current_x][$current_y];
        if($piece->check_move_legal($chessboard,$current_x,$current_y, (int) $move_to_x, (int) $move_to_y)==false){
          $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Piece cannot move like that', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);    
          return false;
        }

        if($whitesturn){
            $color = "white";
        }else{
            $color = "black";
        }

      if($this->still_check($chessboard, $current_x, $current_y, $move_to_x, $move_to_y, $color)){
        $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Still in check', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);    
        return false;
      }

      if($this->self_check($chessboard, $current_x, $current_y, $move_to_x, $move_to_y)){
        $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Cannot move into check', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);    
        return false;
      }

      if($this->is_castling_move($chessboard, $current_x, $current_y, $move_to_x, $move_to_y)){
        if($this->castling_legal($chessboard,$current_x, $current_y, $move_to_x, $move_to_y)==false){
            $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Castling illegal', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);    
            return false;
        }
      }

      return true;
    }

    private function is_check(mixed $chessboard, $color):bool
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
                    if($chessboard[$x][$y]->get_color()==$opponent_color && $chessboard[$x][$y]->check_move_legal($chessboard,$x,$y,$king_pos[$color]['x'],$king_pos[$color]['y'])){
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

    private function is_checkmate(mixed $chessboard):bool
    {
        $move_out_of_check = false;
        $white_checkmated = false;
        $black_checkmated = false;
            
                # check if black has a move             
                # first scan all pieces on the board
        for($x=1;$x<=8;$x++){
            for($y=1;$y<=8;$y++){
                        # only white moves need to be scanned when white is in check
                if($this->is_check($chessboard, "white")){
                    if($chessboard[$x][$y] instanceof ChessPiece && $chessboard[$x][$y]->get_color()=="white"){
                        # when finding a piece try to move it to every square on the board, if it is legal and stops check pass
                            for($move_x=1;$move_x<=8;$move_x++){
                                   for($move_y=1;$move_y<=8;$move_y++){
                                           if($this->chessboard[$x][$y]->check_move_legal($chessboard,$x,$y,$move_x,$move_y)){
                                               $future_board = $this->chessboard_obj->test_move($chessboard, $x, $y, $move_x, $move_y); 
                                               if(!$this->is_check($future_board, "white")){ 
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
                        if($this->is_check($chessboard, "black")){
                            if($chessboard[$x][$y] instanceof ChessPiece && $chessboard[$x][$y]->get_color()=="black"){
                                # when finding a piece try to move it to every square on the board, if it is legal and stops check pass
                                for($move_x=1;$move_x<=8;$move_x++){
                                   for($move_y=1;$move_y<=8;$move_y++){
                                           if($this->chessboard[$x][$y]->check_move_legal($chessboard,$x,$y,$move_x,$move_y)){
                                               $future_board = $this->chessboard_obj->test_move($chessboard, $x, $y, $move_x, $move_y); 
                                               if(!$this->is_check($future_board, "black")){ 
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
                    $gamestatus_array = json_decode($this->gamestatus_json, true);
                    $gamestatus_array['info'] = "There is a legal move";
                    $this->gamestatus_json = json_encode($gamestatus_array);
                }else{
                    if($white_checkmated){
                        $status = json_decode($this->gamestatus_json, true);
                        $status['checkmate'] = "white is checkmated - black wins";
                        $this->gamestatus_json	 = json_encode($status);
                    }
                    if($black_checkmated){
                        $status = json_decode($this->gamestatus_json, true);
                        $status['checkmate'] = "black is checkmated - white wins";
                        $this->gamestatus_json	 = json_encode($status);
                    }
                    
        }
            
           
        return $move_out_of_check;

    }

    private function wrong_turn_order($chessboard,$current_x, $current_y, $whitesturn){
         # check if white moves only his pieces
         if($whitesturn && $chessboard[$current_x][$current_y]->get_color()=="black"){
            return true;
        }

        # check if black only moves his pieces
        if(!$whitesturn && $chessboard[$current_x][$current_y]->get_color()=="white"){
            return true;
        }

        return false;
    }

    private function still_check($chessboard,$current_x, $current_y, $move_to_x, $move_to_y, $color){
        # When a player is in check he has to make sure his next move stops the check
      if($this->is_check($chessboard, $color)){
        $controll_board = $this->chessboard_obj->test_move($chessboard, (int) $current_x, (int) $current_y, (int) $move_to_x, (int) $move_to_y);   
        if($this->is_check($controll_board, $color)){
            return true;
        }
     } 
     return false;
    }


    private function self_check($chessboard,$current_x, $current_y, $move_to_x, $move_to_y){
      # make sure the next move does not result in check for the same color
      # => King cannot move into check
      if($this->whitesturn){
            $controll_board = $this->chessboard_obj->test_move($chessboard, (int) $current_x, (int) $current_y, (int) $move_to_x, (int) $move_to_y);   
            if($this->is_check($controll_board, "white")){
                    return true;
            }
      
      }else{ # blacksturn
        $controll_board = $this->chessboard_obj->test_move($chessboard, (int) $current_x, (int) $current_y, (int) $move_to_x, (int) $move_to_y);   
            if($this->is_check($controll_board, "black")){ 
                    return true;
            }
        }
     return false;
    }

    private function is_stalemate($chessboard){
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

    private function handle_castling(){
        if($this->castling_status=="white_castling_short"){
            $this->chessboard = $this->chessboard_obj->move($this->chessboard,  8,1,6,1);
        }
        elseif($this->castling_status=="white_castling_long"){
            $this->chessboard = $this->chessboard_obj->move($this->chessboard,  1,1,4,1);
        }
        elseif($this->castling_status=="black_castling_short"){
            $this->chessboard = $this->chessboard_obj->move($this->chessboard,  8,8,6,8);
        }
        elseif($this->castling_status=="black_castling_long"){
            $this->chessboard = $this->chessboard_obj->move($this->chessboard,  8,8,6,8);
        }
    }

    private function check_short_castle_white($chessboard,$current_x, $current_y, $move_to_x, $move_to_y){
        if($current_x==5 && $current_y==1 && $move_to_x==7 && $move_to_y==1 && $this->not_castling_through_check_white_short()){
            if($chessboard[6][1]=="" && $chessboard[7][1]=="" && $chessboard[5][1] instanceof King && $chessboard[8][1] instanceof Rook){
                if($chessboard[5][1]->get_has_moved_status()==false&& $chessboard[8][1]->get_has_moved_status()==false){
                    $this->chessboard_obj->test_move($chessboard,8,1,6,1);
                    $this->castling_status = "white_castling_short";
                    return true;
                }
            }
        }
        return false;
    }


    private function check_long_castle_white($chessboard,$current_x, $current_y, $move_to_x, $move_to_y){
        if($current_x==5 && $current_y==1 && $move_to_x==3 && $move_to_y==1 && $this->not_castling_through_check_white_long()){
            if($chessboard[2][1]=="" && $chessboard[3][1]=="" && $chessboard[4][1]=="" && $chessboard[5][1] instanceof King && $chessboard[1][1] instanceof Rook){
                if($chessboard[5][1]->get_has_moved_status()==false && $chessboard[1][1]->get_has_moved_status()==false){
                    $this->chessboard_obj->test_move($chessboard,1,1,4,1);
                    $this->castling_status = "white_castling_long";
                    return true;
                }
            }
        }
        return false;
    }


    private function check_short_castle_black($chessboard,$current_x, $current_y, $move_to_x, $move_to_y){
        if($current_x==5 && $current_y==8 && $move_to_x==7 && $move_to_y==8 && $this->not_castling_through_check_black_short()){
            if($chessboard[6][8]=="" && $chessboard[7][8]=="" && $chessboard[5][8] instanceof King && $chessboard[8][8] instanceof Rook){
                if($chessboard[5][8]->get_has_moved_status()==false && $chessboard[8][8]->get_has_moved_status()==false){
                    $this->chessboard_obj->test_move($chessboard,8,8,6,8);
                    $this->castling_status = "black_castling_short";
                    return true;
                }
            }
        }
        return false;
    }


    private function check_long_castle_black($chessboard,$current_x, $current_y, $move_to_x, $move_to_y){
        if($current_x==5 && $current_y==8 && $move_to_x==3 && $move_to_y==8 && $this->not_castling_through_check_black_long()){
            if($chessboard[2][8]=="" && $chessboard[3][8]=="" && $chessboard[4][8]=="" && $chessboard[5][8] instanceof King && $chessboard[1][8] instanceof Rook){
                if($chessboard[5][8]->get_has_moved_status()==false && $chessboard[1][8]->get_has_moved_status()==false){
                    $this->chessboard_obj->test_move($chessboard,1,8,4,8);
                    $this->castling_status = "black_castling_long";
                    return true;
                }
            }
        }
        return false;
    }

    
    private function white_piece_can_move_to_square($target_x, $target_y){
        # first scan all pieces on the board
        for($x=1;$x<=8;$x++){
            for($y=1;$y<=8;$y++){
                    if($this->chessboard[$x][$y] instanceof ChessPiece && $this->chessboard[$x][$y]->get_color()=="white"){
                        if($this->chessboard[$x][$y]->check_move_legal($this->chessboard,$x,$y,$target_x,$target_y)){
                            return true;
                        }
                } 
            }                    
        }
        return false;
    }


    private function black_piece_can_move_to_square($target_x, $target_y){
        # first scan all pieces on the board
        for($x=1;$x<=8;$x++){
            for($y=1;$y<=8;$y++){
                    if($this->chessboard[$x][$y] instanceof ChessPiece && $this->chessboard[$x][$y]->get_color()=="black"){
                        if($this->chessboard[$x][$y]->check_move_legal($this->chessboard,$x,$y,$target_x,$target_y)){
                            return true;
                        }
                } 
            }                    
        }
        return false;
    }


    private function not_castling_through_check_white_short(){
        if($this->black_piece_can_move_to_square(6,1) || $this->black_piece_can_move_to_square(7,1)){
            return false;
        }
        return true;
    }


    private function not_castling_through_check_white_long(){
        if($this->black_piece_can_move_to_square(4,1) || $this->black_piece_can_move_to_square(3,1) || $this->black_piece_can_move_to_square(2,1)){
            return false;
        }
        return true;
    }


    private function not_castling_through_check_black_short(){
        if($this->white_piece_can_move_to_square(6,8) || $this->white_piece_can_move_to_square(7,8)){
            return false;
        }
        return true;
    }


    private function not_castling_through_check_black_long(){
        if($this->white_piece_can_move_to_square(4,8) || $this->white_piece_can_move_to_square(3,8) || $this->white_piece_can_move_to_square(2,8)){
            return false;
        }
        return true;
    }


    private function is_castling_move($chessboard, $current_x, $current_y, $move_to_x, $move_to_y){
        if($chessboard[$current_x][$current_y] instanceof King){
            # white short
        if($current_x==5 && $current_y==1 && $move_to_x==7 && $move_to_y==1){
            return true;
        }
        # white long
        if($current_x==5 && $current_y==1 && $move_to_x==3 && $move_to_y==1){
            return true;
        }
        # black short
        if($current_x==5 && $current_y==8 && $move_to_x==7 && $move_to_y==8){
            return true;
        }
        # black long
        if($current_x==5 && $current_y==8 && $move_to_x==3 && $move_to_y==8){
            return true;
        }
        } 
    }

    private function castling_legal($chessboard,$current_x, $current_y, $move_to_x, $move_to_y){
        // Cannot castle when in check
        if($this->is_check($chessboard, "white") || $this->is_check($chessboard, "black")){
            return false;
        }

        $legal = $this->check_short_castle_white($chessboard,$current_x, $current_y, $move_to_x, $move_to_y) ||
                 $this->check_long_castle_white($chessboard,$current_x, $current_y, $move_to_x, $move_to_y) ||
                 $this->check_short_castle_black($chessboard,$current_x, $current_y, $move_to_x, $move_to_y) ||
                 $this->check_long_castle_black($chessboard,$current_x, $current_y, $move_to_x, $move_to_y);

        return $legal;
    }

    private function handle_enpassant($current_x, $current_y, $move_to_x, $move_to_y){
        $current_square = $this->chessboard[$current_x][$current_y];
        $move_to_square = $this->chessboard[$move_to_x][$move_to_y];

        $this->play_enpassant($current_square, $current_x, $current_y, $move_to_x, $move_to_y, $move_to_square);
        $this->reset_enpassant_possible();
        $this->enable_enpassant_possible($current_square, $current_x, $current_y, $move_to_x, $move_to_y);
    }  
    

    private function play_enpassant($current_square, $current_x, $current_y, $move_to_x, $move_to_y, $move_to_square){
        if($current_square instanceof Pawn && abs($current_x-$move_to_x)==1 && $move_to_square==""){
            $this->chessboard_obj->remove_piece($move_to_x, $current_y);
            $this->gamestatus_json = json_encode(['status' => 'legal', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y",
             'castling' => $this->castling_status, 'enpassant' => 'true', 'remove_piece' => "$move_to_x$current_y"]);
        }
    }

    private function reset_enpassant_possible(){
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


    private function enable_enpassant_possible($current_square, $current_x, $current_y, $move_to_x, $move_to_y){
        if($move_to_x-1>0){
            $left_square = $this->chessboard[$move_to_x-1][$move_to_y];
        }else{
            $left_square = null;
        }
        if($move_to_x+1<9){
            $right_square = $this->chessboard[$move_to_x+1][$move_to_y];
        }else{
            $right_square = null;
        }

        if($current_square instanceof Pawn && abs($current_y-$move_to_y)==2){
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

    public function get_legal_moves($chessboard, $isbotmove){
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
                            if($chessboard[$x][$y]->check_move_legal($chessboard,$x, $y, $move_x,$move_y) ){
                                if($this->check_rules($chessboard,$x,$y,$move_x,$move_y,!$isbotmove)){
                                    $legal_moves[] = [$x,$y,$move_x,$move_y];
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
