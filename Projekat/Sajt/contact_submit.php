<?php
require 'connection.php';

$ime = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$poruka = $_POST['message'] ?? '';


if ($ime && $email && $poruka) {
  $stmt = $pdo->prepare("INSERT INTO Kontakt_MSG (ime, email, poruka, datum, vreme) VALUES (?, ?, ?, CURDATE(), CURTIME())");
  $stmt->execute([$ime, $email, $poruka]);
  echo "OK";
} else {
  http_response_code(400);
  echo "Nevalidni podaci.";
}
?>
