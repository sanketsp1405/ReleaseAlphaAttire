<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlphaAttire - Men's Fashion</title>
    <link rel="stylesheet" href="IndexStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Old+Standard+TT&display=swap" rel="stylesheet">
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

        <?php
        include 'db.php'; // Include your database connection

        // Fetch all main categories with their associated subcategories, including URLs
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

        <?php
        session_start();
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

    <main>
        <section class="carousel">
            <div class="carousel-container">
                <div class="carousel-slide">
                    <img src="images/banner3.png" alt="Men's Jacket 1">
                </div>
                <div class="carousel-slide">
                    <img src="images/banner2.jpg" alt="Men's Jacket 2">
                </div>
                <div class="carousel-slide">
                    <img src="images/banner1.jpg" alt="Men's Jacket 3">
                </div>
            </div>
            <button class="prev">&lt;</button>
            <button class="next">&gt;</button>
        </section>

   

        <div class="category">
            <h1>Shop By Category</h1>
        </div>

        <div class="container">
            <?php
            include 'db.php'; // Include your database connection

            $query = $conn->query("SELECT * FROM featured_products");
            $products = $query->fetch_all(MYSQLI_ASSOC); // Fetch all results as an associative array

            foreach ($products as $product) {
                echo '<div class="card">';
                echo '<a href="' . htmlspecialchars($product['link']) . '">';
                echo '<img src="' . htmlspecialchars($product['image_url']) . '" alt="' . htmlspecialchars($product['alt_text']) . '">';
                echo '</a>';
                echo '</div>';
            }
            ?>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-section about">
                <h3>About AlphaAttire</h3>
                <p>AlphaAttire offers the latest trends in men's fashion, from topwear and bottomwear to accessories and footwear. Our goal is to keep you stylish and confident for every occasion.</p>
                <div class="social-icons">
                    <a href="#"><img src="images/Facebook.png" alt="Facebook"></a>
                    <a href="#"><img src="images/Twitter.png" alt="Twitter"></a>
                    <a href="#"><img src="images/instagram.png" alt="Instagram"></a>
                    <a href="#"><img src="images/linkedin.png" alt="LinkedIn"></a>
                </div>
            </div>

            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="signin.php">Sign up / Login</a></li>
                    <li><a href="aboutUs.html">About Us</a></li>
                    <li><a href="account.php">Account</a></li>
                    <li><a href="Myorder.php">My Orders</a></li>
            
                </ul>
            </div>

            <div class="footer-section contact">
                <h3>Contact Us</h3>
                <p>Email: aplhaattire07@gmail.com</p>
                <p>Phone: +91 9529941944</p>
                <p>Address: Warje,pune</p>
            </div>
        </div>
    </footer>
    
    <script src="Index.js"></script>
</body>

</html>
