function handle_SquareSelection(id, piece) {
        
    const square = document.getElementById(id);
    const piece_on_square = piece!='';
    console.log(piece);
    console.log("Square clicked: " + id);

    //if no square is selected yet
    if(handle_SquareSelection.square_selected==undefined){ 
        document.querySelectorAll('.square').forEach(f => f.classList.remove('highlight'));
        if(piece_on_square){ 
            select_square(square);
            handle_SquareSelection.piece = piece;
        }else{
            console.log("No Piece on that square");
        }
    }else if(handle_SquareSelection.square_selected==true){ //if a square is selected
        var selected_piece_id = handle_SquareSelection.highlighted_square.id;
        var move_to_id = id;
        process_highlighting(square);
        sendMove(selected_piece_id, move_to_id);
        handle_SquareSelection.square_selected=undefined
    }
}


function select_square(square){
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

function sendMove(selected_piece_id, move_to_id){
    console.log("move:"+selected_piece_id+" "+move_to_id)

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'logic/logic.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            //Output server response
            console.log(xhr.responseText);
            var response = JSON.parse(xhr.responseText);
            console.log(response);
            if (response.status === 'legal') {
                console.log("Move is legal");
                movePiece(selected_piece_id, move_to_id)
            }else {
                console.log("Move is illegal! "+"selected_piece_id: "+selected_piece_id+" move_to_id:"+move_to_id);
            }
            document.getElementById('ajax_response').innerHTML = xhr.responseText;
        }else {
            console.error('An error occured while sending the move: ' + xhr.statusText);
        }
    };
    xhr.send('move_to_id=' + encodeURIComponent(move_to_id)+'&selected_piece_id='+encodeURIComponent(selected_piece_id));
}

function movePiece(selected_piece_id, move_to_id){
    var selected_square = document.getElementById(selected_piece_id);
    chesspiece_icon = selected_square.innerHTML;
    selected_square.innerHTML='';

    var move_to_square = document.getElementById(move_to_id);
    move_to_square.innerHTML = chesspiece_icon;

    // Update onclick
    var move_to_x = String(move_to_id).charAt(0);
    var move_to_y = String(move_to_id).charAt(1);

    selected_square_attribute = selected_square.getAttribute("onclick");
    var newOnclickValue = selected_square_attribute.replace(/"x":\d+/, `"x":${move_to_x}`).replace(/"y":\d+/, `"y":${move_to_y}`);
        newOnclickValue = newOnclickValue.replace(selected_piece_id, move_to_id);
        
    move_to_square.setAttribute("onclick",newOnclickValue);
    selected_square.removeAttribute("onclick");
}