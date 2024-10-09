<?php 
session_start();
require_once 'database/conn.php';
$message = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $article_id = $_POST['article_id'];
    $user_id = $_SESSION['user_id'];
    $comment = $_POST['comment'];

    
    $query = "INSERT INTO Comments (article_id, user_id, comment) VALUES (?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("iis", $article_id, $user_id, $comment);

    if ($stmt->execute()) {
        header("Location: sport.php");
    } else {
        $message = "Greška prilikom dodavanja komentara.";
    }
} else {
    $message = "Nevažeći zahtev.";
}


echo $message;

?>
