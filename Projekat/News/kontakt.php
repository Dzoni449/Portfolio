<?php    
session_start();
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stil/politika.css"> 
    <style>
        #col {          
            color: #e74c3c; 
        }
        .contact-container {
            max-width: 800px;
            margin: 40px auto; 
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #eaeaea; 
        }
        .contact-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .contact-info {
            font-size: 1.1em;
            line-height: 1.5;
            color: #555;
            padding: 10px; 
            border-top: 2px solid #e74c3c; 
        }
        .contact-info p {
            margin: 10px 0; 
        }
        .contact-info a {
            color: #e74c3c; 
            text-decoration: none; 
        }
        .contact-info a:hover {
            text-decoration: underline; 
        }
        .map-container {
            margin: 40px auto; 
            max-width: 800px; 
            border: 1px solid #eaeaea; 
            border-radius: 8px; 
            overflow: hidden; 
        }
        .map-container iframe {
            width: 100%; 
            height: 400px; 
            border: 0; 
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

<main>
    <section id="contact-section">
        <div class="contact-container">
            <h2>Kontakt informacije</h2>
            <div class="contact-info">
                <p><strong>Email:</strong> <a href="mailto:info@vesti.com">info@vesti.com</a></p>
                <p><strong>GitHub:</strong> <a href="https://github.com/Dzoni449" target="_blank">GitHub</a></p>
                <p><strong>Telefon:</strong> +381 12 345 6789</p>
                <p><strong>Mesto:</strong> Nis, Srbija</p>
            </div>
        </div>
        
        <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d23222.34802119669!2d21.917399181346973!3d43.31858200869301!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ssr!2srs!4v1728135432735!5m2!1ssr!2srs" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2024 Kontakt - Sva prava zadržana</p>
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

</body>
</html>
