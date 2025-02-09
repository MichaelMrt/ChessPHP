<?php
require_once("chess_piece.php");

class Pawn extends ChessPiece
{
  public bool $check_enpassant = false;
  public bool $enpassant_left_possible = false;
  public bool $enpassant_right_possible = false;
  protected int $weight = 100;

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

  
  function check_move_legal(mixed $chessboard, Move $move): bool
  {

    return ($this->check_moving_onesquare_forwards($chessboard, $move) or
      $this->check_moving_twosquares_forwards($chessboard, $move) or
      $this->check_diagonal_move($chessboard, $move) or
      $this->check_enpassant($move));
  }


  function check_moving_onesquare_forwards(mixed $chessboard, Move $move):bool
  {
    $is_white_move = $this->color == "white" && $move->from_y + 1 == $move->to_y;
    $is_black_move = $this->color == "black" && $move->from_y - 1 == $move->to_y;
    $same_file = $move->from_x == $move->to_x;
    $target_square_empty = $this->target_square_empty($chessboard, $move);
    return ($is_white_move || $is_black_move) && $same_file && $target_square_empty;
  }


  function check_moving_twosquares_forwards(mixed $chessboard, Move $move):bool
  {
    $is_white_move = $this->color == "white" && $move->from_y == 2 && $move->to_y == 4 && $chessboard[$move->from_x][$move->from_y + 1] == "";
    $is_black_move = $this->color == "black" && $move->from_y == 7 && $move->to_y == 5 && $chessboard[$move->from_x][$move->from_y - 1] == "";
    $same_file = $move->from_x == $move->to_x;
    $target_square_empty = $this->target_square_empty($chessboard, $move);
    return ($is_white_move || $is_black_move) && $same_file && $target_square_empty;
  }


  function check_diagonal_move(mixed $chessboard, Move $move):bool
  {
    $is_takes_move = $chessboard[$move->to_x][$move->to_y] instanceof ChessPiece;
    $is_black_move = $this->color == "black" && $move->from_y - 1 == $move->to_y;
    $is_white_move = $this->color == "white" && $move->from_y + 1 == $move->to_y;
    $is_diagonal_move = abs($move->from_x - $move->to_x) == 1;
    return $is_takes_move && $is_diagonal_move && ($is_black_move || $is_white_move);
  }

  
  function update_position(int $pos_x, int $pos_y):void
  {
    parent::update_position($pos_x, $pos_y);
    $this->check_enpassant = false;
  }

  function set_enpassant_left_possible(bool $bool):void
  {
    $this->enpassant_left_possible = $bool;
  }

  function set_enpassant_right_possible(bool $bool):void
  {
    $this->enpassant_right_possible = $bool;
  }


  function get_enpassant_left_possible():bool
  {
    return $this->enpassant_left_possible;
  }

  function get_enpassant_right_possible():bool
  {
    return $this->enpassant_right_possible;
  }

  function check_enpassant(Move $move):bool
  {
    if($this->color == "white"){
      if($this->enpassant_left_possible){
        if($move->to_x== $this->x-1 && $move->to_y == $this->y+1){
          return true;
        }
      }
      if($this->enpassant_right_possible){
        if($move->to_x== $this->x+1 && $move->to_y == $this->y+1){
          return true;
        }
      }
   }else{
      if($this->enpassant_left_possible){
        if($move->to_x== $this->x-1 && $move->to_y == $this->y-1){
          return true;
        }
      }
      if($this->enpassant_right_possible){
        if($move->to_x== $this->x+1 && $move->to_y == $this->y-1){
          return true;
        }
      }
    }
    return false;
  }


  function target_square_empty(mixed $chessboard, Move $move): bool
  {
    if ($chessboard[$move->to_x][$move->to_y] == "") {
      return true;
    }else{
      return false;
    }
  }
}
