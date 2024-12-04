<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: signin.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session upon login

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $delivery_address = htmlspecialchars(trim($_POST['delivery_address']));
    $state = htmlspecialchars(trim($_POST['state']));
    $payment_method = htmlspecialchars(trim($_POST['payment_method']));

    // Calculate total price from cart
    $total_price = 0;
    $cart_items = [];
    $image1 = ''; // Variable to hold the image of the first product

    // Fetch cart items for the user
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];

        // Fetch product details including image
        $product_stmt = $conn->prepare("SELECT name, price, image1 FROM products WHERE id = ?");
        $product_stmt->bind_param("i", $product_id);
        $product_stmt->execute();
        $product = $product_stmt->get_result()->fetch_assoc();
        $price = $product['price'];
        $total_price += $price * $quantity;

        $cart_items[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $price,
            'product_name' => $product['name'],
            'image1' => $product['image1'],  // Get image URL
            'total_price' => $price * $quantity
        ];

        // Get the first image for the order
        if (empty($image1)) {
            $image1 = $product['image1']; // Assign the first image encountered
        }

        $product_stmt->close();
    }
    $stmt->close();

    // Insert order into the orders table with image1 and default statuses
    $stmt = $conn->prepare("INSERT INTO orders (user_id, full_name, email, phone_number, delivery_address, state, total_price, payment_method, image1, payment_status, delivery_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 'Processing')");
    $stmt->bind_param("isssssdss", $user_id, $full_name, $email, $phone_number, $delivery_address, $state, $total_price, $payment_method, $image1);
    if (!$stmt->execute()) {
        die("Error inserting order: " . $stmt->error);
    }
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert each item into the order_items table with user_id
// Insert each item into the order_items table with user_id
$stmt = $conn->prepare("INSERT INTO order_items (order_id, user_id, product_id, product_name, quantity, price, total_price, image1) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($cart_items as $item) {
    $stmt->bind_param("iisiddss", $order_id, $user_id, $item['product_id'], $item['product_name'], $item['quantity'], $item['price'], $item['total_price'], $item['image1']);
    if (!$stmt->execute()) {
        die("Error inserting order item: " . $stmt->error);
    }
}
$stmt->close();

    // Clear the user's cart after checkout
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Generate receipt (you can modify the format as needed)
    ob_start(); // Start output buffering
    ?>
 <style>
    body {
        font-family: 'Poppins', sans-serif;
        background:#34495e;
        padding: 20px;
        color: #fff;
        margin: 0;
    }

    .receipt {
        max-width: 600px;
        margin: 0 auto;
        background:#fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
        position: relative;
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .receipt-header h1 {
        font-size: 32px;
        font-weight: 800;
        color: black;
        letter-spacing: 2px;
     
    }

    .receipt-header h2 {
        font-size: 24px;
        font-weight: 600;
        color: black;
    }

    .receipt p {
        color: black;
        margin: 10px 0;
    }

    .receipt ul {
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }

    .receipt ul li {
        background-color: rgba(255, 255, 255, 0.1);
        padding: 15px;
        border-radius: 8px;
        margin: 8px 0;
        color: black;
        font-size: 18px;
        font-weight: 500;
    }

    .total {
        font-weight: 700;
        font-size: 22px;
        color: #f8b500;
        margin-top: 20px;
    }
    .download-button {
    display: inline-block;
    padding: 15px;
    width: auto;
    background-color: #27ae60;
    color: #fff;
    text-align: center;
    border-radius: 8px;
    font-size: 20px;
    margin-top: 25px;
    font-weight: bold;
    transition: all 0.4s ease;
    text-decoration: none;
    margin-left: 45%;
}


    .download-button:hover {
        background-color: #c29e35;
        transform: scale(1.05);
    }
</style>


    <div class="receipt">
        <div class="receipt-header">
            <h1>AlphaAttire</h1>
            <h2>Order Receipt</h2>
        </div>
        <p>Thank you for your order, <?= $full_name ?>!</p>
        <p>Your Order ID is: <strong><?= $order_id ?></strong></p>
        <p>Items:</p>
        <ul>
            <?php foreach ($cart_items as $item): ?>
                <li><?= $item['product_name'] ?> (x<?= $item['quantity'] ?>) - ₹<?= number_format($item['total_price'], 2) ?></li>
            <?php endforeach; ?>
        </ul>
        <p class="total">Total Price: ₹<?= number_format($total_price, 2) ?></p>
        <p>Delivery Address: <?= $delivery_address ?>, <?= $state ?></p>
        <p>Contact: <?= $phone_number ?></p>
        <p>Payment Method: <?= $payment_method ?></p>
        <p>Payment Status: <strong>Pending</strong></p>
        <p>Delivery Status: <strong>Processing</strong></p>
        <p>Thank you for shopping with us!</p>
    </div>
    <?php
    $receipt = ob_get_clean(); // Get the buffered content and clean the buffer

    // Save the receipt to a file
    $receipt_file = "receipts/receipt_$order_id.html";
    file_put_contents($receipt_file, $receipt);

    // Display the receipt
    echo $receipt;

    // Provide a download link for the receipt
    echo "<p><a href='$receipt_file' class='download-button' download>Download Receipt</a></p>";

    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #34495e;
        padding: 20px;
        margin: 0;
        color: #34495e;
    }

    .checkout-container {
        max-width: 650px;
        margin: 0 auto;
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 1);
        position: relative;
    }

    .checkout-container::before {
        content: '';
        position: absolute;
        bottom: -60px;
        left: -60px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
    }

    h2 {
        text-align: center;
        font-size: 30px;
        color: black;
        margin-bottom: 30px;
        font-weight: 800;
        text-transform: uppercase;
    }

    label {
        font-size: 18px;
        color: fff;
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }

    input, textarea, select {
        padding: 14px;
        border: none;
        border-radius: 8px;
        background-color: rgba(255, 255, 255, 0.1);
        color: black;
        font-size: 16px;
        margin-top: 8px;
        display: block;
        width: 100%;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    input:focus, textarea:focus, select:focus {
        outline: none;
        border: 1px solid #d4af37;
        box-shadow: 0 0 12px rgba(212, 175, 55, 0.8);
    }

    button {
        padding: 16px;
        background-color: #27ae60;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 20px;
        width: 100%;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.4s ease;
    }

    button:hover {
        background-color: #c29e35;
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
    }
</style>


</head>
<body>
    <div class="checkout-container">
        <h2>Checkout</h2>
        <form method="POST" action="">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>
            
            <label for="delivery_address">Delivery Address:</label>
            <textarea id="delivery_address" name="delivery_address" rows="4" required></textarea>
            
            <label for="state">State:</label>
            <input type="text" id="state" name="state" required>
            
            <label for="payment_method">Payment Method:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="Credit Card">Credit Card</option>
                <option value="Debit Card">Debit Card</option>
                <option value="Cash on Delivery">Cash on Delivery</option>
            </select>
            
            <button type="submit">Proceed to Checkout</button>
        </form>
    </div>
</body>
</html>
