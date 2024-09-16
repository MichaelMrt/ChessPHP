<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $move = isset($_POST['move']) ? $_POST['move'] : '';
    if(isset($move)){
        $move=$_POST['move'];
    }else{
        $move = 'Error while processing the move';
    }


    echo "Move played: ".$move;
}
?>
