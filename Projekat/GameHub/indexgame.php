<!DOCTYPE html> 
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameSet</title>    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="container text-center mt-5">
        <!-- Row za naslov i dugme -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 id="page-title" class="mb-0">Odaberi igru</h1>
            <a href="../index.php" class="btn btn-primary" id="back-button">Nazad na Portfolio</a>
        </div>
        
        <div class="row justify-content-center">
            <?php
            $games = [
                [
                    'title' => 'Attack of the mushroom',
                    'image' => 'slike/curke.png',
                    'description' => 'Ova igra je akcioni triler pun uzbuđenja.'
                ],
                [
                    'title' => 'Puzzle',
                    'image' => 'slike/puzzle.png',
                    'description' => 'Ova igra nudi uzbudljive puzzle izazove.'
                ],
                [
                    'title' => 'Snake',
                    'image' => 'slike/snake.png',
                    'description' => 'Ova igra je avantura u otvorenom svetu sa puno slobode.'
                ]
            ];

            foreach ($games as $index => $game) {
                echo "
                <div class='col-md-4 mb-4'>
                    <a href='igra" . ($index + 1) . ".php' class='card'> <!-- Dodano href -->
                        <img src='{$game['image']}' class='card-img-top game-image' alt='{$game['title']}'>
                        <div class='card-body'>
                            <h5 id='game-title-$index' class='card-title'>{$game['title']}</h5>
                            <p id='game-desc-$index' class='card-text'>{$game['description']}</p>
                        </div>
                    </a>
                </div>
                ";
            }
            ?>
        </div>
    </div>

    <button class="translate-btn" onclick="translatePage()">EN</button>

    <footer>
        <div class="container">
            <p id="footer-created">Created By: <strong>NS</strong></p>
            <p id="footer-links-title">Linkovi:</p>
            <a href="mailto:yourname@gmail.com" class="text-dark mx-2">Gmail: nikolastevanovic449@gmail.com</a>
            <a href="https://github.com/Dzoni449/project-one" target="_blank" class="text-dark mx-2">GitHub</a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const translations = {
            "sr": {
                "page-title": "Odaberi igru",
                "footer-created": "Kreirano od strane: NS",
                "footer-links-title": "Linkovi:",
                "back-button": "Nazad na Portfolio",
                "games": [
                    { "title": "Attack of the mushroom", "description": "Ova igra je akcioni triler pun uzbuđenja." },
                    { "title": "Puzzle", "description": "Ova igra nudi uzbudljive puzzle izazove." },
                    { "title": "Jump Jack", "description": "Ova igra je avantura u otvorenom svetu sa puno slobode." }
                ]
            },
            "en": {
                "page-title": "Choose a game",
                "footer-created": "Created By: NS",
                "footer-links-title": "Links:",
                "back-button": "Back to Portfolio",
                "games": [
                    { "title": "Attack of the mushroom", "description": "This game is an action-packed thriller." },
                    { "title": "Beat the puzzle", "description": "This game offers exciting puzzle challenges." },
                    { "title": "Jump Jack", "description": "This game is an open-world adventure with lots of freedom." }
                ]
            }
        };

        let currentLang = "sr"; 

        function translatePage() {
            currentLang = currentLang === "sr" ? "en" : "sr";

            document.getElementById("page-title").textContent = translations[currentLang]["page-title"];
            document.getElementById("footer-created").textContent = translations[currentLang]["footer-created"];
            document.getElementById("footer-links-title").textContent = translations[currentLang]["footer-links-title"];
            document.getElementById("back-button").textContent = translations[currentLang]["back-button"];

            translations[currentLang]["games"].forEach((game, index) => {
                document.getElementById(`game-title-${index}`).textContent = game.title;
                document.getElementById(`game-desc-${index}`).textContent = game.description;
            });
        }
    </script>
</body>
</html>
