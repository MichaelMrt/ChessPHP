<?php
require_once('piece_square_tables.php');

function evaluate_board($chessboard){
    global $pst_white;
    global $pst_black;
    $white_score = 0;
    $black_score = 0;


    foreach($chessboard as $row){
        foreach($row as $field){
            if(is_a($field,'Chesspiece')){
                $type = $field->get_type();
                $x = $field->get_x()-1;
                $y = 8 - $field->get_y();

                if($field->get_color()=='white'){
                    $white_score += $field->get_weight();
                    $white_score += $pst_white[$type][$y][$x];
                }else{
                    $black_score += $field->get_weight();
                    $black_score += $pst_black[$type][$y][$x];
                }
            }
        }
}
        return $white_score - $black_score;
}
?>