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
    protected bool $whitesturn=true; // has to be removed
    protected bool $white_in_check=false;
    protected bool $black_in_check=false;
    protected $gamestatus_json;
    protected string $castling_status = "none";
    protected string $gamemode;
    
    function __construct($gamemode)
    {   
        $this->chessboard_obj = new chessboard();
        $this->chessboard = $this->chessboard_obj->get_board();
        $this->gamemode = $gamemode;
    }


    function test($x,$y):void
    {
        if($this->chessboard[$x][$y]==""){
            
        }else{
            print("PIECE ON THIS SQUARE:".$x." ".$y."\n");
        }
    }

    function input_move(int $current_x, int $current_y, int $move_to_x, int $move_to_y):void
    {   
        if($this->chessboard[$current_x][$current_y]==""){
            throw new Exception("No piece on this square");
        }
        if($this->check_rules($current_x, $current_y,$move_to_x,$move_to_y)){           
                # move is legal
                $this->gamestatus_json = json_encode(['status' => 'legal', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y", 'castling' => $this->castling_status]);
                $this->handle_enpassant($current_x, $current_y, $move_to_x, $move_to_y);
                $this->castling_status = "none";  
                $this->chessboard = $this->chessboard_obj->move($this->chessboard, (int) $current_x, (int) $current_y,(int) $move_to_x, (int) $move_to_y);
                $this->whitesturn = !$this->whitesturn; # swap turns
                $this->is_check($this->chessboard);
                $this->is_checkmate($this->chessboard);
                echo $this->gamestatus_json;
        }else{
            echo $this->gamestatus_json;
        }
    }


    #just for testing purpose
    function debug_output_board(): void
    {
        echo "<pre>";
        print_r($this->chessboard);
        echo "</pre>";
    }
 

    function check_rules(int $current_x, int $current_y, int $move_to_x, int $move_to_y):bool
    {

       if($this->wrong_turn_order($current_x,$current_y)){
        $this->gamestatus_json =  json_encode(['status' => 'illegal', 'message' => 'Wrong move order', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);    
        return false;
       }

      if($this->still_check($current_x, $current_y, $move_to_x, $move_to_y)){
        $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Still in check', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);    
        return false;
      }

      if($this->self_check($current_x, $current_y, $move_to_x, $move_to_y)){
        $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Cannot move into check', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);    
        return false;
      }

      $piece = $this->chessboard[$current_x][$current_y];
      if($piece->check_move_legal($this->chessboard,$current_x,$current_y, (int) $move_to_x, (int) $move_to_y)==false){
        $this->gamestatus_json = json_encode(['status' => 'illegal', 'message' => 'Piece cannot move like that', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);    
        return false;
      }

      if($this->is_castling_move($current_x, $current_y, $move_to_x, $move_to_y)){
        if($this->castling_legal($current_x, $current_y, $move_to_x, $move_to_y)==false){
            echo json_encode(['status' => 'illegal', 'message' => 'Castling through check is  illegal', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y"]);    
            return false;
        }
      }

      return true;
    }


    function get_player_on_move():bool
    {
        return $this->whitesturn;
    }


    function is_check(mixed $chessboard):bool
    {   $king_pos = $this->get_king_pos($chessboard);
        # check if king is in check
        for ($x=1; $x < 9; $x++) { 
            for ($y=1; $y < 9; $y++) { 
                
                if(is_a($chessboard[$x][$y],'ChessPiece')){
                    if($chessboard[$x][$y]->get_color()=="black" && $chessboard[$x][$y]->check_move_legal($chessboard,$x,$y,$king_pos['white']['x'],$king_pos['white']['y'])){
                        $this->white_in_check = true;
                        $this->gamestatus_json = json_encode(['status' => 'legal','check' => 'white in check']);           
                        return true;
                    }
                    if($chessboard[$x][$y]->get_color()=="white" && $chessboard[$x][$y]->check_move_legal($chessboard,$x,$y,$king_pos['black']['x'],$king_pos['black']['y'])){
                        $this->black_in_check = true;
                        $this->gamestatus_json = json_encode(['status' => 'legal','check' => 'black in check']);
                       return true;
                    }
                }
            }
        }
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
                                           if($this->chessboard[$x][$y]->check_move_legal($chessboard,$x,$y,$move_x,$move_y)){
                                               $future_board = $this->chessboard_obj->test_move($chessboard, $x, $y, $move_x, $move_y); 
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
                                           if($this->chessboard[$x][$y]->check_move_legal($chessboard,$x,$y,$move_x,$move_y)){
                                               $future_board = $this->chessboard_obj->test_move($chessboard, $x, $y, $move_x, $move_y); 
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
                    $gamestatus_array = json_decode($this->gamestatus_json, true);
                    $gamestatus_array['info'] = "There is a legal move";
                    $this->gamestatus_json = json_encode($gamestatus_array);
                }else{
                    if($white_checkmated){
                        $this->gamestatus_json = json_encode(['status' => 'legal','checkmate' => 'white is checkmated - black wins']);
                    }
                    if($black_checkmated){
                        $this->gamestatus_json = json_encode(['status' => 'legal','checkmate' => 'black checkmated - white wins']);
                    }
                    
                }
            }
           
        return $move_out_of_check;

    }

    function wrong_turn_order($current_x, $current_y){
         # check if white moves only his pieces
         if($this->whitesturn && $this->chessboard[$current_x][$current_y]->get_color()=="black"){
            return true;
        }

        # check if black only moves his pieces
        if(!$this->whitesturn && $this->chessboard[$current_x][$current_y]->get_color()=="white"){
            return true;
        }

        return false;
    }

    function still_check($current_x, $current_y, $move_to_x, $move_to_y){
        # When a player is in check he has to make sure his next move stops the check
      if($this->is_check($this->chessboard)){
        $controll_board = $this->chessboard_obj->test_move($this->chessboard, (int) $current_x, (int) $current_y, (int) $move_to_x, (int) $move_to_y);   
        if($this->is_check($controll_board)){
            return true;
        }
     } 
     return false;
    }


    function self_check($current_x, $current_y, $move_to_x, $move_to_y){
      # make sure the next move does not result in check for the same color
      # => King cannot move into check
      if($this->whitesturn){
            $controll_board = $this->chessboard_obj->test_move($this->chessboard, (int) $current_x, (int) $current_y, (int) $move_to_x, (int) $move_to_y);   
            if($this->is_check($controll_board)){
                if($this->white_in_check){
                    return true;
                }
            }
      
      }else{ # blacksturn
        $controll_board = $this->chessboard_obj->test_move($this->chessboard, (int) $current_x, (int) $current_y, (int) $move_to_x, (int) $move_to_y);   
            if($this->is_check($controll_board)){ 
                if($this->black_in_check){
                    return true;
                }
            }
        }
     return false;
    }

    function check_short_castle_white($current_x, $current_y, $move_to_x, $move_to_y){
        if($current_x==5 && $current_y==1 && $move_to_x==7 && $move_to_y==1 && $this->not_castling_through_check_white_short()){
            $this->chessboard = $this->chessboard_obj->move($this->chessboard,8,1,6,1);
            $this->castling_status = "white_castling_short";
            return true; 
        }
        return false;
    }


    function check_long_castle_white($current_x, $current_y, $move_to_x, $move_to_y){
        if($current_x==5 && $current_y==1 && $move_to_x==3 && $move_to_y==1 && $this->not_castling_through_check_white_long()){
            $this->chessboard = $this->chessboard_obj->move($this->chessboard,1,1,4,1);
            $this->castling_status = "white_castling_long";
            return true;
        }
        return false;
    }


    function check_short_castle_black($current_x, $current_y, $move_to_x, $move_to_y){
        if($current_x==5 && $current_y==8 && $move_to_x==7 && $move_to_y==8 && $this->not_castling_through_check_black_short()){
            $this->chessboard = $this->chessboard_obj->move($this->chessboard,8,8,6,8);
            $this->castling_status = "black_castling_short";
            return true; 
        }
        return false;
    }


    function check_long_castle_black($current_x, $current_y, $move_to_x, $move_to_y){
        if($current_x==5 && $current_y==8 && $move_to_x==3 && $move_to_y==8 && $this->not_castling_through_check_black_long()){
            $this->chessboard = $this->chessboard_obj->move($this->chessboard,1,8,4,8);
            $this->castling_status = "black_castling_long";
            return true;
        }
        return false;
    }

    
    function white_piece_can_move_to_square($target_x, $target_y){
        # first scan all pieces on the board
        for($x=1;$x<=8;$x++){
            for($y=1;$y<=8;$y++){
                    if(is_a($this->chessboard[$x][$y],'ChessPiece')&&$this->chessboard[$x][$y]->get_color()=="white"){
                        if($this->chessboard[$x][$y]->check_move_legal($this->chessboard,$x,$y,$target_x,$target_y)){
                            return true;
                        }
                } 
            }                    
        }
        return false;
    }


    function black_piece_can_move_to_square($target_x, $target_y){
        # first scan all pieces on the board
        for($x=1;$x<=8;$x++){
            for($y=1;$y<=8;$y++){
                    if(is_a($this->chessboard[$x][$y],'ChessPiece')&&$this->chessboard[$x][$y]->get_color()=="black"){
                        if($this->chessboard[$x][$y]->check_move_legal($this->chessboard,$x,$y,$target_x,$target_y)){
                            return true;
                        }
                } 
            }                    
        }
        return false;
    }


    function not_castling_through_check_white_short(){
        if($this->black_piece_can_move_to_square(6,1) || $this->black_piece_can_move_to_square(7,1)){
            return false;
        }
        return true;
    }


    function not_castling_through_check_white_long(){
        if($this->black_piece_can_move_to_square(4,1) || $this->black_piece_can_move_to_square(3,1) || $this->black_piece_can_move_to_square(2,1)){
            return false;
        }
        return true;
    }


    function not_castling_through_check_black_short(){
        if($this->white_piece_can_move_to_square(6,8) || $this->white_piece_can_move_to_square(7,8)){
            return false;
        }
        return true;
    }


    function not_castling_through_check_black_long(){
        if($this->white_piece_can_move_to_square(4,8) || $this->white_piece_can_move_to_square(3,8) || $this->white_piece_can_move_to_square(2,8)){
            return false;
        }
        return true;
    }


    function is_castling_move($current_x, $current_y, $move_to_x, $move_to_y){
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


    function castling_legal($current_x, $current_y, $move_to_x, $move_to_y){
        $legal = $this->check_short_castle_white($current_x, $current_y, $move_to_x, $move_to_y) ||
                 $this->check_long_castle_white($current_x, $current_y, $move_to_x, $move_to_y) ||
                 $this->check_short_castle_black($current_x, $current_y, $move_to_x, $move_to_y) ||
                 $this->check_long_castle_black($current_x, $current_y, $move_to_x, $move_to_y);

        return $legal;
    }

    function handle_enpassant($current_x, $current_y, $move_to_x, $move_to_y){
        $current_square = $this->chessboard[$current_x][$current_y];
        $move_to_square = $this->chessboard[$move_to_x][$move_to_y];

        $this->play_enpassant($current_square, $current_x, $current_y, $move_to_x, $move_to_y, $move_to_square);
        $this->reset_enpassant_possible();
        $this->enable_enpassant_possible($current_square, $current_x, $current_y, $move_to_x, $move_to_y);
    }  
    

    function play_enpassant($current_square, $current_x, $current_y, $move_to_x, $move_to_y, $move_to_square){
        if($current_square instanceof Pawn && abs($current_x-$move_to_x)==1 && $move_to_square==""){
            $this->chessboard_obj->remove_piece($move_to_x, $current_y);
            $this->gamestatus_json = json_encode(['status' => 'legal', 'from' =>"$current_x$current_y", 'to' => "$move_to_x$move_to_y",
             'castling' => $this->castling_status, 'enpassant' => 'true', 'remove_piece' => "$move_to_x$current_y"]);
        }
    }


    function reset_enpassant_possible(){
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


    function enable_enpassant_possible($current_square, $current_x, $current_y, $move_to_x, $move_to_y){
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

    function get_chessboard():mixed
    {
        return $this->chessboard;
    }

    function get_chessboard_obj():Chessboard
    {
        return $this->chessboard_obj;
    }

    function check_rules_bot(int $current_x, int $current_y, int $move_to_x, int $move_to_y):bool
    {

    //    if($this->wrong_turn_order($current_x,$current_y)){
    //     return false;
    //    }

    //   if($this->still_check($current_x, $current_y, $move_to_x, $move_to_y)){
    //     return false;
    //   }

    //   if($this->self_check($current_x, $current_y, $move_to_x, $move_to_y)){
    //     return false;
    //   }

    //   $piece = $this->chessboard[$current_x][$current_y];
    //   if($piece->check_move_legal($this->chessboard, (int) $move_to_x, (int) $move_to_y)==false){
    //     return false;
    //   }

    //   if($this->is_castling_move($current_x, $current_y, $move_to_x, $move_to_y)){
    //     if($this->castling_legal($current_x, $current_y, $move_to_x, $move_to_y)==false){
    //         return false;
    //     }
    //   }
      return true;
    }

    function get_legal_moves($chessboard, $whitesturn){
        $legal_moves = [];
        if($whitesturn){
            $color = "white";
        }else{
            $color = "black";
        }

        for($x=1;$x<=8;$x++){
            for($y=1;$y<=8;$y++){
                if(is_a($chessboard[$x][$y],'ChessPiece') && $chessboard[$x][$y]->get_color()==$color){
                    for($move_x=1;$move_x<=8;$move_x++){
                        for($move_y=1;$move_y<=8;$move_y++){
                            if($chessboard[$x][$y]->check_move_legal($chessboard,$x, $y, $move_x,$move_y) ){
                                if($this->check_rules_bot($x,$y,$move_x,$move_y)){
                                    $legal_moves[] = [$x,$y,$move_x,$move_y];
                                }
                                
                            }
                        }
                    }
                }
            }
        }
        
        // var_dump($legal_moves);
        return $legal_moves;
    }
}
