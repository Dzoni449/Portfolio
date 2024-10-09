<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake Game</title>
    <link rel="stylesheet" href="style/stylegame3.css">
    <style>
        /* Footer styling */
        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: #fff;
            position: fixed;
            width: 100%;
            bottom: 0;
            left: 0;
        }
        /* Page bottom padding so content does not overlap with footer */
        body {
            margin-bottom: 60px; /* Adds space to accommodate the footer */
        }
    </style>
</head>
<body>

<div class="game-container">
    <div class="controls">
        <button onclick="startGame()">Start Game</button>
        <button onclick="goToMenu()">Game Menu</button>
        <button onclick="toggleLanguage()">Language</button>
    </div>

    <div style="display: flex;">
        <div class="game-board" id="gameBoard"></div>
        <div class="instructions" id="instructions">
            <h3>Instructions:</h3>
            <p>Use the following keys to move the snake:</p>
            <ul>
                <li><strong>W</strong> - Up</li>
                <li><strong>A</strong> - Left</li>
                <li><strong>S</strong> - Down</li>
                <li><strong>D</strong> - Right</li>
            </ul>
        </div>
    </div>

    <div id="gameOverMessage" style="display: none;">Game Over!</div>
    <div id="winMessage" style="display: none;">Congrats, you beat the game!</div>
</div>

<!-- Footer for created by message -->
<footer>
    <p>Created by: NS</p>
</footer>

<script>
    const boardSize = 400;
    const gridSize = 20;
    let snake = [{ x: 100, y: 100 }];
    let food = { x: 0, y: 0 };
    let direction = { x: 0, y: 0 };
    let gameInterval;
    let gameBoard = document.getElementById('gameBoard');
    let gameOverMessage = document.getElementById('gameOverMessage');
    let winMessage = document.getElementById('winMessage');
    let instructions = document.getElementById('instructions');

    document.addEventListener('keydown', changeDirection);

    function startGame() {
        resetGame();
        placeFood();
        direction = { x: gridSize, y: 0 };
        gameInterval = setInterval(updateGame, 200);
        gameOverMessage.style.display = 'none';
        winMessage.style.display = 'none';
    }

    function resetGame() {
        clearInterval(gameInterval);
        snake = [{ x: 100, y: 100 }];
        direction = { x: 0, y: 0 };
        gameBoard.innerHTML = '';
        drawSnake();
    }

    function updateGame() {
        const head = { x: snake[0].x + direction.x, y: snake[0].y + direction.y };
        
        if (head.x < 0 || head.x >= boardSize || head.y < 0 || head.y >= boardSize || checkSnakeCollision(head)) {
            endGame(false);
            return;
        }

        snake.unshift(head); 

        if (head.x === food.x && head.y === food.y) {
            placeFood();
        } else {
            snake.pop(); 
        }

        drawSnake();

        if (snake.length * gridSize >= boardSize) {
            endGame(true); 
        }
    }

    function drawSnake() {
        gameBoard.innerHTML = '';
        snake.forEach((part, index) => {
            const snakeElement = document.createElement('div');
            snakeElement.classList.add('snake');
            snakeElement.style.left = part.x + 'px';
            snakeElement.style.top = part.y + 'px';

            if (index === 0) {
                const eye = document.createElement('div');
                eye.classList.add('eye');
                snakeElement.appendChild(eye);
            }

            gameBoard.appendChild(snakeElement);
        });

        const foodElement = document.createElement('div');
        foodElement.classList.add('food');
        foodElement.style.left = food.x + 'px';
        foodElement.style.top = food.y + 'px';
        gameBoard.appendChild(foodElement);
    }

    function changeDirection(event) {
        const key = event.key.toLowerCase();
        switch (key) {
            case 'w':
                if (direction.y === 0) direction = { x: 0, y: -gridSize }; 
                break;
            case 'a':
                if (direction.x === 0) direction = { x: -gridSize, y: 0 }; 
                break;
            case 's':
                if (direction.y === 0) direction = { x: 0, y: gridSize }; 
                break;
            case 'd':
                if (direction.x === 0) direction = { x: gridSize, y: 0 }; 
                break;
        }
    }

    function placeFood() {
        const x = Math.floor(Math.random() * (boardSize / gridSize)) * gridSize;
        const y = Math.floor(Math.random() * (boardSize / gridSize)) * gridSize;
        food = { x: x, y: y };
    }

    function checkSnakeCollision(head) {
        return snake.some(part => part.x === head.x && part.y === head.y);
    }

    function endGame(won) {
        clearInterval(gameInterval);
        if (won) {
            winMessage.style.display = 'block';
            gameOverMessage.style.display = 'none';
        } else {
            gameOverMessage.style.display = 'block';
            winMessage.style.display = 'none';
        }
    }

    function goToMenu() {
        window.location.href = 'indexgame.php';
    }

    function toggleLanguage() {
        const currentLang = instructions.getAttribute('data-language');

        if (currentLang === 'en') {
            instructions.setAttribute('data-language', 'sr');
            instructions.innerHTML = `
                <h3>Uputstvo:</h3>
                <p>Koristite sledeće tastere za pomeranje zmije:</p>
                <ul>
                    <li><strong>W</strong> - Gore</li>
                    <li><strong>A</strong> - Levo</li>
                    <li><strong>S</strong> - Dole</li>
                    <li><strong>D</strong> - Desno</li>
                </ul>
            `;
        } else {
            instructions.setAttribute('data-language', 'en');
            instructions.innerHTML = `
                <h3>Instructions:</h3>
                <p>Use the following keys to move the snake:</p>
                <ul>
                    <li><strong>W</strong> - Up</li>
                    <li><strong>A</strong> - Left</li>
                    <li><strong>S</strong> - Down</li>
                    <li><strong>D</strong> - Right</li>
                </ul>
            `;
        }
    }
</script>

</body>
</html>
