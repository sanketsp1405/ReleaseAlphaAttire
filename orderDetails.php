<?php
include 'db.php'; // Include your database connection file

// Fetch total count of orders
$count_sql = "SELECT COUNT(*) AS total_orders FROM orders";
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$total_orders = $count_row['total_orders'];

// Fetch orders from the database
$sql = "SELECT 
            id,
            user_id,
            full_name,
            email,
            phone_number,
            delivery_address,
            state,
            total_price,
            payment_status,
            delivery_status,
            payment_method,
            created_at,
            image1 
        FROM orders"; // Adjust the table name if necessary

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
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
        .total-count {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Order Details</h1>

    <div class="total-count">
        <strong>Total Orders: <?php echo $total_orders; ?></strong>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Delivery Address</th>
                <th>State</th>
                <th>Total Price</th>
                <th>Payment Status</th>
                <th>Delivery Status</th>
                <th>Payment Method</th>
                <th>Created At</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['delivery_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['state']); ?></td>
                        <td><?php echo number_format($row['total_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['delivery_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <img src="<?php echo htmlspecialchars($row['image1']); ?>" alt="Order Image">
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="13" style="text-align:center;">No orders found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
