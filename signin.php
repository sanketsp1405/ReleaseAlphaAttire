<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role === 'admin') {
        // Check if an admin account exists
        $admin_check = $conn->query("SELECT COUNT(*) AS count FROM admin");
        $admin_count = $admin_check->fetch_assoc()['count'];

        if ($admin_count == 0) {
            echo '<p style="color:red;">Admin account does not exist. Please contact support.</p>';
        } else {
            $stmt = $conn->prepare("SELECT * FROM admin WHERE phone = ?");
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE phone_number = ?");
    }

    if ($stmt && $stmt !== false) {
        $stmt->bind_param('s', $phone_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['role'] = $role;
            header("Location: index.php");
            exit();
        } else {
            echo '<p style="color:red;">Invalid phone number or password.</p>';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 350px;
            position: relative;
            overflow: hidden;
        }

        .form-container:before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .form-container:after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        h2 {
            margin-bottom: 30px;
            color: #333;
            font-weight: bold;
            font-size: 24px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 16px;
            background-color: #f9f9f9;
        }

        input[type="submit"] {
            background-color: #ff7e67;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #ff5733;
        }

        .form-container a {
            display: block;
            margin-top: 20px;
            color: #ff7e67;
            text-decoration: none;
            font-weight: bold;
        }

        .form-container a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Sign In</h2>
        <form action="signin.php" method="POST">
            <select name="role" required>
                <option value="user">Customer</option>
                <option value="admin">Admin</option>
            </select>
            <input type="text" name="phone_number" placeholder="Phone Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Sign In">
        </form>
        <a href="signup.php">Don't have an account? Sign Up</a>
    </div>
</body>
</html>
