<?php
session_start();
include 'db.php'; // Include your existing database connection file

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Handle removing items from the cart
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $remove_id);
    $stmt->execute();
    $stmt->close();
}

// Handle updating cart item quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update']) && isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cart_id = (int)$_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];

    // Fetch product stock
    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $stmt->bind_result($stock);
    $stmt->fetch();
    $stmt->close();

    // Ensure quantity doesn't exceed stock
    if ($quantity > 0 && $quantity <= $stock) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $quantity, $user_id, $cart_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "<script>alert('Invalid quantity. Please choose a quantity between 1 and $stock.');</script>";
    }
}

// Fetch cart items for the user
$stmt = $conn->prepare("SELECT cart.product_id, cart.quantity, products.name, products.price, products.image1, products.stock FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    echo "SQL error: " . $stmt->error; // Show any SQL errors
    exit();
}

$result = $stmt->get_result();
$cart_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

$stmt->close();

// Function to calculate total price and subtotal
function calculateCartTotals($cart) {
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    $shipping = 10.00; // Flat shipping fee
    $total = $subtotal + $shipping;

    return ['subtotal' => $subtotal, 'total' => $total];
}

// Calculate totals
$totals = calculateCartTotals($cart_items);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart</title>
    <link rel="stylesheet" href="cart.css">
</head>
<style>
    /* Additional CSS for styling the logo and name */
    .logo {
        display: flex;
        align-items: center;
    }
    .logo img {
        height: 50px;
        margin-right: 10px;
    }
    .logo span {
        font-weight: bold;
        font-size: 24px;
        font-family: 'Old Standard TT', serif;
    }
</style>
<body>

<header>
    <div class="logo">
        <a href="images/logoAlphaAttire.jpg">
            <img src="images/logoAlphaAttire.jpg" alt="AlphaAttire">
        </a>
        <span>AlphaAttire</span>
    </div>
    
    <?php
    // Fetch all main categories with their associated subcategories, including URLs
    $query = $conn->query("
        SELECT main_category.name AS main_name, sub_category.id AS sub_id, sub_category.name AS sub_name, sub_category.link AS sub_link
        FROM main_category
        LEFT JOIN sub_category ON sub_category.main_category_id = main_category.id
        ORDER BY main_category.name, sub_category.name;
    ");

    $categories = [];

    if ($query && $query->num_rows > 0) {
        while ($row = $query->fetch_assoc()) {
            if (!empty($row['sub_name'])) {
                $categories[$row['main_name']][] = [
                    'id' => $row['sub_id'],
                    'name' => $row['sub_name'],
                    'link' => $row['sub_link'],
                ];
            }
        }
    }
    ?>

    <nav>
        <ul>
        <li><a href="index.php">Home</a></li>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $main_category => $sub_categories): ?>
                    <li>
                        <a href="#"><?php echo htmlspecialchars($main_category); ?></a>
                        <?php if (!empty($sub_categories)): ?>
                            <ul class="dropdown">
                                <?php foreach ($sub_categories as $sub_category): ?>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($sub_category['link']); ?>">
                                            <?php echo htmlspecialchars($sub_category['name']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No categories available.</li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="user-icons">
            <a href="Wishlist.php">
                <svg class="w-6 h-6 text-gray-800 dark:text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.75 20.66l6.184-7.098c2.677-2.884 2.559-6.506.754-8.705-.898-1.095-2.206-1.816-3.72-1.855-1.293-.034-2.652.43-3.963 1.442-1.315-1.012-2.678-1.476-3.973-1.442-1.515.04-2.825.76-3.724 1.855-1.806 2.201-1.915 5.823.772 8.706l6.183 7.097c.19.216.46.34.743.34s.553-.124.743-.34Z" />
                </svg>
            </a>
            <a href="cart.php">
                <svg class="w-6 h-6 text-gray-800 dark:text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="black" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312" />
                </svg>
            </a>
            <a href="#">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16" class="bi bi-file-person">
                    <path d="M12 1a1 1 0 0 1 1 1v10.755S12 11 8 11s-5 1.755-5 1.755V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                    <path d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                </svg>
            </a>
            <div class="dropdown">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="dashboard.php"> Admin Dashboard</a></li>
                <?php else: ?>
                    <a href="signin.php">Sign up / Login</a>
                    <a href="account.php">Account</a>
                    <a href="Wishlist.php">Wishlist</a>
                    <a href="Myorder.php">My Orders</a>
                    <a href="aboutUs.html">About Us</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>
</header>



</header>

<main>
    <h1>Your Shopping Cart</h1>
    <section class="cart">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Remove</th>
                    <th>Stock Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cart_items)): ?>
                    <tr>
                        <td colspan="6">Your cart is empty.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($item['image1']); ?>" alt="Product Image" style="width:50px;height:auto;">
                                <p><?php echo htmlspecialchars($item['name']); ?></p>
                            </td>
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <form method="POST" action="cart.php">
                                    <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" max="<?php echo $item['stock']; ?>" required>
                                    <input type="hidden" name="cart_id" value="<?php echo $item['product_id']; ?>">
                                    <button type="submit" name="update">Update</button>
                                </form>
                            </td>
                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            <td><a href="?remove=<?php echo $item['product_id']; ?>" onclick="return confirm('Are you sure you want to remove this item?');">Remove</a></td>
                            <td>
                                <?php if ($item['stock'] == 0): ?>
                                    <span style="color:red;">Out of Stock</span>
                                <?php elseif ($item['stock'] < 5): ?>
                                    <span style="color:orange;">Only <?php echo $item['stock']; ?> left in stock</span>
                                <?php else: ?>
                                    <span>In Stock</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section class="cart-totals">
        <h2>Cart Totals</h2>
        <p>Subtotal: ₹<?php echo number_format($totals['subtotal'], 2); ?></p>
        <p>Shipping: ₹10.00</p>
        <p>Total: ₹<?php echo number_format($totals['total'], 2); ?></p>
        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
    </section>
</main>

</body>
</html>