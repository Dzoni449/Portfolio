<?php 
session_start();
require_once 'database/conn.php';


$title = 'No Title Available';
$content = 'No Content Available';
$image_path = null;
$published_date = 'No Date Available';
$author_name = 'Nepoznat autor';

try {
    
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

      
        $sql = "SELECT a.*, au.first_name 
                FROM articles a 
                LEFT JOIN authors au ON a.author_id = au.id 
                WHERE a.id = ? AND a.status = 'published'";
        $stmt = mysqli_prepare($con, $sql);

        if (!$stmt) {
            throw new Exception("SQL Error: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        
        if ($row = mysqli_fetch_assoc($result)) {
            $title = htmlspecialchars($row['title'] ?? 'No Title Available');
            $content = htmlspecialchars($row['content'] ?? 'No Content Available');
            $image_path = htmlspecialchars($row['image_path'] ?? '');
            $published_date = htmlspecialchars($row['published_date'] ?? 'No Date Available');
            $author_name = htmlspecialchars($row['first_name'] ?? 'Nepoznat autor');
        } else {
            die("Article not found."); 
        }

    
        mysqli_stmt_close($stmt);

        
        $tagQuery = "
            SELECT t.tag_name 
            FROM ArticleTags at 
            JOIN Tags t ON at.tag_id = t.id 
            WHERE at.article_id = ?";
        $tagStmt = mysqli_prepare($con, $tagQuery);

        if (!$tagStmt) {
            throw new Exception("SQL Error: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($tagStmt, 'i', $id);
        mysqli_stmt_execute($tagStmt);
        $tagResult = mysqli_stmt_get_result($tagStmt);

        $tagNames = [];
        while ($tag = mysqli_fetch_assoc($tagResult)) {
            $tagNames[] = $tag['tag_name'];
        }

        mysqli_stmt_close($tagStmt);

        
        $commentCountQuery = "
        SELECT COUNT(*) AS comment_count 
        FROM Comments 
        WHERE article_id = ?";
        $commentCountStmt = mysqli_prepare($con, $commentCountQuery);

        if (!$commentCountStmt) {
            throw new Exception("SQL Error: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($commentCountStmt, 'i', $id);
        mysqli_stmt_execute($commentCountStmt);
        $commentCountResult = mysqli_stmt_get_result($commentCountStmt);
        $commentCountRow = mysqli_fetch_assoc($commentCountResult);
        $commentCount = $commentCountRow['comment_count'];

        mysqli_stmt_close($commentCountStmt);
    } else {
        die("No article ID provided."); 
    }
} catch (Exception $e) {
    die($e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stil/index.css">
    <style>
        #col {          
            color: #e74c3c; 
        }
        .article-container {
            max-width: 800px; 
            margin: 40px auto; 
            padding: 20px; 
            background-color: #ffffff; 
            border-radius: 8px; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
            text-align: center; 
        }
        .article-container h1 {
            color: #333; 
            margin-bottom: 20px; 
        }
        .article-container img {
            max-width: 100%; 
            height: auto; 
            border-radius: 8px; 
            margin-bottom: 20px; 
        }
        .article-container p {
            line-height: 1.6; 
            color: #555; 
            margin-bottom: 15px; 
        }
        .article-container small {
            color: #777; 
            display: block; 
            margin-top: 10px; 
        }
        #col {
            color: #e74c3c;
        }
        .comment-section {
            margin-top: 20px;
            padding: 20px;
            border-top: 1px solid #ddd;
        }
        .comment-form textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
        }
        .comment-form button {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .comments {
            margin-top: 20px;
        }
        .comment-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .comment-item small {
            color: #999;
        }
        .hidden {
            display: none;
        }
        .show-more-btn {
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }
        .comments-header {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .comments-header i {
            font-size: 24px;
            color: #007bff;
        }
        #marg {
            margin-top: 10px;
        }
        .hidden-comments {
            display: none;
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
                <li><a href="politika.php">Politika</a></li>
                <li><a href="sport.php">Sport</a></li>
                <li><a href="tehnologija.php">Tehnologija</a></li>
                <li><a href="kontakt.php">Kontakt</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php" id="col">Odjavi se</a></li>
                <?php else: ?>
                    <li><a href="login.php" id="col">Prijava</a></li>
                    <li><a href="register.php" id="col">Registracija</a></li>
                <?php endif; ?>
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

<div class="article-container"> 
    <h1><?= $title; ?></h1>
    <?php if (!empty($image_path)): ?>
        <img src="<?= $image_path; ?>" alt="<?= $title; ?>">
    <?php endif; ?>
    <p><?= $content; ?></p>
    <p><small>Objavljeno: <?= $published_date; ?></small></p>
    <p><small>Autor: <?= $author_name; ?></small></p>

    <?php if (!empty($tagNames)): ?>
        <p><strong>Tagovi:</strong> <?= implode(', ', $tagNames); ?></p>
    <?php endif; ?>

    <div class="comment-section">
        <div class="comments-header" style="cursor:pointer;">
            <i class="fas fa-comments"></i>
            <h4>Komentari (<?= $commentCount; ?>)</h4>
        </div>

        <div class="comments hidden-comments">
            <?php
            $commentQuery = "
            SELECT c.*, u.username 
            FROM Comments c 
            JOIN Users u ON c.user_id = u.id 
            WHERE c.article_id = ? 
            ORDER BY c.comment_date DESC";

            $commentStmt = mysqli_prepare($con, $commentQuery);
            if ($commentStmt) {
                mysqli_stmt_bind_param($commentStmt, 'i', $id);
                mysqli_stmt_execute($commentStmt);
                $commentResult = mysqli_stmt_get_result($commentStmt);

                while ($comment = mysqli_fetch_assoc($commentResult)): ?>
                    <div class="comment-item">
                        <p><?= htmlspecialchars($comment['comment']); ?></p>
                        <small>Autor: <?= htmlspecialchars($comment['username']); ?> | Datum: <?= date('d M Y H:i', strtotime($comment['comment_date'])); ?></small>
                    </div>
                <?php endwhile;

                mysqli_stmt_close($commentStmt);
            }
            ?>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="comment-form" id="marg">
                <form action="add_comment.php" method="POST">
                    <textarea name="comment" placeholder="Unesite vaš komentar..." required></textarea>
                    <input type="hidden" name="article_id" value="<?= $id; ?>">
                    <button type="submit">Dodaj komentar</button>
                </form>
            </div>
        <?php else: ?>
            <p>Morate biti prijavljeni da biste ostavili komentar. <a href="login.php">Prijavite se ovde</a>.</p>
        <?php endif; ?>
    </div>
</div>

<footer id="arg">
    <div class="container">
        <p>&copy; 2024 Vesti. Sva prava zadržana NS.</p>
    </div>
</footer>
<?php

if ($con) {
    mysqli_close($con);
}
?>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuIcon = document.getElementById('mobile-menu-icon');
        const navLinks = document.querySelector('.nav-links');

        mobileMenuIcon.addEventListener('click', function() {
            navLinks.classList.toggle('show');
        });

        const commentHeaders = document.querySelectorAll('.comments-header');
        commentHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const commentsSection = this.nextElementSibling;
                commentsSection.classList.toggle('hidden-comments');
            });
        });
    });
</script>
</body>
</html>
