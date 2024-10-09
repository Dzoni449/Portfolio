<?php
session_start();
require_once 'database/conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: ' . ($user['role'] === 'admin' ? 'dashboard.php' : 'index.php'));
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}

?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stil/index.css">
    
    <style>
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
                    <li><a href="#politika">Politika</a></li>
                    <li><a href="#sport">Sport</a></li>
                    <li><a href="#tehnologija">Tehnologija</a></li>
                    <li><a href="#kontakt">Kontakt</a></li>
                    <li><a href="login.php" id="col" >Prijava</a></li>
                    <li><a href="register.php" id="col">Registracija</a></li>
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

<div class="login-register-container">
        <div class="login-register-box">
            <h2>Prijava</h2>
            <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
            <form method="POST" action="login.php">
                <input type="text" name="username" placeholder="Korisničko ime" required>
                <input type="password" name="password" placeholder="Lozinka" required>
                <button type="submit">Prijavi se</button>
            </form>
            <p>Nemate nalog? <a href="register.php">Registrujte se ovde</a>.</p>
        </div>
</div>

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
</body>
</html>
