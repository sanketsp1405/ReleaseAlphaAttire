<?php
session_start(); // Start the session
include 'db.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to view your orders.";
    exit; // Exit if the user is not logged in
}

// Fetch the logged-in user's ID from the session
$userId = $_SESSION['user_id'];

// Fetch order items for the logged-in user, joining with the products table
$sql = "SELECT oi.*, p.name AS product_name, p.image1 FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId); // Assuming user_id is an INT
$stmt->execute();
$result = $stmt->get_result();

// Initialize total price variable
$totalPrice = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Items</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        img {
            max-width: 100px; /* Set a max width for images */
            height: auto; /* Maintain aspect ratio */
        }
        .total-price {
            text-align: right;
            margin: 20px 0;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <h1>Order Items</h1>
    
    <!-- Display total price above the table -->
    <div class="total-price">
        <?php
        // Calculate total price
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $totalPrice += $row['total_price']; // Sum up total price
            }
            echo "Total Price: " . number_format($totalPrice, 2); // Display total price
        } else {
            echo "Total Price: 0.00"; // If no orders found
        }
        ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Image</th> <!-- New column for Product Image -->
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Reset the result pointer and display order items
            $result->data_seek(0); // Reset result set pointer to the beginning
            if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($row['image1']); ?>" 
                                 alt="<?php echo htmlspecialchars($row['product_name']); ?>" 
                                 style="width:50px; height:auto;">
                        </td> <!-- Display Product Image -->
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo number_format($row['total_price'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No orders found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
