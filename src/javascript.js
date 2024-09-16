function handle_SquareSelection(id, piece) {
        
    const square = document.getElementById(id);
    const piece_on_square = piece!='';
    console.log(piece);

    if(handle_SquareSelection.square_selected==undefined){ //No Square highlighted yet
        if(piece_on_square){ 
            process_piece_on_square(square);
        }else{
            console.log("No Piece on that square")
        }
    }else{ // A square is highlighted already
        if(piece_on_square){ 
            handle_SquareSelection.highlighted_square.classList.remove('highlight'); 
            process_piece_on_square(square);
        }else{
            //No Piece on that square, but a beforehand a piece was selected, try to move there
            selected_piece = handle_SquareSelection.highlighted_square.id
            move_to = id
            sendMove(selected_piece, move_to)
        }
    }


    console.log("Square clicked: " + id);
}

function process_piece_on_square(square){
    console.log("Piece on that square");
    square.classList.add('highlight');
    handle_SquareSelection.square_selected=true;
    handle_SquareSelection.highlighted_square=square;
}

function process_highlighting(square){
    // if field is already highlighted remove the highlight
    if (square.classList.contains('highlight')) {
        square.classList.remove('highlight');
    } else {
        // if not highlighted add highlight
        document.querySelectorAll('.square').forEach(f => f.classList.remove('highlight'));
        square.classList.add('highlight');
    }
}

function sendMove(selected_piece, move_to){
    console.log("move:"+selected_piece+" "+move_to)

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'server.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            //Output server response
            document.getElementById('ajax_response').innerHTML = xhr.responseText;
        } else {
            console.error('An error occured while sending the move: ' + xhr.statusText);
        }
    };

    xhr.send('move=' + encodeURIComponent(move_to));
}