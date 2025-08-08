<?php 
session_start();
require 'connection.php';

if (!isset($_SESSION['admin'])) {
  header('Location: admindashboard_login.php');
  exit;
}

if (isset($_GET['logout'])) {
  session_destroy();
  header('Location: admindashboard_login.php');
  exit;
}

// Dodavanje nove porudžbine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_number'])) {
  $stmt = $pdo->prepare("INSERT INTO orders (
    order_number, client_name, client_address, description,
    header, info_redirect, contact, add_page, add_function, add_picture, status
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  
  $stmt->execute([
    $_POST['order_number'],
    $_POST['client_name'],
    $_POST['client_address'],
    $_POST['description'],
    isset($_POST['header']) ? 1 : 0,
    isset($_POST['info_redirect']) ? 1 : 0,
    isset($_POST['contact']) ? 1 : 0,
    isset($_POST['add_page']) ? 1 : 0,
    isset($_POST['add_function']) ? 1 : 0,
    isset($_POST['add_picture']) ? 1 : 0,
    'pending' // početni status
  ]);
}

// Promena statusa porudžbine
if (isset($_POST['change_status'], $_POST['order_id'], $_POST['new_status'])) {
  $allowedStatuses = ['pending', 'closed', 'completed'];
  $newStatus = $_POST['new_status'];
  $orderId = (int)$_POST['order_id'];

  if (in_array($newStatus, $allowedStatuses)) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $orderId]);
  }
}

// Brisanje poruke iz chata
if (isset($_POST['delete_chat_id'])) {
  $stmt = $pdo->prepare("DELETE FROM livechat WHERE id = ?");
  $stmt->execute([$_POST['delete_chat_id']]);
}

$liveChats = $pdo->query("SELECT * FROM livechat ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$contacts = $pdo->query("SELECT * FROM Kontakt_MSG ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sr">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f4f4f4;
    }
    .header {
      background: #3B1E54;
      color: #fff;
      padding: 15px 30px;
      font-size: 24px;
      font-weight: bold;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .container {
      display: flex;
      padding: 20px;
      gap: 20px;
      height: auto;
      flex-wrap: wrap;
    }
    .panel {
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      padding: 20px;
      flex: 1 1 30%;
      min-width: 300px;
      max-height: 80vh;
      overflow-y: auto;
    }
    h2 {
      color: #3B1E54;
      margin-bottom: 15px;
    }
    .msg-box {
      background: #f9f9f9;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 8px;
      position: relative;
    }
    .msg-box form textarea {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .msg-box form button,
    .panel form button {
      margin-top: 5px;
      background: #3B1E54;
      color: white;
      padding: 8px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .delete-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: #ff4d4d;
      border: none;
      color: white;
      padding: 5px 10px;
      border-radius: 5px;
      cursor: pointer;
    }
    .logout {
      background: #fff;
      color: #3B1E54;
      padding: 8px 15px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 14px;
    }
    .logout:hover {
      background: #ddd;
    }
    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }
    input[type="text"],
    textarea {
      width: 100%;
      padding: 8px;
      margin-bottom: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .status-label {
      font-weight: bold;
    }
    .status-pending { color: blue; }
    .status-closed { color: red; }
    .status-completed { color: green; }
  </style>
</head>
<body>
  <div class="header">
    Admin Panel
    <a class="logout" href="?logout=true">Logout</a>
  </div>

  <div class="container">
    <!-- Panel 1: Live chat -->
    <div class="panel">
      <h2>Live Chat</h2>
      <?php foreach ($liveChats as $chat): ?>
        <div class="msg-box">
          <form method="post" style="display:inline;">
            <input type="hidden" name="delete_chat_id" value="<?= $chat['id'] ?>">
            <button class="delete-btn" onclick="return confirm('Da li ste sigurni da želite da obrišete ovu poruku?')">X</button>
          </form>
          <strong><?= htmlspecialchars($chat['name']) ?>:</strong> <?= htmlspecialchars($chat['poruka']) ?><br>
          <form method="post" action="chat_reply.php">
            <input type="hidden" name="chat_id" value="<?= $chat['id'] ?>">
            <textarea name="reply" rows="2" placeholder="Odgovori..."></textarea>
            <button type="submit">Pošalji</button>
          </form>
          <?php if (!empty($chat['reply'])): ?>
            <div style="margin-top:5px;"><em><?= htmlspecialchars($chat['reply']) ?></em></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Panel 2: Kontakt poruke -->
    <div class="panel">
      <h2>Kontakt Poruke</h2>
      <?php foreach ($contacts as $msg): ?>
        <div class="msg-box">
          <strong><?= htmlspecialchars($msg['ime']) ?> (<?= htmlspecialchars($msg['email']) ?>)</strong><br>
          <small><?= $msg['datum'] ?> u <?= $msg['vreme'] ?></small><br>
          <?= nl2br(htmlspecialchars($msg['poruka'])) ?>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Panel 3: Nove Porudžbine -->
    <div class="panel">
      <h2>Nova Porudžbina</h2>
      <form method="post">
        <label>ORDER#:</label>
        <input type="text" name="order_number" required>
        
        <label>Klijent:</label>
        <input type="text" name="client_name" required>

        <label>Adresa klijenta:</label>
        <input type="text" name="client_address">

        <label>Opis:</label>
        <textarea name="description" rows="3"></textarea>

        <label><input type="checkbox" name="header"> HEADER</label><br>
        <label><input type="checkbox" name="info_redirect"> INFO REDIRECT</label><br>
        <label><input type="checkbox" name="contact"> CONTACT</label><br>
        <label><input type="checkbox" name="add_page"> ADD PAGE</label><br>
        <label><input type="checkbox" name="add_function"> ADD FUNCTION</label><br>
        <label><input type="checkbox" name="add_picture"> ADD PICTURE</label><br>

        <button type="submit">Sačuvaj</button>
      </form>
      <hr>
      <h3>Postojeće porudžbine:</h3> 
      <?php foreach ($orders as $order): ?>
        <div class="msg-box">
          <strong>ORDER#<?= htmlspecialchars($order['order_number']) ?></strong><br>
          Klijent: <?= htmlspecialchars($order['client_name']) ?><br>
          Adresa: <?= htmlspecialchars($order['client_address']) ?><br>
          Opis: <?= nl2br(htmlspecialchars($order['description'])) ?><br>
          Zadaci:
          <ul>
            <?= $order['header'] ? "<li>HEADER</li>" : "" ?>
            <?= $order['info_redirect'] ? "<li>INFO REDIRECT</li>" : "" ?>
            <?= $order['contact'] ? "<li>CONTACT</li>" : "" ?>
            <?= $order['add_page'] ? "<li>ADD PAGE</li>" : "" ?>
            <?= $order['add_function'] ? "<li>ADD FUNCTION</li>" : "" ?>
            <?= $order['add_picture'] ? "<li>ADD PICTURE</li>" : "" ?>
          </ul>

          <span class="status-label 
            <?= 'status-' . htmlspecialchars($order['status']) ?>">
            Status: <?= htmlspecialchars(ucfirst($order['status'])) ?>
          </span>

          <form method="post" style="margin-top: 8px;">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <select name="new_status">
              <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
              <option value="closed" <?= $order['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
              <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
            <button type="submit" name="change_status">Promeni status</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
