<!DOCTYPE html> 
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    </style>
    <link rel="stylesheet" href="css.css">
</head>
<body>

<div class="floating-shape shape1"></div>
<div class="floating-shape shape2"></div>
<div class="floating-shape shape3"></div>
<div class="floating-shape shape4"></div>
<div class="floating-shape shape5"></div>

<div class="container">
    <header>
        <h1 id="aboutTitle">O meni</h1>
        <p id="aboutText">Zdravo!
        Ja sam junior web programer i dobrodošli na moj portfolio. Ovde možete videti neke od mojih radova:</p>
    </header>

    <div class="projects">
        <div class="project-card">
            <button onclick="location.href='GameHub/indexgame.php'">GameHub</button>
            <p class="project-description">Platforma za više igara koje sam kreirao koristeci JavaScript i jednostavne css i html elemente.</p>
        </div>
        <div class="project-card">
            <button onclick="location.href='E-shop/indexEshop.php'">E-shop Ribar</button>
            <p class="project-description">E-shop kreiran koriscenjem vanila php-a i MySQL-a</p>
        </div>
        <div class="project-card">
            <button onclick="location.href='News/index.php'">News</button>
            <p class="project-description">News stranica koja ima razne mogucnosti korisnika od komentara,pretplate itd.</p>
        </div>
        <div class="project-card">
            <button onclick="location.href='Photo-editor/index.php'">Photo-Editor</button>
            <p class="project-description">Aplikacija koja omogucava editovanje slika.</p>
        </div>
    </div>
</div>


<div class="translate-btn">
    <button onclick="toggleLanguage()">English</button>
</div>

<script>
    function toggleLanguage() {
        const aboutTitle = document.getElementById('aboutTitle');
        const aboutText = document.getElementById('aboutText');
        const button = document.querySelector('.translate-btn button');
        
        if (document.documentElement.lang === 'sr') {
            aboutTitle.textContent = 'About Me';
            aboutText.textContent = 'Hello! I am a junior web developer, and welcome to my portfolio. Here you can see some of my work:';
            document.querySelectorAll('.project-description')[0].textContent = "A platform for multiple games I created by using JavaScript and simple css and html.";
            document.querySelectorAll('.project-description')[1].textContent = "E-Shop created with mainly php and MySQL-a";
            document.querySelectorAll('.project-description')[2].textContent = "A news website with various user features, including comments, subscriptions, and more.";
            document.querySelectorAll('.project-description')[3].textContent = "Aplikacija koja omogucava editovanje slika.";
            button.textContent = 'Srpski';
            document.documentElement.lang = 'en';
        } else {
            aboutTitle.textContent = 'O meni';
            aboutText.textContent = 'Ja sam junior web programer i dobrodošli na moj portfolio. Ovde možete videti neke od mojih radova:';
            document.querySelectorAll('.project-description')[0].textContent = "Platforma za više igara koje sam kreirao koristeci JavaScript i jednostavne css i html elemente.";
            document.querySelectorAll('.project-description')[1].textContent = "E-shop kreiran uz koriscenje vanila php-a i MySQL-a";
            document.querySelectorAll('.project-description')[2].textContent = "News stranica koja ima razne mogucnosti korisnika od komentara,pretplate itd.";
            document.querySelectorAll('.project-description')[3].textContent = "An application that allows image editing.";
            button.textContent = 'English';
            document.documentElement.lang = 'sr';
        }
    }
</script>

</body>
</html>
