<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FormalShoes</title>
    <link rel="stylesheet" href="Prostyles.css">
    <style>
        .image-slider {
            position: relative;
            width: 100%;
            overflow: hidden;
            height: 300px;
            display: flex;
            /* Enables flex layout */
            justify-content: center;
            /* Centers the images horizontally */
            align-items: center;
            /* Centers the images vertically */
        }

        .image-slider img {
            width: auto;
            /* Ensures the image maintains its aspect ratio */
            height: 100%;
            /* Adjusts the height to fit the slider */
            object-fit: contain;
            /* Prevents image cropping while maintaining proportions */
            display: none;
            /* Default state */
        }

        .image-slider img.active {
            display: block;
            /* Show only active image */
        }


        .slider-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
        }

        .slider-nav button {
            background-color: rgba(255, 255, 255, 0.7);
            /* Faint gray background */
            border: none;
            color: gray;
            /* Faint color for the icons */
            padding: 3px 6px;
            /* Reduced padding to make icons smaller */
            font-size: 14px;
            /* Smaller font size */
            cursor: pointer;
            border-radius: 50%;
            /* Rounded buttons for better aesthetics */
        }

        .slider-nav button:hover {
            background-color: rgba(200, 200, 200, 0.9);
            /* Slightly darker gray on hover */
            color: black;
            /* Darker color for better visibility */
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Filter Products</h2>
        <label for="priceFilter">Price:</label>
        <select id="priceFilter" onchange="filterProducts()">
            <option value="all">All</option>
            <option value="0-1000">₹0 - ₹1000</option>
            <option value="1001-2000">₹1001 - ₹2000</option>
            <option value="2001-3000">₹2001 - ₹3000</option>
        </select>

        <label for="brandFilter">Brand:</label>
        <select id="brandFilter" onchange="filterProducts()">
            <option value="all">All</option>
            <option value="Bata">Bata</option>
            <option value="Lee Cooper">Lee Cooper</option>
            <option value="Red Cheif">Red Cheif</option>
            <option value="Mochi">Mochi</option>
            <option value="Red Tape">Red Tape</option>
            <option value="Van Heusen">Van Heusen</option>
        </select>

        <label for="sizeFilter">Size:</label>
        <select id="sizeFilter" onchange="filterProducts()">
            <option value="all">All</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
        </select>
    </div>

    <div class="container">
        <?php
        session_start(); // Start the session to access user ID
        include 'db.php';  // Make sure the path to db.php is correct

        // Check if the user is logged in and retrieve user ID
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
            header("Location: signin.php");
            exit();
        }

        $userId = $_SESSION['user_id']; // Retrieve user ID from session

        // SQL query to fetch product data where sub_category_id = 16
        $sql = "SELECT id, name, price, image1, image2, image3, size, brand 
                FROM products 
                WHERE sub_category_id = 11";
        $result = $conn->query($sql);
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
            $product_id = $_POST['product_id'];
            $quantity = $_POST['quantity'] ?? 1; // Default quantity is 1
            $image = $_POST['image']; // Get the image URL

            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, image1) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $userId, $product_id, $quantity, $image1);

            // Execute the query
            if ($stmt->execute()) {
                echo "<script>alert('Product added to cart!');</script>";
            } else {
                echo "<script>alert('Failed to add product to cart.');</script>";
            }

            // Close statement
            $stmt->close();
        }

        // Check if there are any products in the database
        if ($result->num_rows > 0) {
            // Loop through each product and display it in a card
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="card" data-price="<?php echo $row['price']; ?>" data-brand="<?php echo $row['brand']; ?>" data-size="<?php echo $row['size']; ?>">
                    <div class="image-slider">
                        <img src="<?php echo $row['image1']; ?>" alt="<?php echo $row['name']; ?>" class="active">
                        <img src="<?php echo $row['image2']; ?>" alt="<?php echo $row['name']; ?>">
                        <img src="<?php echo $row['image3']; ?>" alt="<?php echo $row['name']; ?>">
                        <div class="slider-nav">
                            <button onclick="prevImage(this)">&#10094;</button>
                            <button onclick="nextImage(this)">&#10095;</button>
                        </div>
                    </div>
                    <p><?php echo $row['name']; ?></p>

                    <p>MRP: ₹<?php echo number_format($row['price'], 2); ?></p>
                    <form method="POST" action="">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="image" value="<?php echo htmlspecialchars($row['image1']); ?>"> <!-- Image -->
                       
                        <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                    </form>
                    <button class="add-to-wishlist" onclick="addToWishlist('<?php echo $row['id']; ?>','<?php echo $row['name']; ?>', '<?php echo $row['price']; ?>', '<?php echo $row['image1']; ?>'); event.stopPropagation();">Add to Wishlist</button>
                </div>
        <?php
            }
        } else {
            // If no products are available
            echo "<p>No products available.</p>";
        }

        // Optionally close the connection
        $conn->close();
        ?>
    </div>

    <script>
        function filterProducts() {
            const priceFilter = document.getElementById('priceFilter').value;
            const brandFilter = document.getElementById('brandFilter').value;
            const sizeFilter = document.getElementById('sizeFilter').value;

            const cards = document.querySelectorAll('.card');

            cards.forEach(card => {
                const cardPrice = parseFloat(card.getAttribute('data-price'));
                const cardBrand = card.getAttribute('data-brand');
                const cardSize = card.getAttribute('data-size');

                let priceMatch = false;
                let brandMatch = false;
                let sizeMatch = false;

                if (priceFilter === 'all') {
                    priceMatch = true;
                } else {
                    const [minPrice, maxPrice] = priceFilter.split('-').map(Number);
                    if (cardPrice >= minPrice && cardPrice <= maxPrice) {
                        priceMatch = true;
                    }
                }

                if (brandFilter === 'all' || cardBrand === brandFilter) {
                    brandMatch = true;
                }

                if (sizeFilter === 'all' || cardSize === sizeFilter) {
                    sizeMatch = true;
                }

                if (priceMatch && brandMatch && sizeMatch) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function openProductDetail(name, price, image, brand, description, image1, image2, image3) {
            const url = `productpage.html?name=${encodeURIComponent(name)}&price=${price}&image=${image}&brand=${brand}&description=${encodeURIComponent(description)}&image1=${image1}&image2=${image2}&image3=${image3}`;
            window.location.href = url;
        }

        function addToCart(name, price, image) {
            // Example function to handle adding to cart
            console.log(`Added to cart: ${name}, ${price}, ${image}`);
            // You might want to handle actual cart logic here
        }

        function addToWishlist(id, name, price, image) {
            const wishlistItem = {
                id,
                name,
                price,
                image
            };
            let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

            const itemExists = wishlist.some(item => item.id === id);
            if (!itemExists) {
                wishlist.push(wishlistItem);
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
                alert(`${name} has been added to your wishlist!`);
            } else {
                alert(`${name} is already in your wishlist!`);
            }
        }
        function nextImage(button) {
            const slider = button.closest('.image-slider');
            const images = slider.querySelectorAll('img');
            const activeIndex = Array.from(images).findIndex(img => img.classList.contains('active'));
            images[activeIndex].classList.remove('active');
            images[(activeIndex + 1) % images.length].classList.add('active');
        }

        function prevImage(button) {
            const slider = button.closest('.image-slider');
            const images = slider.querySelectorAll('img');
            const activeIndex = Array.from(images).findIndex(img => img.classList.contains('active'));
            images[activeIndex].classList.remove('active');
            images[(activeIndex - 1 + images.length) % images.length].classList.add('active');
        }
    </script>
    <script src="cart.js"></script>

</body>

</html>