<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puzzle Game</title>
    <link rel="stylesheet" href="style/stylegame2.css">
</head>
<body>

<div class="game-container">
    <div class="controls">
        <button onclick="startGame()">Start Game</button>
        <input type="file" id="imageUpload" accept="image/*" />
        <button onclick="goToMenu()">Game Menu</button>
        <div id="uploadText">Možete uploudovati sliku za puzzle</div>
    </div>

    <div class="puzzle-board" id="puzzleBoard"></div>

    <div id="congratulations">Congratulations! Puzzle Completed.</div>

    <img src="slike/pecurke.png" id="defaultImage" class="default-image" alt="Default Puzzle Image">
</div>


<footer>
    <p>Created by: NS</p>
</footer>

<script>
    let selectedImage = null;
    let puzzlePieces = [];
    const puzzleBoard = document.getElementById('puzzleBoard');
    const defaultImage = document.getElementById('defaultImage');
    const snapThreshold = 20; 

    document.getElementById('imageUpload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                selectedImage = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    function startGame() {       
        document.getElementById('congratulations').style.display = 'none'; // Sakrijemo 'congrats' na početku igre        
        const image = selectedImage || defaultImage.src;
        createPuzzle(image);
    }

    function createPuzzle(imageSrc) {
        puzzleBoard.innerHTML = '';
        puzzlePieces = [];

        const rows = 3;
        const cols = 2;
        const pieceWidth = puzzleBoard.offsetWidth / cols;
        const pieceHeight = puzzleBoard.offsetHeight / rows;

        const positions = [];

        for (let row = 0; row < rows; row++) {
            for (let col = 0; col < cols; col++) {
                const xPos = col * pieceWidth;
                const yPos = row * pieceHeight;

                const piece = document.createElement('div');
                piece.className = 'puzzle-piece';
                piece.style.width = `${pieceWidth}px`;
                piece.style.height = `${pieceHeight}px`;
                piece.style.left = `${xPos}px`;
                piece.style.top = `${yPos}px`;
                piece.style.backgroundImage = `url(${imageSrc})`;
                piece.style.backgroundPosition = `-${xPos}px -${yPos}px`;

                // Originalna pozicija
                piece.dataset.originalX = xPos;
                piece.dataset.originalY = yPos;

                // Dodaj delove na tablu
                puzzleBoard.appendChild(piece);
                puzzlePieces.push(piece);

                positions.push({ x: xPos, y: yPos });
            }
        }

        shufflePuzzle(positions); // Promesaj
        makePiecesDraggable();
    }

    function shufflePuzzle(positions) {
        positions.sort(() => Math.random() - 0.5);

        puzzlePieces.forEach((piece, index) => {
            const pos = positions[index];
            piece.style.left = `${pos.x}px`;
            piece.style.top = `${pos.y}px`;
        });
    }

    function makePiecesDraggable() {
        puzzlePieces.forEach(piece => {
            piece.addEventListener('mousedown', onDragStart);

            function onDragStart(e) {
                const rect = puzzleBoard.getBoundingClientRect();
                const offsetX = e.clientX - parseInt(piece.style.left);
                const offsetY = e.clientY - parseInt(piece.style.top);

                function onMouseMove(e) {
                    let newX = e.clientX - offsetX;
                    let newY = e.clientY - offsetY;

                    // Pokreti
                    if (newX < 0) newX = 0;
                    if (newY < 0) newY = 0;
                    if (newX + piece.offsetWidth > rect.width) newX = rect.width - piece.offsetWidth;
                    if (newY + piece.offsetHeight > rect.height) newY = rect.height - piece.offsetHeight;

                    piece.style.left = `${newX}px`;
                    piece.style.top = `${newY}px`;
                }

                function onMouseUp() {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);

                    // Proveri da li je puzzle piece dovoljno blizu da se sam ubaci u mesto
                    snapPieceToPlace(piece);
                    checkIfPuzzleSolved();
                }

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            }
        });
    }

    function snapPieceToPlace(piece) {
        const currentX = parseInt(piece.style.left);
        const currentY = parseInt(piece.style.top);
        const originalX = parseInt(piece.dataset.originalX);
        const originalY = parseInt(piece.dataset.originalY);

        if (Math.abs(currentX - originalX) <= snapThreshold && Math.abs(currentY - originalY) <= snapThreshold) {
            piece.style.left = `${originalX}px`;
            piece.style.top = `${originalY}px`;
        }
    }

    function checkIfPuzzleSolved() {
        let isSolved = true;
        puzzlePieces.forEach(piece => {
            const currentX = parseInt(piece.style.left);
            const currentY = parseInt(piece.style.top);
            const originalX = parseInt(piece.dataset.originalX);
            const originalY = parseInt(piece.dataset.originalY);

            if (currentX !== originalX || currentY !== originalY) {
                isSolved = false;
            }
        });

        if (isSolved) {
            document.getElementById('congratulations').style.display = 'block';
        }
    }

    function goToMenu() {
        window.location.href = 'indexgame.php';
    }
</script>

</body>
</html>
