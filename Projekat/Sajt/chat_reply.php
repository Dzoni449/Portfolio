<?php
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chat_id'], $_POST['reply'])) {
    $id = (int)$_POST['chat_id'];
    $reply = trim($_POST['reply']);

    if ($reply !== '') {
        $stmt = $pdo->prepare("UPDATE livechat SET reply = ? WHERE id = ?");
        $stmt->execute([$reply, $id]);
    }
}

header('Location: admindashboard.php');
exit;
