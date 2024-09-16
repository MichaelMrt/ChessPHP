function highlight_square(id, piece) {
        
    const square = document.getElementById(id);
    console.log(piece);
    if(piece!=''){
        console.log("Piece exists")
    }else{
        console.log("Piece doesnt exist")
    }

    // if field is highlighted remove the highlight
    if (square.classList.contains('highlight')) {
        square.classList.remove('highlight');
    } else {
        // if not highlighted add highlight
        document.querySelectorAll('.square').forEach(f => f.classList.remove('highlight'));
        square.classList.add('highlight');
    }

    // Output
    console.log("Square clicked: " + id);
}