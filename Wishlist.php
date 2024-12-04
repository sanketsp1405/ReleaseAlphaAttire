<?php
session_start();
include 'db.php'; // Include your database connection

// Handle adding items to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $itemId = $_POST['item_id'];

    // Fetch item details from products table based on the item ID
    $query = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $query->bind_param("i", $itemId);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();

        // Prepare to insert into the cart table
        $cartQuery = $conn->prepare("INSERT INTO cart (product_id, quantity, user_id) VALUES (?, 1, ?)");
        $userId = $_SESSION['user_id']; // Assuming you have a session variable for the user ID
        $cartQuery->bind_param("ii", $item['id'], $userId);
        $cartQuery->execute();

        echo json_encode(['success' => true, 'message' => 'Item added to cart!']);
        exit; // Exit after processing the AJAX request
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found.']);
        exit; // Exit after processing the AJAX request
    }
}

// Fetch all main categories with their associated subcategories
$query = $conn->query("
    SELECT main_category.name AS main_name, sub_category.id AS sub_id, sub_category.name AS sub_name, sub_category.link AS sub_link
    FROM main_category
    LEFT JOIN sub_category ON sub_category.main_category_id = main_category.id
    ORDER BY main_category.name, sub_category.name;
");

// Initialize the $categories array
$categories = [];

if ($query && $query->num_rows > 0) {
    // Populate the array by grouping subcategories under their respective main categories
    while ($row = $query->fetch_assoc()) {
        // Only add non-empty subcategory names
        if (!empty($row['sub_name'])) {
            $categories[$row['main_name']][] = [
                'id' => $row['sub_id'],
                'name' => $row['sub_name'],
                'link' => $row['sub_link'], // URL for the subcategory
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Wishlist</title>
    <link rel="stylesheet" href="Wishlist.css">
    <style>
        /* Additional CSS for styling the logo and name */
        .logo {
            display: flex;
            align-items: center; /* Center items vertically */
        }

        .logo img {
            height: 50px; /* Adjust the height as needed */
            margin-right: 10px; /* Space between logo and text */
        }

        .logo span {
            font-weight: bold;
            font-size: 24px;
            font-family: 'Old Standard TT', serif; /* Ensure the font is applied */
        }
        /* Style for the add-to-cart button */
.add-to-cart {
    background-color: #28a745; /* Green background */
    color: white; /* White text color */
    border: none; /* Remove border */
    border-radius: 5px; /* Rounded corners */
    /* Vertical and horizontal padding */
    height: 40px;
    width: 150px;
    font-size: 16px; /* Font size */
    cursor: pointer; /* Pointer cursor on hover */
    transition: background-color 0.3s, transform 0.3s; /* Smooth transitions for background and scale */
}

/* Change the background color on hover */
.add-to-cart:hover {
    background-color: #218838; /* Darker green on hover */
    transform: scale(1.05); /* Slightly enlarge on hover */
}

/* Optional: Style for active state */
.add-to-cart:active {
    transform: scale(0.95); /* Slightly shrink when clicked */
}

    </style>
</head>

<body>
    <header>
        <div class="logo">
            <a href="images/logoAlphaAttire.jpg">
                <img src="images/logoAlphaAttire.jpg" alt="AlphaAttire">
            </a>
            <span>AlphaAttire</span>
        </div>

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
                    <a href="Wishlist.php">Wishlist</a>
                    <a href="account.php">Account</a>
                    <a href="Myorder.php">My Orders</a>
                    <a href="aboutUs.html">About Us</a>

                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <h1>Your Wishlist</h1>
    <div class="container" id="wishlistContainer"></div>

    <script>
        // Load Wishlist items from localStorage
        function loadWishlist() {
            const wishlistContainer = document.getElementById('wishlistContainer');
            const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

            wishlistContainer.innerHTML = ''; // Clear the container before reloading

            if (wishlist.length === 0) {
                wishlistContainer.innerHTML = '<p>Your wishlist is empty!</p>';
                return;
            }

            wishlist.forEach(item => {
                const card = document.createElement('div');
                card.classList.add('card');
                card.innerHTML = `
                    <img src="${item.image}" alt="${item.name}" style="height: 300px;">
                    <p>${item.name}</p>
                    <p>MRP: ₹${item.price}</p>
                    <button class="add-to-cart" data-id="${item.id}">Add to Cart</button>
                    <button class="remove-from-wishlist" data-id="${item.id}">Remove from Wishlist</button>
                `;
                wishlistContainer.appendChild(card);
            });

            // Attach event listeners to the buttons after loading the wishlist
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', addToCart);
            });

            document.querySelectorAll('.remove-from-wishlist').forEach(button => {
                button.addEventListener('click', function() {
                    removeFromWishlist(this.dataset.id);
                });
            });
        }

        function addToCart(event) {
            const itemId = event.target.dataset.id;

            // Send AJAX request to add the item to the cart
            fetch('Wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `item_id=${itemId}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function removeFromWishlist(id) {
            let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
            wishlist = wishlist.filter(item => item.id != id);
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            loadWishlist(); // Reload wishlist after removal
        }

        // Initial call to load wishlist items
        loadWishlist();
    </script>
</body>

</html>
