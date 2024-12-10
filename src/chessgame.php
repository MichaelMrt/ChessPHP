<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="javascript.js"></script>
    <title>Chessboard</title>
</head>
<body>
<?php
    require_once("logic/logic.php");
    
    $logic = new logic(); //start game
    $_SESSION['chess_game'] = $logic;

    echo "<div id='ajax_response'></div>";
    # footer
    echo "<div class='center'>
    <h1>footer</h1>
    <p> Contribute on GitHub:<a href='https://github.com/MichaelMrt03/ChessPHP'>https://github.com/MichaelMrt03/ChessPHP</a></p>
    </div>";
?>
</body>
</html>