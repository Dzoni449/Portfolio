<?php     
session_start(); 
require_once 'database\conn.php';


$category_query = "SELECT * FROM Categories";
$categories_result = $con->query($category_query);

$selected_category = isset($_GET['category']) ? $_GET['category'] : null;
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$sort_price = isset($_GET['sort_price']) ? $_GET['sort_price'] : '';


$product_query = "SELECT * FROM Products WHERE 1=1";
if ($selected_category) {
    $product_query .= " AND category_id = " . intval($selected_category);
}
if ($search_query) {
    $product_query .= " AND name LIKE '%" . $con->real_escape_string($search_query) . "%'";
}
if ($sort_price == 'asc') {
    $product_query .= " ORDER BY price ASC";
} elseif ($sort_price == 'desc') {
    $product_query .= " ORDER BY price DESC";
}

$products_result = $con->query($product_query);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    
    $query = "SELECT * FROM Products WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

       
        $_SESSION['cart'][$product_id] = array(
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] + 1 : 1,
            'image' => $product['image']
        );
    }

    
    header("Location: korpa.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proizvodi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="stilovi/proizvodi.css">
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
    <aside class="sidebar">
        <h3>Kategorije</h3>
        <ul>
            <li><a href="proizvodi.php">Sve Kategorije</a></li>
            <?php while($category = $categories_result->fetch_assoc()): ?>
                <li><a href="proizvodi.php?category=<?php echo $category['id']; ?>"><?php echo $category['category_name']; ?></a></li>
            <?php endwhile; ?>
        </ul>
    </aside>

    <div class="content">
        <h3>Izlistani Proizvodi</h3>

        <div class="search-bar">
            <form action="proizvodi.php" method="GET">
                <input type="text" name="search" placeholder="Pretraži proizvode..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" id="raz">Pretraži</button>
            </form>
        </div>

        <div class="filter-bar">
            <form action="proizvodi.php" method="GET">
                <select name="sort_price" onchange="this.form.submit()">
                    <option value="">Sortiraj po ceni</option>
                    <option value="asc" <?php echo ($sort_price == 'asc') ? 'selected' : ''; ?>>Rastuće</option>
                    <option value="desc" <?php echo ($sort_price == 'desc') ? 'selected' : ''; ?>>Opadajuće</option>
                </select>
                <input type="hidden" name="category" value="<?php echo $selected_category; ?>">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
            </form>
        </div>

        <div class="products">
            <?php if ($products_result && $products_result->num_rows > 0): ?>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <div class="product">
                        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p>Cena: <?php echo htmlspecialchars($product['price']); ?> RSD</p>

                        
                        <form action="proizvodi.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="add-to-cart-button">Dodaj u korpu</button>
                        </form>

                        <button class="details-button" onclick="openPopup('<?php echo htmlspecialchars($product['name']); ?>', '<?php echo htmlspecialchars($product['description']); ?>', '<?php echo htmlspecialchars($product['price']); ?>', '<?php echo htmlspecialchars($product['image']); ?>')">Detaljnije</button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nema proizvoda koji odgovaraju vašem pretraživanju.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024 E-shop Ribar. Created by: NS </p>
</footer>

<div class="popup" id="popup"> 
    <div class="popup-content">
        <button class="popup-close" onclick="closePopup()">Zatvori</button>
        <img id="popup-product-image" src="" alt="Product Image" style="max-width: 80%; height: auto; margin-bottom: 15px; border-radius: 10px; display: block; margin-left: auto; margin-right: auto;">
        <h4 id="popup-product-name"></h4>
        <p id="popup-product-description"></p>
        <p id="popup-product-price"></p>
    </div>
</div>

<script>
    function openPopup(name, description, price, image) {
        document.getElementById('popup-product-name').innerText = name;
        document.getElementById('popup-product-description').innerText = description;
        document.getElementById('popup-product-price').innerText = 'Cena: ' + price + ' RSD';
        document.getElementById('popup-product-image').src = 'uploads/' + image;
        document.getElementById('popup').style.display = 'flex';
    }

    function closePopup() {
        document.getElementById('popup').style.display = 'none';
    }

    
    window.onclick = function(event) {
        var popup = document.getElementById('popup');
        if (event.target == popup) {
            closePopup();
        }
    }
</script>
</body>
</html>
