<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attack of The Mushroom</title>
    <link rel="stylesheet" href="style/stylegame1.css">
    <style>
      

    </style>
</head>
<body>

    <div class="game-container">
        <button class="start-button" onclick="startGame()">Pokreni igru</button>
        <button class="menu-button" onclick="goToMenu()">Game Menu</button>

        <div class="score">Poeni: 0</div>
        <div class="game-over">Kraj igre!</div>
        <div class="character">
            <div class="character-head">
                <div class="eye left"></div>
                <div class="eye right"></div>
            </div>

            <div class="arm left"></div>
            <div class="arm right">
                <div class="lightsaber"></div> 
            </div>

            <div class="leg left"></div>
            <div class="leg right"></div> 
        </div>

        <div class="ground">Kreirano od strane: NS </div>
    </div>

    <script>
        let score = 0; 
        let gameStarted = false;
        let mushroomInterval;
        let activeMushrooms = []; // Array za praćenje aktivnih pečuraka

        function startGame() {
            gameStarted = true;
            score = 0; 
            document.querySelector('.start-button').style.display = 'none'; 
            document.querySelector('.menu-button').style.display = 'none'; // Sakrij dugme "Game Menu"
            document.querySelector('.game-over').style.display = 'none'; 
            document.querySelector('.score').innerText = `Poeni: ${score}`;
            spawnMushroom();
            mushroomInterval = setInterval(spawnMushroom, 1000); // Povećava broj pečuraka svake sekunde
        }

        function goToMenu() {
    window.location.href = 'indexgame.php'; // Preusmeravanje na index.php
}
        function spawnMushroom() {
            const mushroom = document.createElement('div');
            mushroom.className = 'mushroom';
            activeMushrooms.push(mushroom); // Dodaj pečurku u aktivne

            // Odredi slučajnu poziciju za pečurku
            const direction = Math.floor(Math.random() * 4); // 0: levo, 1: desno, 2: gore, 3: dole
            const gameContainer = document.querySelector('.game-container');
            const containerRect = gameContainer.getBoundingClientRect();

            switch(direction) {
                case 0: // Levo
                    mushroom.style.left = '-30px';
                    mushroom.style.top = Math.random() * (containerRect.height - 30) + 'px';
                    break;
                case 1: // Desno
                    mushroom.style.left = (containerRect.width) + 'px';
                    mushroom.style.top = Math.random() * (containerRect.height - 30) + 'px';
                    break;
                case 2: // Gore
                    mushroom.style.left = Math.random() * (containerRect.width - 30) + 'px';
                    mushroom.style.top = '-30px';
                    break;
                case 3: // Dole
                    mushroom.style.left = Math.random() * (containerRect.width - 30) + 'px';
                    mushroom.style.top = (containerRect.height) + 'px';
                    break;
            }

            document.body.appendChild(mushroom);
            moveMushroom(mushroom);
        }

        function moveMushroom(mushroom) {
            const character = document.querySelector('.character');
            const characterRect = character.getBoundingClientRect();

            const moveInterval = setInterval(() => {
                if (!gameStarted) {
                    clearInterval(moveInterval); // Zaustavi kretanje kada igra završi
                    return;
                }

                const mushroomRect = mushroom.getBoundingClientRect();

                // Pomeraj pečurku ka liku
                let dx = characterRect.left - mushroomRect.left;
                let dy = characterRect.top - mushroomRect.top;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < 5) {
                    clearInterval(moveInterval);
                    endGame(); // Završava igru
                } else {
                    // Normalizuj pravac kretanja
                    dx /= distance;
                    dy /= distance;

                    mushroom.style.left = (mushroomRect.left + dx * 4) + 'px'; // Povećaj pomeraj na 4
                    mushroom.style.top = (mushroomRect.top + dy * 4) + 'px'; // Povećaj pomeraj na 4

                    // Proveri da li je lightsaber dodirnuo pečurku
                    const lightsaber = document.querySelector('.lightsaber');
                    const lightsaberRect = lightsaber.getBoundingClientRect();

                    if (lightsaberRect.left < mushroomRect.right &&
                        lightsaberRect.right > mushroomRect.left &&
                        lightsaberRect.top < mushroomRect.bottom &&
                        lightsaberRect.bottom > mushroomRect.top) {
                        score++;
                        updateScore();
                        clearInterval(moveInterval);
                        mushroom.remove(); // Ukloni pečurku
                        activeMushrooms = activeMushrooms.filter(m => m !== mushroom); // Ukloni iz aktivnih
                    }
                }
            }, 50);
        }

        function updateScore() {
            document.querySelector('.score').innerText = `Poeni: ${score}`;
        }

        function endGame() {
            clearInterval(mushroomInterval);
            gameStarted = false; // Završi igru
            document.querySelector('.game-over').style.display = 'block';
    
            const replayButton = document.createElement('button');
            replayButton.innerText = 'Igraj ponovo';
            replayButton.style.position = 'absolute';
            replayButton.style.top = '60%';
            replayButton.style.left = '50%';
            replayButton.style.transform = 'translate(-50%, -50%)';
            replayButton.style.padding = '10px 20px';
            replayButton.style.fontSize = '20px';
            replayButton.style.backgroundColor = '#ffcc00';
            replayButton.style.border = 'none';
            replayButton.style.borderRadius = '5px';
            replayButton.style.cursor = 'pointer';
            document.body.appendChild(replayButton);

            replayButton.onclick = () => {
            location.reload(); // Ponovno pokretanje igre
            };

            // Dodaj dugme za Game Menu
        const menuButton = document.createElement('button');
            menuButton.innerText = 'Game Menu';
            menuButton.style.position = 'absolute';
            menuButton.style.top = '70%'; // Pomeranje dugmeta malo niže
            menuButton.style.left = '50%';
            menuButton.style.transform = 'translate(-50%, -50%)';
            menuButton.style.padding = '10px 20px';
            menuButton.style.fontSize = '20px';
            menuButton.style.backgroundColor = '#ffcc00';
            menuButton.style.border = 'none';
            menuButton.style.borderRadius = '5px';
            menuButton.style.cursor = 'pointer';
            document.body.appendChild(menuButton);

    menuButton.onclick = () => {
        window.location.href = 'indexgame.php'; // Preusmeravanje na index.php
            };
        }

        // Praćenje miša i rotacija lightsabera
        document.addEventListener('mousemove', function(event) {
            if (!gameStarted) return; // Ako igra nije počela, ne pomeraj mač
            const lightsaber = document.querySelector('.lightsaber');
            const character = document.querySelector('.character');

            const characterRect = character.getBoundingClientRect();
            const characterX = characterRect.left + characterRect.width / 2;
            const characterY = characterRect.top + characterRect.height / 2;

            const mouseX = event.clientX;
            const mouseY = event.clientY;

            const deltaX = mouseX - characterX;
            const deltaY = mouseY - characterY;
            const angle = Math.atan2(deltaY, deltaX) * 180 / Math.PI;

            lightsaber.style.transform = `rotate(${angle}deg)`;
        });
    </script>
</body>
</html>
