<?php    
session_start();

require_once 'database\conn.php';

$total_price = 0;


if (isset($_POST['remove_id'])) {
    $remove_id = intval($_POST['remove_id']);
    unset($_SESSION['cart'][$remove_id]); 
    header("Location: korpa.php"); 
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $email = $_POST['email'];

    
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $quantity = $item['quantity'];
        
        $sql = "INSERT INTO Orders (user_name, user_surname, adresa, email, product_id, quantity) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssii", $first_name, $last_name, $address, $email, $product_id, $quantity);
        
        if (!$stmt->execute()) {
            echo "Greška: " . $stmt->error;
        }
    }

    $stmt->close();
    $con->close();

    
    unset($_SESSION['cart']);

    echo "<p>Porudžbina je uspešno poslata!</p>";
    echo "<p><a href='proizvodi.php'>Nastavite kupovinu</a></p>";
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Korpa</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stilovi/korpa.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

<div class="container">
    <h3>Vaša Korpa</h3>

    <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <h4>Vaša korpa je prazna.</h4>
            <p>Dodajte proizvode u svoju korpu da biste ih poručili.</p>
            <form action="proizvodi.php" method="GET">
                <button type="submit" class="continue-shopping-button">Nastavite kupovinu</button>
            </form>
        </div>
    <?php else: ?>
        <table>
            <tr>
                <th>Naziv</th>
                <th>Cena</th>
                <th>Količina</th>
                <th>Ukupno</th>
                <th>Akcija</th> 
            </tr>
            <?php foreach ($_SESSION['cart'] as $product_id => $item): 
                $total_price += $item['price'] * $item['quantity'];
            ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo htmlspecialchars($item['price']); ?> RSD</td>
                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                <td><?php echo htmlspecialchars($item['price'] * $item['quantity']); ?> RSD</td>
                <td>
                    
                    <form action="korpa.php" method="POST" style="display:inline;">
                        <input type="hidden" name="remove_id" value="<?php echo $product_id; ?>">
                        <button type="submit" class="remove-button" title="Ukloni iz korpe">
                            <i class="fas fa-trash"></i> 
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <h4>Ukupno: <?php echo $total_price; ?> RSD</h4>
        <form action="proizvodi.php" method="GET">
        <button type="submit" class="continue-shopping-button">Poruči još nešto</button>
    </form>

        
        <h4>Ukoliko je to sve, popunite sledeći formular:</h4>
        <form action="korpa.php" method="POST" class="order-form"> 
            <label for="first_name">Ime:</label>
            <input type="text" name="first_name" id="first_name" required>

            <label for="last_name">Prezime:</label>
            <input type="text" name="last_name" id="last_name" required>

            <label for="address">Adresa:</label>
            <input type="text" name="address" id="address" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <button type="submit" name="submit_order" class="submit-order-button">Pošalji porudžbinu</button>
        </form>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2024 E-shop Ribar. Created by: NS </p>
</footer>

</body>
</html>
