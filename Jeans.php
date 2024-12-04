<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeans</title>
    <link rel="stylesheet" href="Prostyles.css">
    <style>
        .image-slider {
            position: relative;
            width: 100%;
            overflow: hidden;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .image-slider img {
            width: auto;
            height: 100%;
            object-fit: contain;
            display: none;
        }

        .image-slider img.active {
            display: block;
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
            border: none;
            color: gray;
            padding: 3px 6px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 50%;
        }

        .slider-nav button:hover {
            background-color: rgba(200, 200, 200, 0.9);
            color: black;
        }

        .out-of-stock {
            color: orangered;
            font-weight: bold;
        }

        .low-stock {
            color: orangered;
            font-weight: bold;
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
            <option value="Roadster">Roadster</option>
            <option value="Flying Machine">Flying Machine</option>
            <option value="Spykar">Spykar</option>
            <option value="Pape Jeans">Pape Jeans</option>
            <option value="Levis">Levis</option>
            <option value="Wrogn">Wrogn</option>
            <option value="HIGHLANDER">HIGHLANDER</option>
            <option value="Wrangler">Wrangler</option>
        </select>

        <label for="sizeFilter">Size:</label>
        <select id="sizeFilter" onchange="filterProducts()">
            <option value="all">All</option>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
            <option value="XXL">XXL</option>
        </select>
    </div>
    <div class="container">
        <?php
        session_start();
        include 'db.php';

        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
            header("Location: signin.php");
            exit();
        }

        $userId = $_SESSION['user_id'];

        // Modify SQL query to fetch the stock information
        $sql = "SELECT id, name, price, image1, image2, image3, size, brand, stock FROM products WHERE sub_category_id = 13";
        $result = $conn->query($sql);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
            $product_id = $_POST['product_id'];
            $quantity = $_POST['quantity'] ?? 1;
            $image = $_POST['image'];

            // Check if there is enough stock
            $stockQuery = "SELECT stock FROM products WHERE id = ?";
            $stmt = $conn->prepare($stockQuery);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $stmt->bind_result($stock);
            $stmt->fetch();
            $stmt->close();

            if ($stock >= $quantity) {
                // Prepare and bind for cart insertion
                $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, image1) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiis", $userId, $product_id, $quantity, $image);

                if ($stmt->execute()) {
                    // Reduce stock in the products table
                    $newStock = $stock - $quantity;
                    $updateStockQuery = "UPDATE products SET stock = ? WHERE id = ?";
                    $updateStmt = $conn->prepare($updateStockQuery);
                    $updateStmt->bind_param("ii", $newStock, $product_id);
                    $updateStmt->execute();
                    echo "<script>alert('Product added to cart!');</script>";
                } else {
                    echo "<script>alert('Failed to add product to cart.');</script>";
                }

                $stmt->close();
            } else {
                echo "<script>alert('Not enough stock available.');</script>";
            }
        }

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $stock = $row['stock'];
                $stockMessage = '';

                if ($stock == 0) {
                    $stockMessage = '<p class="out-of-stock">Out of Stock</p>';
                } elseif ($stock < 5) {
                    $stockMessage = '<p class="low-stock">Only ' . $stock . ' left!</p>';
                }
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
                    <?php echo $stockMessage; ?>
                    <form method="POST" action="">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="image" value="<?php echo htmlspecialchars($row['image1']); ?>"> 
                        
                        <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                    </form>
                    <button class="add-to-wishlist" onclick="addToWishlist('<?php echo $row['id']; ?>','<?php echo $row['name']; ?>', '<?php echo $row['price']; ?>', '<?php echo $row['image1']; ?>'); event.stopPropagation();">Add to Wishlist</button>
                </div>
        <?php
            }
        } else {
            echo "<p>No products available.</p>";
        }

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

        function prevImage(button) {
            const images = button.closest('.image-slider').querySelectorAll('img');
            let activeIndex = Array.from(images).findIndex(img => img.classList.contains('active'));
            images[activeIndex].classList.remove('active');
            activeIndex = (activeIndex === 0) ? images.length - 1 : activeIndex - 1;
            images[activeIndex].classList.add('active');
        }

        function nextImage(button) {
            const images = button.closest('.image-slider').querySelectorAll('img');
            let activeIndex = Array.from(images).findIndex(img => img.classList.contains('active'));
            images[activeIndex].classList.remove('active');
            activeIndex = (activeIndex === images.length - 1) ? 0 : activeIndex + 1;
            images[activeIndex].classList.add('active');
        }

        function addToWishlist(productId, productName, productPrice, productImage) {
            console.log('Product added to wishlist:', productName);
            // Additional functionality to save to wishlist can be added here
        }
    </script>
</body>

</html>