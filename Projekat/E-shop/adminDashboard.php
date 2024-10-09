<?php    
session_start();
require_once 'database/conn.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}  

if (isset($_GET['delete_category'])) {
    $category_id = $_GET['delete_category'];
    $sql = "DELETE FROM categories WHERE id = ?";
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_GET['delete_product'])) {
    $product_id = $_GET['delete_product'];
    $sql = "DELETE FROM products WHERE id = ?";
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['add_product'])) {
    $product_name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $product_image = $_FILES['image'];

    
    if ($product_image['error'] == UPLOAD_ERR_OK) {
        $upload_directory = "uploads/";
        $image_name = uniqid() . "_" . basename($product_image['name']); 
        $image_path = $upload_directory . $image_name;

        
        if (move_uploaded_file($product_image['tmp_name'], $image_path)) {
            
            $insert_product_query = "INSERT INTO products (name, category_id, price, image, description) 
                                      VALUES ('$product_name', '$category_id', '$price', '$image_name', '$description')";

            
            $insert_product_result = mysqli_query($con, $insert_product_query);

            if ($insert_product_result) {
                $_SESSION['success_message'] = "Proizvod je uspešno dodat.";
                header("Location: adminDashboard.php");
                exit;
            } else {
                $_SESSION['error_message'] = "Došlo je do greške prilikom dodavanja proizvoda: " . mysqli_error($con);
            }
        } else {
            $_SESSION['error_message'] = "Došlo je do greške prilikom čuvanja slike.";
        }
    } else {
        $_SESSION['error_message'] = "Greška prilikom učitavanja slike.";
    }
}




if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $description = $_POST['description'];
    $sql = "INSERT INTO categories (category_name, description) VALUES (?, ?)";
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("ss", $category_name, $description);
        $stmt->execute();
        $stmt->close();
    }
}

$categories_result = $con->query("SELECT * FROM categories");
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

$products = $con->query("SELECT * FROM products");
$orders = $con->query("SELECT * FROM orders");

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            padding: 20px;
        }
        .dashboard-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .section {
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .action-btn {
            padding: 5px 10px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .action-btn:hover {
            background-color: #c0392b;
        }
        .logout-btn {
            display: block;
            text-align: center;
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        .logout-btn:hover {
            background-color: #1a252f;
        }
        .form-input {
            margin-bottom: 10px;
        }
        input[type="text"], input[type="number"] {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        #raz{
            margin-top:3px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <h2>Admin Dashboard</h2>

    <div class="section">
        <h3>Manage Categories</h3>
        <form method="POST">
            <div class="form-input">
                <input type="text" name="category_name" placeholder="Category Name" required>
            </div>
            <div class="form-input">
                <input type="text" name="description" placeholder="Description">
            </div>
            <button type="submit" name="add_category" class="action-btn">Add Category</button>
        </form>
        <table>
            <tr>
                <th>Category Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= htmlspecialchars($category['category_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($category['description'] ?? '') ?></td>
                    <td><a href="adminDashboard.php?delete_category=<?= $category['id'] ?>" class="action-btn">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    
    <div class="section">
        <h3>Manage Products</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-input">
                <input type="text" name="name" placeholder="Product Name" required>
            </div>
            <div class="form-input">
                <input type="number" name="price" placeholder="Price" step="0.01" required>
            </div>
            <div class="form-input">
                <input type="text" name="description" placeholder="Description">
            </div>
            <div class="form-input">
                <select name="category_id" required>
                    <option value="" disabled selected>Choose Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-input">
                <input type="file" name="image" accept="image/*" required>
            </div>
            <button type="submit" name="add_product" class="action-btn">Add Product</button>
        </form>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Image</th> 
                <th>Category ID</th>
                <th>Action</th>
            </tr>
            <?php while ($product = $products->fetch_assoc()): ?> 
            <tr>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['price']) ?></td>
                <td>
                    <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width: 100px; height: auto;">
                </td>
                <td><?= htmlspecialchars($product['category_id']) ?></td>
                <td><a href="adminDashboard.php?delete_product=<?= $product['id'] ?>" class="action-btn">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="section">
        <h3>Manage Orders</h3>
        <table>
            <tr>
                <th>Order ID</th> 
                <th>Username</th>
                <th>User Surname</th>
                <th>Address</th>
                <th>Email</th>
                <th>Product ID</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td> 
                    <td><?= htmlspecialchars($order['user_name']) ?></td>
                    <td><?= htmlspecialchars($order['user_surname']) ?></td>
                    <td><?= htmlspecialchars($order['adresa']) ?></td> 
                    <td><?= htmlspecialchars($order['email']) ?></td>
                    <td><?= htmlspecialchars($order['product_id']) ?></td>
                    <td><?= htmlspecialchars($order['quantity']) ?></td>
                    <td>
                        <form action="adminDashboard.php" method="POST">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>"> 
                            <select name="status">
                                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <input type="submit" name="update_order" class="action-btn" value="Update" id="raz">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

</body>
</html>
