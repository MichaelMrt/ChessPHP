<?php
require_once("chess_piece.php");

class Pawn extends ChessPiece
{
  public $check_enpassant = false;
  public $enpassant_left_possible = false;
  public $enpassant_right_possible = false;

  function __construct(String $color, int $x, int $y)
  {
    parent::__construct($color, $x, $y);
    $this->type = 'pawn';
    
    if ($color == 'white') {
      $this->icon = "<img src='../images/chesspieces/white-pawn.png' class='chesspiece'>";
    } elseif ($color == 'black') {
      $this->icon = "<img src='../images/chesspieces/black-pawn.png' class='chesspiece'>";
    }
  }

  
  function check_move_legal(mixed $chessboard, int $move_to_x, int $move_to_y): bool
  {
    # Coordinates from the current Piece position
    $current_x = $this->x;
    $current_y = $this->y;

    return $this->check_moving_onesquare_forwards($current_x, $current_y, $move_to_x, $move_to_y) or
      $this->check_moving_twosquares_forwards($chessboard, $current_x, $current_y, $move_to_x, $move_to_y) or
      $this->check_diagonal_move($chessboard, $current_x, $current_y, $move_to_x, $move_to_y) or
      $this->check_enpassant($chessboard, $current_x, $current_y, $move_to_x, $move_to_y);
  }


  function check_moving_onesquare_forwards($current_x, $current_y, $move_to_x, $move_to_y)
  {
    $is_white_move = $this->color == "white" && $current_y + 1 == $move_to_y;
    $is_black_move = $this->color == "black" && $current_y - 1 == $move_to_y;
    $same_file = $current_x == $move_to_x;
    return ($is_white_move || $is_black_move) && $same_file;
  }


  function check_moving_twosquares_forwards($chessboard, $current_x, $current_y, $move_to_x, $move_to_y)
  {
    $is_white_move = $this->color == "white" && $current_y == 2 && $move_to_y == 4 && $chessboard[$current_x][$current_y + 1] == "";
    $is_black_move = $this->color == "black" && $current_y == 7 && $move_to_y == 5 && $chessboard[$current_x][$current_y - 1] == "";
    $same_file = $current_x == $move_to_x;
    return ($is_white_move || $is_black_move) && $same_file;
  }


  function check_diagonal_move($chessboard, $current_x, $current_y, $move_to_x, $move_to_y)
  {
    $is_takes_move = is_a($chessboard[$move_to_x][$move_to_y], "ChessPiece");
    $is_black_move = $this->color == "black" && $current_y - 1 == $move_to_y;
    $is_white_move = $this->color == "white" && $current_y + 1 == $move_to_y;
    $is_diagonal_move = abs($current_x - $move_to_x) == 1;
    return $is_takes_move && $is_diagonal_move && ($is_black_move || $is_white_move);
  }

  
  function update_position($pos_x, $pos_y)
  {
    parent::update_position($pos_x, $pos_y);
    $this->check_enpassant = false;
  }

  function set_enpassant_left_possible()
  {
    $this->enpassant_left_possible = true;
  }

  function set_enpassant_right_possible()
  {
    $this->enpassant_right_possible = true;
  }

  function check_enpassant($chessboard, $current_x, $current_y, $move_to_x, $move_to_y){
    if($this->color == "white"){
      if($this->enpassant_left_possible){
        if($move_to_x == $this->x-1 && $move_to_y == $this->y+1){
          return true;
        }
      }
      if($this->enpassant_right_possible){
        if($move_to_x == $this->x+1 && $move_to_y == $this->y+1){
          return true;
        }
      }
   }else{
      if($this->enpassant_left_possible){
        if($move_to_x == $this->x-1 && $move_to_y == $this->y-1){
          return true;
        }
      }
      if($this->enpassant_right_possible){
        if($move_to_x == $this->x+1 && $move_to_y == $this->y-1){
          return true;
        }
      }
    }
    return false;
  }
}
