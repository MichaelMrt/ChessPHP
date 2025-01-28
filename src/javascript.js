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
    xhr.open('POST', 'logic/process_move.php', true);
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
                get_bot_move();
            }else {
                console.log("Move is illegal! "+"selected_piece_id: "+selected_piece_id+" move_to_id:"+move_to_id);
            }

            // Castling
            if(response.castling=='white_castling_short'){
                console.log("Castling accepted");
                movePiece(81,61);
            }
            else if(response.castling=='white_castling_long'){
                console.log("Castling accepted");
                movePiece(11,41);
            }
            else if(response.castling=='black_castling_short'){
                console.log("Castling accepted");
                movePiece(88,68);
            }
            else if(response.castling=='black_castling_long'){
                console.log("Castling accepted");
                movePiece(18,48);
            }

            // En passant
            if(response.enpassant=='true'){
                remove_piece(document.getElementById(response.remove_piece));
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
    var move_to_square = document.getElementById(move_to_id);
    var chesspiece_img = selected_square.querySelector('img');

    transition_chesspiece(selected_square, move_to_square, chesspiece_img);

    setTimeout(function(){
        chesspiece_img.style.transform = ''; // reset transformation

        move_html_img(selected_square, move_to_square)
        update_onclick_attribute(selected_square, move_to_square, selected_piece_id, move_to_id)
    }, 500); 
}


function transition_chesspiece(selected_square, move_to_square, chesspiece_img){
// Calculate position from selected_square and move_to_square
var selected_square_rect = selected_square.getBoundingClientRect();
var move_to_square_rect = move_to_square.getBoundingClientRect();

// Calculate the differenz of the positions
var deltaX = move_to_square_rect.left - selected_square_rect.left;
var deltaY = move_to_square_rect.top - selected_square_rect.top;

// Set the position to absolute to move the img
chesspiece_img.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
}


function update_onclick_attribute(selected_square, move_to_square, selected_piece_id, move_to_id){
            // Update onclick attribute
            var move_to_x = String(move_to_id).charAt(0);
            var move_to_y = String(move_to_id).charAt(1);
        
            selected_square_attribute = selected_square.getAttribute("onclick");
            var moveToOnclickValue = selected_square_attribute.replace(/"x":\d+/, `"x":${move_to_x}`).replace(/"y":\d+/, `"y":${move_to_y}`);
                moveToOnclickValue = moveToOnclickValue.replace(selected_piece_id, move_to_id);
            move_to_square.setAttribute("onclick",moveToOnclickValue);
            selected_square.setAttribute("onclick",`handle_SquareSelection(${selected_piece_id},'')`);
}


function move_html_img(selected_square, move_to_square){
    chesspiece_icon = selected_square.innerHTML;
    selected_square.innerHTML='';
    move_to_square.innerHTML = chesspiece_icon;
}


function remove_piece(square){
    square.innerHTML='';
}

async function get_bot_move(){
    const startTime = Date.now();
    //await new Promise(r => setTimeout(r, 500));
    console.log("BOT REQUESTED MOVE");

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'logic/process_bot_move.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            const endTime = Date.now();
            const duration = endTime - startTime; 
            console.log(`Bot-request duration: ${duration} ms`);
            //Output server response
            console.log(xhr.responseText);
            var response = JSON.parse(xhr.responseText);
            console.log(response);
            if (response.status === 'legal') {
                console.log("Move is legal");
                movePiece(response.from, response.to)
            }else {
                console.log("Move is illegal!");
            }

            // Castling
            if(response.castling=='white_castling_short'){
                console.log("Castling accepted");
                movePiece(81,61);
            }
            else if(response.castling=='white_castling_long'){
                console.log("Castling accepted");
                movePiece(11,41);
            }
            else if(response.castling=='black_castling_short'){
                console.log("Castling accepted");
                movePiece(88,68);
            }
            else if(response.castling=='black_castling_long'){
                console.log("Castling accepted");
                movePiece(18,48);
            }

            // En passant
            if(response.enpassant=='true'){
                remove_piece(document.getElementById(response.remove_piece));
            }

            document.getElementById('ajax_response').innerHTML = xhr.responseText;
        }else {
            console.error('An error occured while sending the move: ' + xhr.statusText);
        }
    };
    xhr.send('move_to_id=' + 'test'+'&selected_piece_id='+'test');
}