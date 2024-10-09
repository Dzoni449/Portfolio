<!DOCTYPE html>  
<html lang="sr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt | E-shop Ribolovačka Oprema</title>
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

        .contact-section {
            padding: 50px 20px;
            background-color: #ecf0f1;
            text-align: center;
        }

        .contact-section h2 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .contact-section p {
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .contact-grid {
            display: flex;
            justify-content: space-around;
            max-width: 1200px;
            margin: 0 auto;
            flex-wrap: wrap;
        }

        .contact-info, .contact-form {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 20px;
            flex: 1;
            min-width: 300px;
            max-width: 500px;
        }

        .contact-info h3, .contact-form h3 {
            font-size: 1.8rem;
            color: #e67e22;
            margin-bottom: 20px;
        }

        .contact-info p {
            font-size: 1rem;
            color: #7f8c8d;
            margin: 10px 0;
        }

        .contact-info i {
            margin-right: 10px;
            color: #e67e22;
        }

        .contact-form input, .contact-form textarea {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
        }

        .contact-form button {
            padding: 15px 30px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .contact-form button:hover {
            background-color: #c0392b;
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

    
    <section class="contact-section">
        <h2>Kontaktirajte nas</h2>
        <p>Ako imate bilo kakva pitanja ili vam je potrebna pomoć, slobodno nas kontaktirajte putem formulara ili naših kontakt podataka ispod. Uvek smo tu da pomognemo.</p>

        <div class="contact-grid">
            
            <div class="contact-info">
                <h3>Kontakt podaci</h3>
                <p><i class="fas fa-phone"></i> Telefon: +381 60 123 4567</p>
                <p><i class="fas fa-envelope"></i> Email: <a href="mailto:nikolastevanovic449@gmail.com">ribar@gmail.com</a></p>
                <p><i class="fas fa-map-marker-alt"></i> Adresa: Ulica Ribara 23, Nis, Srbija</p>
                <p><i class="fas fa-clock"></i> Radno vreme: Pon-Pet 09:00 - 18:00</p>
            </div>

            
            <div class="contact-form">
                <h3>Pošaljite poruku</h3>
                <form action="submit_form.php" method="post">
                    <input type="text" name="name" placeholder="Vaše ime" required>
                    <input type="email" name="email" placeholder="Vaš email" required>
                    <textarea name="message" rows="5" placeholder="Vaša poruka" required></textarea>
                    <button type="submit">Pošalji poruku</button>
                </form>
            </div>
        </div>
    </section>

    
    <footer>
        <p>&copy; 2024 E-shop Ribar | Sva prava zadržana</p>
        <p>
            <a href="mailto:ribar@gmail.com">Kontakt</a> |
            <a href="https://github.com/Dzoni449/project-one">GitHub</a>
        </p>
    </footer>

</body>

</html>
