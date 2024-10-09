<?php 
session_start();
require_once "database/conn.php";

if (isset($_SESSION['user'])) {
    header('Location: adminDashboard.php'); 
    exit();
}

$login_err = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($password)) {
        
        $sql = "SELECT id, username, password, role FROM Users WHERE username = ?";
        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $username, $hashed_password, $role);
                $stmt->fetch();

                
                if (password_verify($password, $hashed_password)) {
                    $_SESSION['user'] = [
                        'id' => $id,
                        'username' => $username,
                        'role' => $role
                    ];
                    
                    if ($role === 'admin') {
                        header("Location: adminDashboard.php");
                        exit();
                    } else {
                        $login_err = "Nemate privilegije za pristup admin panelu.";
                    }
                
                
                } else {
                    $login_err = "Pogrešna lozinka.";
                }
            } else {
                $login_err = "Ne postoji korisnik sa ovim korisničkim imenom.";
            }
            $stmt->close();
        }
    } else {
        $login_err = "Unesite korisničko ime i lozinku.";
    }
}

$con->close();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: white;
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            max-width: 400px;
            width: 100%;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container input[type="text"], .login-container input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        .login-container input[type="submit"] {
            padding: 10px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .login-container input[type="submit"]:hover {
            background-color: #c0392b;
        }

        .login-container .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php
        if (!empty($login_err)) {
            echo '<div class="error">' . $login_err . '</div>';
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="text" name="username" placeholder="Korisničko ime" required>
            <input type="password" name="password" placeholder="Lozinka" required>
            <input type="submit" value="Prijavi se">
        </form>
    </div>
</body>
</html>
