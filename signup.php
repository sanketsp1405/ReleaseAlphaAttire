<?php
include 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            if ($role === 'admin') {
                $admin_check = $conn->query("SELECT COUNT(*) AS count FROM admin");
                $admin_count = $admin_check->fetch_assoc()['count'];

                if ($admin_count > 0) {
                    $message = '<p class="error-message">Only one admin account is allowed. Admin registration failed.</p>';
                } else {
                    $stmt = $conn->prepare("INSERT INTO admin (phone, password) VALUES (?, ?)");
                    $stmt->bind_param('ss', $phone_number, $hashed_password);
                    $stmt->execute();
                    $message = '<p class="success-message">Admin registration successful!</p>';
                }
            } else {
                $name = htmlspecialchars($_POST['name']);
                $stmt = $conn->prepare("INSERT INTO users (name, phone_number, password) VALUES (?, ?, ?)");
                $stmt->bind_param('sss', $name, $phone_number, $hashed_password);
                $stmt->execute();
                $message = '<p class="success-message">Registration successful!</p>';
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $message = '<p class="error-message">You already have an account. Please sign in.</p>';
            } else {
                $message = '<p class="error-message">Error: ' . $e->getMessage() . '</p>';
            }
        }
    } else {
        $message = '<p class="error-message">Password cannot be empty.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4f7;
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
        }

        h2 {
            margin-bottom: 10px;
            color: #333;
            font-weight: bold;
            font-size: 24px;
        }

        .message-container {
            margin-bottom: 20px;
        }

        .error-message {
            color: red;
            font-size: 14px;
        }

        .success-message {
            color: green;
            font-size: 14px;
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
    </style>
</head>
<body>
    <div class="form-container">
        <div class="message-container">
            <?php echo $message; ?>
        </div>
        <h2>Sign Up</h2>
        <form action="signup.php" method="POST">
            <select name="role" required>
                <option value="user">Customer</option>
                <option value="admin">Admin</option>
            </select>
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="text" name="phone_number" placeholder="Phone Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Sign Up">
        </form>
        <a href="signin.php">Already have an account? Sign In</a>
    </div>
</body>
</html>
