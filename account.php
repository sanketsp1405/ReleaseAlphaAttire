<?php
session_start();
include('db.php');

// Initialize error messages
$error_messages = [];

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch the user's data from the database
$sql = "SELECT name, phone_number FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Output the user data
    $user = $result->fetch_assoc();
} else {
    echo "No user found.";
    exit();
}

// Update user information
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_info'])) {
    $new_name = $_POST['name'];
    $new_phone = $_POST['phone_number'];

    // Validate inputs
    if (empty($new_name) || empty($new_phone)) {
        $error_messages[] = "Name and phone number are required.";
    } else {
        // Update the user's data in the database
        $update_sql = "UPDATE users SET name = ?, phone_number = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $new_name, $new_phone, $user_id);

        if ($update_stmt->execute()) {
            // Update successful
            $user['name'] = $new_name; // Update session variable
            $user['phone_number'] = $new_phone; // Update session variable
        } else {
            $error_messages[] = "Error updating user information.";
        }

        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Page</title>
    <style>
        /* Basic styling for the account page */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #34495e; /* Soft light background */
            color: #333;
            margin: 0;
            padding: 0;
        }
        .account-info {
            margin: 50px auto;
            padding: 20px;
            max-width: 500px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #ffffff; /* White background for card */
            box-shadow: 0 8px 20px black; /* Light shadow */
            transition: box-shadow 0.3s; /* Smooth transition */
        }
        .account-info:hover {
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2); /* Enhanced shadow on hover */
        }
        .account-info h2 {
            text-align: center;
            color: #00796b; /* Teal color for heading */
        }
        .account-info p {
            font-size: 18px;
            margin: 10px 0;
            color: #555; /* Darker gray for text */
        }
        .edit-button, .update-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #27ae60; /* Light blue background */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s; /* Smooth transition */
        }
        .edit-button:hover, .update-button:hover {
            background-color: #4fc3f7; /* Darker blue on hover */
        }
        .error {
            color: #e57373; /* Light red for error messages */
            font-size: 14px;
            margin: 10px 0;
            text-align: center;
        }
        .editable {
            display: none; /* Hidden by default */
        }
    </style>
</head>
<body>

<div class="account-info">
    <h2>Account Information</h2>
    
    <!-- Display error messages -->
    <?php if (!empty($error_messages)): ?>
        <div class="error">
            <?php foreach ($error_messages as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div id="display-info">
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
        <button type="button" class="edit-button" id="edit-button" onclick="showEditFields()">Edit Details</button>
    </div>

    <div id="editable-fields" class="editable">
        <form action="" method="post">
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required placeholder="Enter your name" style="width: calc(100% - 22px); padding: 10px; margin-bottom: 10px;">
            <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required placeholder="Enter your phone number" style="width: calc(100% - 22px); padding: 10px; margin-bottom: 10px;">
            <button type="submit" name="update_info" class="update-button">Update Information</button>
        </form>
    </div>
</div>

<script>
    // Function to show the edit fields
    function showEditFields() {
        document.getElementById('editable-fields').style.display = 'block'; // Show input fields
        document.getElementById('display-info').style.display = 'none'; // Hide display info
        document.getElementById('edit-button').style.display = 'none'; // Hide edit button
    }
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
