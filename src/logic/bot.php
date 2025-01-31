<?php
require_once('piece_square_tables.php');
// evaluate chessboard position: Positive score for white, negative score for black
class Bot{
    private int $counter=0;

    public function evaluate_board($chessboard){
        global $pst_white;
        global $pst_black;
        $white_score = 0;
        $black_score = 0;
        for($x = 1; $x < 9; $x++){
            for($y = 1; $y < 9; $y++){
                if($chessboard[$x][$y]!=""){
                        $field = $chessboard[$x][$y];
                    if($field instanceof ChessPiece){
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
        return $black_score-$white_score;
    }


    public function alpha_beta_pruning($chessboard_obj, $chessboard, $depth, $previous_score,$alpha,$beta ,$isBotMove){
        $legal_moves = $_SESSION['chess_logic']->get_legal_moves($chessboard,$isBotMove);
        $this->counter++;
        if(count($legal_moves)==0 || $depth==0){    
            return [null, $previous_score];
        } 

        $max_value = -PHP_INT_MAX;
        $min_value = PHP_INT_MAX;
        $best_move = null;

        for($i = 0; $i < count($legal_moves); $i++){ // Loop through all legal moves
            $current_x = $legal_moves[$i][0];
            $current_y = $legal_moves[$i][1];
            $move_to_x = $legal_moves[$i][2];
            $move_to_y = $legal_moves[$i][3];
            $move = $current_x.$current_y.$move_to_x.$move_to_y;
            $new_board = $chessboard_obj->test_move($chessboard, $current_x, $current_y, $move_to_x, $move_to_y);
            $new_score = $this->evaluate_board($new_board);
            $node_data = $this->alpha_beta_pruning($chessboard_obj, $new_board, $depth-1, $new_score,$alpha,$beta, !$isBotMove); // evaluate node
            $node_bestscore = $node_data[1];


            if($isBotMove==true){ // Maximize for bot
                if($node_bestscore > $max_value){
                    $max_value = $node_bestscore;
                    $best_move = $move;
                }
                if($node_bestscore > $alpha){
                    $alpha = $node_bestscore;
                }
            }else{ // Minimize for bot
                if($node_bestscore < $min_value){ 
                    $min_value = $node_bestscore;
                    $best_move = $move;
                }
                if($node_bestscore < $beta){
                    $beta = $node_bestscore;
                }
            }

            // Prune branch
            if($alpha >= $beta){
                break;
            }
        }

            if($isBotMove){
                return [$best_move, $max_value];
            }else{
                return [$best_move, $min_value];
            }
    }

    function get_counter(){
        return $this->counter;
    }
}
?>