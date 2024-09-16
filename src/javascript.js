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
            //do something
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