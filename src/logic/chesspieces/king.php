<?php
class King extends ChessPiece
{
  protected int $weight = 60000;
  function __construct(String $color, int $x, int $y)
  {
      parent::__construct($color, $x, $y);
      $this->type = 'king';

      if($color=='white'){
        $this->icon ="<img src='../images/chesspieces/white-king.png' class='chesspiece'>";
      }elseif($color=='black'){
        $this->icon ="<img src='../images/chesspieces/black-king.png' class='chesspiece'>";
      }
  }

  function check_move_legal(mixed $chessboard, Move $move):bool
    {
      $distance_x = sqrt(pow(($move->to_x-$move->from_x),2)); 
      $distance_y = sqrt(pow(($move->to_y-$move->from_y),2)); 

      if($distance_x <= 1 && $distance_y <= 1){
        # check if there is a piece on the move to square and if it is opposite color
         if($chessboard[$move->to_x][$move->to_y] instanceof ChessPiece &&  $chessboard[$move->from_x][$move->from_y]->get_color()!=$chessboard[$move->to_x][$move->to_y]->get_color()){
          return true;
         }elseif(!$chessboard[$move->to_x][$move->to_y] instanceof ChessPiece){
          return true;
         }
      }

      if($this->has_moved==false){
      # castling short as white
        if($this->color=='white' && $move->to_x==7 && $move->to_y==1){
            if(!$chessboard[6][1] instanceof ChessPiece && !$chessboard[7][1] instanceof ChessPiece){
              if($chessboard[8][1] instanceof Rook && $chessboard[8][1]->has_moved==false){
                return true;
              }
            }
        }

        # castling long as white
        if($this->color=='white' && $move->to_x==3 && $move->to_y==1){
          if(!$chessboard[2][1] instanceof ChessPiece && !$chessboard[3][1] instanceof ChessPiece && !$chessboard[4][1] instanceof ChessPiece){
            if($chessboard[1][1] instanceof Rook && $chessboard[1][1]->has_moved==false){
              return true;
            }
          }
      }

      # castling short as black
      if($this->color=='black' && $move->to_x==7 && $move->to_y==8){
        if(!$chessboard[6][8] instanceof ChessPiece && !$chessboard[7][8] instanceof ChessPiece){
          if($chessboard[8][8] instanceof Rook && $chessboard[8][8]->has_moved==false){
            return true;
          }
        }
      }

      # castling long as black
      if($this->color=='black' && $move->to_x==3 && $move->to_y==8){
        if(!$chessboard[2][8] instanceof ChessPiece && !$chessboard[3][8] instanceof ChessPiece && !$chessboard[4][8] instanceof ChessPiece){
          if($chessboard[8][8] instanceof Rook && $chessboard[8][8]->has_moved==false){
            return true;
          }
        }
      }
  }

        return false;
    }
}
?>