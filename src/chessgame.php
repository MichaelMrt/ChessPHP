<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Chessboard</title>
</head>
<?php
    require_once("chessboard.php");

    
    $chessboard = new chessboard();

    # footer
    echo "<div class='center'>
    <h1>footer</h1>
    <p> Contribute on GitHub:<a href='https://github.com/MichaelMrt03/ChessPHP'>https://github.com/MichaelMrt03/ChessPHP</a></p>
    </div>";
    
?>