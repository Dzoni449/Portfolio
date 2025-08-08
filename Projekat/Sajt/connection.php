<?php
// connection.php – Povezivanje sa bazom i kreiranje baze i tabela ako ne postoje

$host = 'localhost';
$user = 'root'; // izmeni ako koristiš drugo korisničko ime
$pass = '';     // izmeni ako koristiš lozinku
$dbname = 'my_website';

try {
    // Prvo se konektujemo na MySQL bez odabrane baze
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Proveri da li postoji baza, ako ne postoji kreiraj je
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Poveži se sada na tu bazu
    $pdo->exec("USE `$dbname`");

    // Kreiraj tabelu za kontakt poruke ako ne postoji
    $pdo->exec("CREATE TABLE IF NOT EXISTS Kontakt_MSG (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ime VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        poruka TEXT NOT NULL,
        datum DATE NOT NULL,
        vreme TIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Kreiraj tabelu za live chat ako ne postoji
    $pdo->exec("CREATE TABLE IF NOT EXISTS livechat (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL DEFAULT 'User',
        poruka TEXT NOT NULL,
        reply TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role INT DEFAULT 0
    ) ENGINE=InnoDB");



        $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(255),
    client_name VARCHAR(255),
    client_address TEXT,
    description TEXT,
    header BOOLEAN DEFAULT 0,
    info_redirect BOOLEAN DEFAULT 0,
    contact BOOLEAN DEFAULT 0,
    add_page BOOLEAN DEFAULT 0,
    add_function BOOLEAN DEFAULT 0,
    add_picture BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) NOT NULL DEFAULT 'pending'
    );");


} catch (PDOException $e) {
    die("Greška pri povezivanju: " . $e->getMessage());
}
?>
