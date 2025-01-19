<?php
require_once('piece_square_tables.php');

// evaluate chessboard position: Positive score for white, negative score for black
function evaluate_board($chessboard){
    global $pst_white;
    global $pst_black;
    $white_score = 0;
    $black_score = 0;
    for($x = 1; $x < 9; $x++){
        for($y = 1; $y < 9; $y++){
            if($chessboard[$x][$y]!=""){
                    $field = $chessboard[$x][$y];
                if(is_a($field,'Chesspiece')){
                    $type = $field->get_type();
                    $piece_y = 8 - $y;
                    if($field->get_color()=='white'){
                        $white_score += $field->get_weight();
                        $white_score += $pst_white[$type][$piece_y][$x-1];
                    }else{
                        $black_score += $field->get_weight();
                        $black_score += $pst_black[$type][$piece_y][$x-1];
                    }
                }
            }
        }
    }
    return $white_score - $black_score;
}


function minimax($chessboard_obj, $chessboard, $depth, $previous_score, $isBotMove){
    $legal_moves = $chessboard_obj->get_legal_moves(!$isBotMove);

    if(count($legal_moves)==0 || $depth==0){
        return [null, $previous_score];
    }
    
    if($isBotMove==true){
        $best_score = 100000;
    }
    if($isBotMove==false){
        $best_score = -100000;
    }

    for($i = 0; $i < count($legal_moves); $i++){ // Loop through all legal moves
        $current_x = $legal_moves[$i][0];
        $current_y = $legal_moves[$i][1];
        $move_to_x = $legal_moves[$i][2];
        $move_to_y = $legal_moves[$i][3];
        $move = $current_x.$current_y.$move_to_x.$move_to_y;
        $new_board = $chessboard_obj->test_move($chessboard, $current_x, $current_y, $move_to_x, $move_to_y);
        $new_score = evaluate_board($new_board);
        $node_score = minimax($chessboard_obj, $new_board, $depth-1, $new_score, !$isBotMove); // evaluate node  

        if($isBotMove==true){ // Maximize for bot
            if($node_score[1] < $best_score){
                $best_score = $node_score[1];
                $best_move = $move;
            }
        }

        if($isBotMove==false){ // Minimize for bot
            if($node_score[1] > $best_score){ 
                $best_score = $node_score[1];
                $best_move = $move;
            }
        }
    }
    return [$best_move, $best_score];
   }
?>