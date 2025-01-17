<?php
require_once('piece_square_tables.php');

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


function minimax($chessboard_obj,$chessboard, $depth,$score, $isBotMove){


    $legal_moves = $chessboard_obj->get_legal_moves($isBotMove);
    $best_score = -100000;

    if(count($legal_moves)==0 || $depth==0){
        return $best_score;
    }

    

    for($i = 0; $i < count($legal_moves); $i++){
        $current_x = $legal_moves[$i][0];
        $current_y = $legal_moves[$i][1];
        $move_to_x = $legal_moves[$i][2];
        $move_to_y = $legal_moves[$i][3];
        $new_board = $chessboard_obj->test_move($chessboard, $current_x, $current_y, $move_to_x, $move_to_y);
        $score = evaluate_board($new_board);

        $scores_tracker[$i] = $current_x.$current_y.$move_to_x.$move_to_y." ".$score;

        if($score > $best_score){
            $best_score = $score;
        }
        $score = minimax($chessboard_obj, $new_board, $depth-1, $score, !$isBotMove);   
    }


    return $scores_tracker;
   }
?>