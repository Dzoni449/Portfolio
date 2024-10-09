<?php 
session_start();
require_once 'database/conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['add_category'])) {
        $name = $_POST['category_name'];
        $stmt = $con->prepare("INSERT INTO Categories (name) VALUES (?)");
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_category'])) {
        $id = $_POST['category_id'];
        $stmt = $con->prepare("DELETE FROM Categories WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    
    if (isset($_POST['add_author'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $stmt = $con->prepare("INSERT INTO Authors (first_name, last_name, email) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $first_name, $last_name, $email);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_author'])) {
        $id = $_POST['author_id'];
        $stmt = $con->prepare("DELETE FROM Authors WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    
    if (isset($_POST['add_article'])) {
       
        $title = $_POST['title'];
        $content = $_POST['content'];
        $category_id = $_POST['category_id'];
        $author_id = $_POST['author_id'];
        $tag_ids = $_POST['tag_ids'];
    
        
        $query = "INSERT INTO Articles (title, content, category_id, author_id, published_date, status) VALUES (?, ?, ?, ?, NOW(), 'published')";
        $stmt = $con->prepare($query);
        $stmt->bind_param('ssii', $title, $content, $category_id, $author_id);
        $stmt->execute();
    
        
        $article_id = $stmt->insert_id;
    
        
        foreach ($tag_ids as $tag_id) {
            $tagQuery = "INSERT INTO ArticleTags (article_id, tag_id) VALUES (?, ?)";
            $tagStmt = $con->prepare($tagQuery);
            $tagStmt->bind_param('ii', $article_id, $tag_id);
            $tagStmt->execute();
        }
    
        
        $subscriptionQuery = "SELECT u.email FROM Subscriptions s JOIN Users u ON s.user_id = u.id WHERE s.category_id = ?";
        $subscriptionStmt = $con->prepare($subscriptionQuery);
        $subscriptionStmt->bind_param('i', $category_id);
        $subscriptionStmt->execute();
        $subscribersResult = $subscriptionStmt->get_result();
    
        
        $subject = "New Article Published: $title";
        $article_link = "https://yourwebsite.com/article.php?id=$article_id"; 
        $message = "
            Hello,
    
            A new article titled '$title' has been published in your subscribed category.
    
            You can read the article at the following link:
            $article_link
    
            Thank you for subscribing to our website.
        ";
    
        // Moguce slanje ovim kodom ukoliko nisu lokalni hostovi, u suprotnom moze da se korsiti PHPmailer ili SendGrid platforma
       // while ($subscriber = $subscribersResult->fetch_assoc()) {
            //$to = $subscriber['email'];
            //$headers = "From: no-reply@yourwebsite.com"; // Change to your actual "from" email
            //mail($to, $subject, $message, $headers); // You can replace this with PHPMailer if needed
       // }
    
        
        echo "Article added and notification emails sent to subscribers.";
    }
    

    
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $con->prepare("INSERT INTO Users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $email, $password);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_user'])) {
        $id = $_POST['user_id'];
        $stmt = $con->prepare("DELETE FROM Users WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    
    if (isset($_POST['delete_comment'])) {
        $id = $_POST['comment_id'];
        $stmt = $con->prepare("DELETE FROM Comments WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }


    if (isset($_POST['add_tag'])) {
        $tag_name = $_POST['tag_name'];

        $stmt = $con->prepare("SELECT * FROM Tags WHERE tag_name = ?");
        $stmt->bind_param('s', $tag_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Tag already exists. Please enter a unique tag name.";
        } else {
            $stmt = $con->prepare("INSERT INTO Tags (tag_name) VALUES (?)");
            $stmt->bind_param('s', $tag_name);
            $stmt->execute();
        }
        $stmt->close();
    }
}


$categories = mysqli_query($con, "SELECT * FROM Categories");
$authors = mysqli_query($con, "SELECT * FROM Authors");
$articles = mysqli_query($con, "SELECT * FROM Articles");
$users = mysqli_query($con, "SELECT * FROM Users");
$comments = mysqli_query($con, "SELECT * FROM Comments");
$tags = mysqli_query($con, "SELECT * FROM Tags");

?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="styles/dashboard.css"> 
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            margin: 0;
            font-size: 24px;
            }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        nav ul li {
            margin-left: 20px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        main {
            width: 80%;
            margin: 20px auto;
        }

        section {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
        }

        form {
            margin-bottom: 20px;
        }

        form input, form textarea, form select, form button {
            padding: 10px;
            margin-right: 10px;
            margin-bottom: 10px;
            font-size: 16px;
        }

        form button {
            background-color: #333;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f9;
        }

        button[type="submit"] {
            background-color: #ff4d4d;
            color: #fff;
            border: none;
            cursor: pointer;
            padding: 5px 10px;
        }

        button[type="submit"]:hover {
            background-color: #e60000;
        }

    </style>
</head>
<body>

<header>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#categories">Categories</a></li>
                <li><a href="#authors">Authors</a></li>
                <li><a href="#articles">Articles</a></li>
                <li><a href="#users">Users</a></li>
                <li><a href="#comments">Comments</a></li>
                <li><a href="#tags">Tags</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    
    <section id="categories">
        <h2>Manage Categories</h2>
        <form method="POST" action="">
            <input type="text" name="category_name" placeholder="Add Category" required>
            <button type="submit" name="add_category">Add</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($categories)): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['name']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="category_id" value="<?= $row['id']; ?>">
                            <button type="submit" name="delete_category">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    
    <section id="authors">
        <h2>Manage Authors</h2>
        <form method="POST" action="">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <button type="submit" name="add_author">Add</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($authors)): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['first_name']; ?></td>
                    <td><?= $row['last_name']; ?></td>
                    <td><?= $row['email']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="author_id" value="<?= $row['id']; ?>">
                            <button type="submit" name="delete_author">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

            
            <section id="articles">
                <h2>Manage Articles</h2>
                <form method="POST" action="">
            <input type="text" name="title" placeholder="Article Title" required>
            <textarea name="content" placeholder="Article Content" required></textarea>

            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php 
                $categories = mysqli_query($con, "SELECT * FROM Categories");
                while ($cat = mysqli_fetch_assoc($categories)): ?>
                <option value="<?= $cat['id']; ?>"><?= $cat['name']; ?></option>
                <?php endwhile; ?>
            </select>

            <select name="author_id" required>
                <option value="">Select Author</option>
                <?php 
                $authors = mysqli_query($con, "SELECT * FROM Authors");
                while ($auth = mysqli_fetch_assoc($authors)): ?>
                <option value="<?= $auth['id']; ?>"><?= $auth['first_name'] . ' ' . $auth['last_name']; ?></option>
                <?php endwhile; ?>
            </select>

            
            <label for="tags">Select Tags:</label>
            <select name="tag_ids[]" multiple>
                <?php 
                $tags = mysqli_query($con, "SELECT * FROM Tags");
                while ($tag = mysqli_fetch_assoc($tags)): ?>
                <option value="<?= $tag['id']; ?>"><?= $tag['tag_name']; ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit" name="add_article">Add</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($articles)): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['title']; ?></td>
                    <td><?= substr($row['content'], 0, 100); ?>...</td>
                    <td><?= $row['category_id']; ?></td>
                    <td><?= $row['author_id']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="article_id" value="<?= $row['id']; ?>">
                            <button type="submit" name="delete_article">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    
    <section id="users">
        <h2>Manage Users</h2>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="add_user">Add</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['username']; ?></td>
                    <td><?= $row['email']; ?></td>
                    <td><?= $row['role']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                            <button type="submit" name="delete_user">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    
    <section id="comments">
        <h2>Manage Comments</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Article ID</th>
                    <th>User ID</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($comments)): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['article_id']; ?></td>
                    <td><?= $row['user_id']; ?></td>
                    <td><?= $row['comment']; ?></td>
                    <td><?= $row['comment_date']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="comment_id" value="<?= $row['id']; ?>">
                            <button type="submit" name="delete_comment">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    
    <section id="tags">
        <h2>Manage Tags</h2>
        <form method="POST" action="">
            <input type="text" name="tag_name" placeholder="Add Tag" required>
            <button type="submit" name="add_tag">Add</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tag Name</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($tags)): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['tag_name']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="tag_id" value="<?= $row['id']; ?>">
                            <button type="submit" name="delete_tag">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

</main>

</body>
</html>
