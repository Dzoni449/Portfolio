<?php 

require_once "conn.php";


$sql = "CREATE TABLE IF NOT EXISTS Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);"; 

$sql .= "CREATE TABLE IF NOT EXISTS Categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT
);"; 

$sql .= "CREATE TABLE IF NOT EXISTS Products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES Categories(id) ON DELETE CASCADE
);"; 

$sql .= "CREATE TABLE IF NOT EXISTS Orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL,
    user_surname VARCHAR(255) NOT NULL,    
    adresa VARCHAR(30) NOT NULL, 
    email VARCHAR(30) NOT NULL, 
    product_id INT, 
    quantity INT NOT NULL, 
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',  
    FOREIGN KEY (product_id) REFERENCES Products(id) ON DELETE CASCADE
);"; 

if ($con->multi_query($sql)) {
    do {
        if ($result = $con->store_result()) {
            $result->free();
        }
        if ($con->more_results()) {
            echo "Uspešno izvršeni upiti<br>";
        }
    } while ($con->next_result());
} else {
    echo "Greška prilikom izvršenja upita: " . $con->error;
}

?>
