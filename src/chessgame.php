<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="javascript.js"></script>
    <title>Chess</title>
</head>
<body>
<?php
    session_start();
    require_once("logic/logic.php");

    if( isset($_GET['gamemode'])){
        $gamemode = $_GET['gamemode'];
    }else{
        $gamemode = "solo";
    }

    if( isset($_GET['color'])){
        $color = $_GET['color'];
    }else
        $color = "white";

    // echo $gamemode ." ". $color;
    echo "<h1> ChessPHP </h1>";
    echo "<div class='container'>";

    echo "<div class='side'>"; 
    // echo "<h1> Kurzinfo </h1>";
    echo "</div>";

    echo "<div class='center'>";
    $logic = new Logic($gamemode); //start game
    $_SESSION['chess_logic'] = $logic;
    echo "</div>";

    echo "<div class='side'> <h1>Funktionsweise</h1>";
    echo "Klicke auf eine Figur, um sie auszuwählen,<br> und anschließend auf das Feld, <br>
    auf das sie bewegt werden soll, <br>
    um deinen Zug auszuführen.<br>
    Ist der Zug legal, <br>
    wird er ausgeführt und der Bot zieht im Anschluss.</div>";

    echo "</div>";

    echo "<div id='ajax_response'></div>";

    echo "<footer class='footer'>
    <p>
        © 2024 | <a href='https://github.com/MichaelMrt/ChessPHP' target='_blank'>
            Contribute on GitHub: https://github.com/MichaelMrt/ChessPHP
            <span class='icon'><img src='../docs/gh-icon.png' alt='https://github.com/MichaelMrt/ChessPHP'></span>
        </a>
    </p>
</footer>
";
?>
</body>
</html>