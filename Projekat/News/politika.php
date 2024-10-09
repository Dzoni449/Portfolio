<?php  
session_start();
require_once 'database/conn.php';

$query = "
SELECT a.*, au.first_name AS author_name 
FROM Articles a 
JOIN Authors au ON a.author_id = au.id 
WHERE a.category_id = 1 AND a.status = 'published' 
ORDER BY a.published_date DESC";

$stmt = $con->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politika</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stil/politika.css"> 
    <style>
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



<main>
<section id="featured-news">
    <div class="container">
        <div class="news-grid">
            <div class="headline">
                <h2>Najnovije vesti iz Politike</h2>
                <div class="headline">                
                <?php if (isset($_SESSION['user_id'])): 
                    
                    $user_id = $_SESSION['user_id'];
                    $category_id = 1; 
                    $subscriptionCheckQuery = "SELECT * FROM Subscriptions WHERE user_id = ? AND category_id = ?";
                    $subscriptionCheckStmt = $con->prepare($subscriptionCheckQuery);
                    $subscriptionCheckStmt->bind_param("ii", $user_id, $category_id);
                    $subscriptionCheckStmt->execute();
                    $isSubscribed = $subscriptionCheckStmt->get_result()->num_rows > 0;
                ?>
                    <div class="subscribe-section">
                    <?php if ($isSubscribed): ?>
                        <button class="unsubscribe-btn" data-category-id="<?= $category_id; ?>">Otkažite pretplatu</button>
                        <p class="subscribe-description">Vi ste pretplaćeni na ovu kategoriju i dobijate obaveštenja o novim člancima iz Politike.</p>
                    <?php else: ?>
                        <button class="subscribe-btn" data-category-id="<?= $category_id; ?>">Pretplatite se</button>
                        <p class="subscribe-description">Pretplatom na ovu kategoriju, bićete obavešteni svaki put kada se objavi novi članak iz Politike.</p>
                    <?php endif; ?>
                </div>

                <?php else: ?>
                    <p>Morate biti prijavljeni da biste se pretplatili. <a href="login.php">Prijavite se ovde</a>.</p>
                <?php endif; ?>
            </div>

            </div>
            <?php while ($article = $result->fetch_assoc()): ?>
                <div class="news-item">
                    <?php if (!empty($article['image_path'])): ?>
                        <img src="<?= $article['image_path']; ?>" alt="<?= $article['title']; ?>">
                    <?php endif; ?>
                    <div class="news-content">
                    <h3><?= $article['title']; ?></h3>
                    <p><?= mb_strimwidth($article['content'], 0, 150, "..."); ?></p>
                    <p><small>Objavljeno: <?= date('d M Y', strtotime($article['published_date'])); ?></small></p>
                    <p><small>Autor: <?= $article['author_name']; ?></small></p>                        
                        <?php
                        $article_id = $article['id']; 
                        $tagQuery = "
                            SELECT t.tag_name 
                            FROM ArticleTags at 
                            JOIN Tags t ON at.tag_id = t.id 
                            WHERE at.article_id = ?";
                        $tagStmt = $con->prepare($tagQuery);
                        $tagStmt->bind_param("i", $article_id);
                        $tagStmt->execute();
                        $tagResult = $tagStmt->get_result();

                        if ($tagResult->num_rows > 0): ?>
                            <p><strong>Tagovi:</strong> 
                            <?php $tagNames = []; 
                            while ($tag = $tagResult->fetch_assoc()): 
                                $tagNames[] = $tag['tag_name']; 
                            endwhile; 
                            echo implode(', ', $tagNames); 
                            ?>
                            </p>
                        <?php endif; ?>

                        <div class="comment-section">
                            <div class="comments-header" style="cursor:pointer;">
                                <i class="fas fa-comments"></i>
                                <?php
                                
                                $commentCountQuery = "
                                SELECT COUNT(*) AS comment_count 
                                FROM Comments 
                                WHERE article_id = ?";
                                $commentCountStmt = $con->prepare($commentCountQuery);
                                $commentCountStmt->bind_param("i", $article['id']);
                                $commentCountStmt->execute();
                                $commentCountResult = $commentCountStmt->get_result();
                                $commentCountRow = $commentCountResult->fetch_assoc();
                                $commentCount = $commentCountRow['comment_count'];
                                ?>
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

                                $commentStmt = $con->prepare($commentQuery);
                                $commentStmt->bind_param("i", $article['id']);
                                $commentStmt->execute();
                                $commentResult = $commentStmt->get_result();

                                while ($comment = $commentResult->fetch_assoc()): ?>
                                    <div class="comment-item">
                                        <p><?= $comment['comment']; ?></p>
                                        <small>Autor: <?= $comment['username']; ?> | Datum: <?= date('d M Y H:i', strtotime($comment['comment_date'])); ?></small>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="comment-form" id="marg">
                                    <form action="add_comment.php" method="POST">
                                        <textarea name="comment" placeholder="Unesite vaš komentar..." required></textarea>
                                        <input type="hidden" name="article_id" value="<?= $article['id']; ?>">
                                        <button type="submit">Dodaj komentar</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <p>Morate biti prijavljeni da biste ostavili komentar. <a href="login.php">Prijavite se ovde</a>.</p>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
</main>

<footer>
    <p>&copy; 2024 Sport - Sva prava zadržana</p>
</footer>

<script> 
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuIcon = document.getElementById('mobile-menu-icon');
        const navLinks = document.querySelector('.nav-links');

        mobileMenuIcon.addEventListener('click', function() {
            navLinks.classList.toggle('show');
        });

        const tickerItems = document.querySelectorAll('.ticker-item');
        let tickerIndex = 0;

        setInterval(() => {
            tickerItems[tickerIndex].style.display = 'none';
            tickerIndex = (tickerIndex + 1) % tickerItems.length;
            tickerItems[tickerIndex].style.display = 'inline-block';
        }, 5000);

        const commentHeaders = document.querySelectorAll('.comments-header');
        commentHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const commentsSection = this.nextElementSibling;
                commentsSection.classList.toggle('hidden-comments');
            });
        });
    });

    document.querySelectorAll('.subscribe-btn').forEach(button => {
        button.addEventListener('click', function() {
            let categoryId = this.getAttribute('data-category-id');

            fetch('subscribe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `category_id=${categoryId}&action=subscribe` 
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    alert('Uspešno ste se pretplatili na ovu kategoriju!');
                    location.reload(); 
                } else if (data === 'already_subscribed') {
                    alert('Već ste pretplaćeni na ovu kategoriju.');
                } else {
                    alert('Došlo je do greške. Pokušajte ponovo.');
                }
            });
        });
    });

    document.querySelectorAll('.unsubscribe-btn').forEach(button => {
        button.addEventListener('click', function() {
            let categoryId = this.getAttribute('data-category-id');

            fetch('subscribe.php', {  
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `category_id=${categoryId}&action=unsubscribe` 
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    alert('Uspešno ste otkazali pretplatu na ovu kategoriju!');
                    location.reload(); 
                } else {
                    alert('Došlo je do greške. Pokušajte ponovo.');
                }
            });
        });
    });
</script>

</body>
</html>
