<?php
session_start();
require 'connection.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        if ($user['role'] == 1) {
            $_SESSION['admin'] = true;
            header("Location: admindashboard.php");
            exit;
        } else {
            $error = "Nemate administratorska prava.";
        }
    } else {
        $error = "Pogrešno korisničko ime ili lozinka.";
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-box {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
      width: 320px;
      text-align: center;
    }
    .login-box h2 {
      margin-bottom: 20px;
      color: #3B1E54;
    }
    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      width: 100%;
      background: #3B1E54;
      color: #fff;
      border: none;
      padding: 12px;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    button:hover {
      background: #29123f;
    }
    .error {
      color: red;
      font-size: 14px;
      margin-top: 10px;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <h2>Admin Login</h2>
    <form method="post">
      <input type="text" name="username" placeholder="Korisničko ime" required>
      <input type="password" name="password" placeholder="Lozinka" required>
      <button type="submit">Prijavi se</button>
    </form>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
  </div>

</body>
</html>
