<?php
class Knight extends ChessPiece
{
    function __construct(String $color, int $x, int $y)
    {
      parent::__construct($color, $x, $y);
      $this->type = 'knight';

      if($color=='white'){
        $this->icon ="<img src='../images/chesspieces/white-knight.png' class='chesspiece'>";
      }elseif($color=='black'){
        $this->icon ="<img src='../images/chesspieces/black-knight.png' class='chesspiece'>";
      }
    }

    function check_move_legal(mixed $chessboard, int $move_to_x, int $move_to_y):bool
    {
         # 1-up, 2-right jump
         # check if moving in legal pattern, then check if its moving to a field with a piece and if its opposite colors
         if($move_to_y==$this->y+1 && $move_to_x==$this->x+2){
          if(is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece')){
              if($chessboard[$this->x][$this->y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
                return true;
              }
          }else{
            return true;
          }
        }
        # 1-up, 2-left jump
        if($move_to_y==$this->y+1 && $move_to_x==$this->x-2){
          if(is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece')){
            if($chessboard[$this->x][$this->y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
              return true;
            }
        }else{
          return true;
        }
        }
        # 1-down, 2-right jump
        if($move_to_y==$this->y-1 && $move_to_x==$this->x+2){
          if(is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece')){
            if($chessboard[$this->x][$this->y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
              return true;
            }
        }else{
          return true;
        }
        }
        # 1-down, 2-left jump
        if($move_to_y==$this->y-1 && $move_to_x==$this->x-2){
          if(is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece')){
            if($chessboard[$this->x][$this->y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
              return true;
            }
        }else{
          if(is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece')){
            if($chessboard[$this->x][$this->y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
              return true;
            }
        }else{
          return true;
        }
        }
        }


        # 2-up, 1-right jump
        if($move_to_y==$this->y+2 && $move_to_x==$this->x+1){
          if(is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece')){
            if($chessboard[$this->x][$this->y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
              return true;
            }
        }else{
          return true;
        }
        }
        # 2-up, 1-left jump
        if($move_to_y==$this->y+2 && $move_to_x==$this->x-1){
          if(is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece')){
            if($chessboard[$this->x][$this->y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
              return true;
            }
        }else{
          return true;
        }
        }
        # 2-down, 1-right jump
        if($move_to_y==$this->y-2 && $move_to_x==$this->x+1){
          if(is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece')){
            if($chessboard[$this->x][$this->y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
              return true;
            }
        }else{
          return true;
        }
        }
        # 2-down, 1-left jump
        if($move_to_y==$this->y-2 && $move_to_x==$this->x-1){
          if(is_a($chessboard[$move_to_x][$move_to_y],'Chesspiece')){
            if($chessboard[$this->x][$this->y]->get_color()!=$chessboard[$move_to_x][$move_to_y]->get_color()){
              return true;
            }
        }else{
          return true;
        }
        }

        $_SESSION['error'] = "<p class='error'>knights can't move like that</p>";

        return false;
    }
}
?>