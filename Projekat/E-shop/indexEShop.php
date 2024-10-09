<!DOCTYPE html>  
<html lang="sr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-shop Ribolovačka Oprema</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header h1 {
            font-size: 1.8rem;
            letter-spacing: 2px;
        }

        header nav ul {
            display: flex;
            list-style: none;
        }

        header nav ul li {
            margin-left: 20px;
        }

        header nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s;
        }

        header nav ul li a:hover {
            color: #f39c12;
        }

        .hero {
            position: relative;
            background: url('slike/fishing.jpg') no-repeat center center/cover;
            height: 80vh;
            display: flex;
            justify-content: center; 
            align-items: center;
            text-align: center;
            color: white;
            flex-direction: column;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6); 
            z-index: 1;
        }

        .hero .content {
            position: relative;
            z-index: 2;
            max-width: 800px;
        }

        .hero h2 {
            font-size: 3rem;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.8);
            margin-bottom: 20px;
            color: #f39c12; 
        }

        .hero p {
            font-size: 1.2rem;
            margin-top: 10px;
            max-width: 600px;
            margin-bottom: 30px;
            color: #ecf0f1;
            text-align: center;
        }

        .cta-button {
            padding: 15px 30px;
            background-color: #e74c3c; 
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .cta-button:hover {
            background-color: #c0392b;
        }

        .categories {
            padding: 50px 20px;
            text-align: center;
            background-color: #ecf0f1; 
        }

        .categories h3 {
            font-size: 2rem;
            margin-bottom: 30px;
            color: #2c3e50; 
        }

        .category-grid {
            display: flex;
            justify-content: space-around;
            max-width: 1200px;
            margin: 0 auto;
            gap: 20px;
            flex-wrap: wrap;
        }

        .category {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s;
            flex: 1;
            max-width: 300px;
        }

        .category img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .category h4 {
            margin: 15px;
            font-size: 1.5rem;
            color: #e67e22; 
        }

        .category p {
            margin: 15px;
            font-size: 1rem;
            color: #777;
        }

        .category:hover {
            transform: translateY(-10px);
            cursor: pointer;
        }

        footer {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        footer a {
            color: #f39c12;
            text-decoration: none;
            margin: 0 10px;
        }

        footer a:hover {
            color: #ddd;
        }

        /* Stil za viseće dugme */
        .floating-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 20px;
            background-color: #e67e22;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s, box-shadow 0.3s;
            z-index: 1000; /* Dugme iznad ostalog sadržaja */
        }

        .floating-button:hover {
            background-color: #d35400;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        #textdec{
            text-decoration:none;
        }
        #textdec:hover{
            opacity:80%;
        }
    </style>
</head>

<body>

    
    <header>
        <div class="container">
            <h1>E-shop Ribar</h1>
            <nav>
                <ul>
                    <li><a href="indexEShop.php">Početna</a></li>
                    <li><a href="proizvodi.php">Proizvodi</a></li>
                    <li><a href="kontakt.php">Kontakt</a></li>
                    <li><a href="korpa.php">Korpa</a></li>
                </ul>
            </nav>
        </div>
    </header>

    
    <section class="hero"> 
        <div class="content">
            <h2>Najbolja oprema za vaš sledeći ribolovački poduhvat</h2>
            <p>Pronađite vrhunsku ribolovačku opremu, od štapova do mamaca, sve na jednom mestu!</p>
            <a href="proizvodi.php">
                <button class="cta-button">Pogledaj ponudu</button>
            </a>
        </div>
    </section>

    
    <section class="categories">
        <h3>Izdvojene Kategorije</h3>
        <div class="category-grid">
            <a href="proizvodi.php?category=1" class="category" id="textdec">
                <img src="slike/fishing rod.jpg" alt="Štapovi za pecanje">
                <h4>Štapovi za pecanje</h4>
                <p>Najbolji izbor kvalitetnih štapova za sve vrste ribolova.</p>
            </a>
            <a href="proizvodi.php?category=2" class="category" id="textdec">
                <img src="slike/lures.jpg" alt="Mamci i varalice">
                <h4>Mamci i varalice</h4>
                <p>Veliki izbor mamaca i varalica za sve uslove i vrste riba.</p>
            </a>
            <a href="proizvodi.php?category=3" class="category" id="textdec">
                <img src="slike/fishing gear.jpg" alt="Ribolovačka oprema">
                <h4>Oprema i pribor</h4>
                <p>Pribor za svaki ribolovački poduhvat, od mreža do kutija za opremu.</p>
            </a>
        </div>
    </section>

    
    <a href="../index.php" class="floating-button">Nazad na portfolio</a>

    
    <footer>
        <p>&copy; 2024 E-shop Ribar | Sva prava zadržana NS</p>
        <p>
            <a href="mailto:nikolastevanovic449@gmail.com">Mail</a> |
            <a href="https://github.com/Dzoni449/project-one">GitHub</a>
        </p>
    </footer>

</body>

</html>
