<?php
session_start();

require_once 'database/conn.php';

$sql = "SELECT * FROM articles WHERE status = 'published' ORDER BY published_date DESC LIMIT 4";
$result = mysqli_query($con, $sql);

?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vesti</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stil/index.css">
    <style>
        #featured-news {
            padding: 70px 0;
            background: url('slike/background.jpg') no-repeat center center/cover; 
            position: relative;
        }
        #col{          
            color:#e74c3c; 
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script defer src="menu.js"></script> 
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>Vesti<span>.</span></h1>
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Početna</a></li>
                    <li><a href="politika.php">Politika</a></li>
                    <li><a href="sport.php">Sport</a></li>
                    <li><a href="tehnologija.php">Tehnologija</a></li>
                    <li><a href="kontakt.php">Kontakt</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="logout.php" id="col">Odjavi se</a></li>
                    <?php else: ?>
                        <li><a href="login.php" id="col">Prijava</a></li>
                        <li><a href="register.php" id="col">Registracija</a></li>
                    <?php endif; ?>
                </ul>
                <div class="mobile-menu-icon" id="mobile-menu-icon">
                    <i class="fas fa-bars"></i> 
                </div>
            </nav>
        </div>
    </header>

    <section id="breaking-news">
    <div class="ticker-wrap">
        <div class="ticker">
            <div class="ticker-item">Vesti: Novi zakon o radu usvojen u Skupštini...</div>
            <div class="ticker-item">Sport: Srbija pobedila Brazil u finalu...</div>
            <div class="ticker-item">Tehnologija: Novi iPhone 15 Pro izlazi sledeće nedelje...</div>
        </div>
    </div>
</section>

<section id="featured-news"> 
    <div class="overlay"></div> 
    <div class="container">
        <div class="headline">
            <h2>Najnovije Vesti</h2>
        </div>
        <div class="news-grid">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <article class="news-item">
                <div class="news-content">
                    <h3><?= htmlspecialchars($row['title']); ?></h3>
                    <p><?= htmlspecialchars(substr($row['content'], 0, 100)); ?>... <a href="article.php?id=<?= $row['id']; ?>">Pročitajte više</a></p>
                    </div>
            </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>

    <footer>
        <div class="container">
            <p>&copy; 2024 Vesti. Sva prava zadržana NS.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuIcon = document.getElementById('mobile-menu-icon');
            const navLinks = document.querySelector('.nav-links');

            mobileMenuIcon.addEventListener('click', function() {
                navLinks.classList.toggle('show');
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
        const tickerItems = document.querySelectorAll('.ticker-item');
        let tickerIndex = 0;

        setInterval(() => {
            tickerItems[tickerIndex].style.display = 'none';
            tickerIndex = (tickerIndex + 1) % tickerItems.length;
            tickerItems[tickerIndex].style.display = 'inline-block';
        }, 5000); 
    });
    </script>
    <a href="../index.php" class="back-to-portfolio">Nazad na portfolio</a>

</body>
</html>
