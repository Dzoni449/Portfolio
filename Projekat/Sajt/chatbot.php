<?php
// chatbot.php
require 'connection.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $stmt = $pdo->prepare("INSERT INTO livechat (poruka) VALUES (?)");
    $stmt->execute([$_POST['message']]);
}
?>