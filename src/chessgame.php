<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Chessboard</title>
</head>
<?php
    require_once("logic/logic.php");
    $logic = new logic();

    
    

    # footer
    echo "<div class='center'>
    <h1>footer</h1>
    <p> Contribute on GitHub:<a href='https://github.com/MichaelMrt03/ChessPHP'>https://github.com/MichaelMrt03/ChessPHP</a></p>
    </div>";
    
?>

<script>
    function highlight_square(id) {
        
        const square = document.getElementById(id);
        
        // if field is highlighted remove the highlight
        if (square.classList.contains('highlight')) {
            square.classList.remove('highlight');
        } else {
            // if not highlighted add highlight
            document.querySelectorAll('.feld').forEach(f => f.classList.remove('highlight'));
            square.classList.add('highlight');
        }

        // Output
        console.log("Square clicked: " + id);
    }
</script>