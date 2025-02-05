<?php
class Move{
    public int $from_x;
    public int $from_y;
    public int $to_x;
    public int $to_y;

    function __construct(int $from_x, int $from_y, int $to_x, int $to_y)
    {
        $this->from_x = $from_x;
        $this->from_y = $from_y;
        $this->to_x = $to_x;
        $this->to_y = $to_y;
    }
}
?>