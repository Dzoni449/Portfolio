<?php
require 'connection.php';

// Fetch the latest live chat for "user"
$stmt = $pdo->prepare("SELECT poruka, reply FROM livechat WHERE name = 'user' ORDER BY id ASC");
$stmt->execute();
$messages = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $messages[] = [
    'message' => $row['poruka'],
    'reply'   => $row['reply']
  ];
}

header('Content-Type: application/json');
echo json_encode($messages);
