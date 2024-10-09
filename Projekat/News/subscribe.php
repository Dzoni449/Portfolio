<?php
session_start();
require 'database/conn.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; 
    $category_id = $_POST['category_id'];
    
    
    if (isset($_POST['action']) && $_POST['action'] === 'subscribe') {
        
        $stmt = $con->prepare("SELECT * FROM Subscriptions WHERE user_id = ? AND category_id = ?");
        $stmt->bind_param('ii', $user_id, $category_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            
            $stmt = $con->prepare("INSERT INTO Subscriptions (user_id, category_id) VALUES (?, ?)");
            $stmt->bind_param('ii', $user_id, $category_id);

            if ($stmt->execute()) {
                echo 'success';
            } else {
                echo 'error';
            }
        } else {
            echo 'already_subscribed';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'unsubscribe') {
       
        $stmt = $con->prepare("DELETE FROM Subscriptions WHERE user_id = ? AND category_id = ?");
        $stmt->bind_param('ii', $user_id, $category_id);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'invalid_action';
    }
}
?>
