<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watches</title>
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
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Filter Products</h2>

        <label for="priceFilter">Price:</label>
        <select id="priceFilter" onchange="filterProducts()">
            <option value="all">All</option>
            <option value="0-2000">₹0 - ₹2000</option>
            <option value="2001-5000">₹2001 - ₹5000</option>
            <option value="5001-10000">₹5001 - ₹10000</option>
            <option value="10001-20000">₹10001 - ₹20000</option>
        </select>

        <label for="brandFilter">Brand:</label>
        <select id="brandFilter" onchange="filterProducts()">
            <option value="all">All</option>
            <option value="Titan">Titan</option>
            <option value="Casio">Casio</option>
            <option value="Fastrack">Fastrack</option>
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
        $sql = "SELECT id, name, price, image1, image2, image3, size, brand FROM products WHERE sub_category_id = 1";
        $result = $conn->query($sql);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
            $product_id = $_POST['product_id'];
            $quantity = $_POST['quantity'] ?? 1;
            $image1 = $_POST['image'];

            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, image1) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $userId, $product_id, $quantity, $image1);

            if ($stmt->execute()) {
                echo "<script>alert('Product added to cart!');</script>";
            } else {
                echo "<script>alert('Failed to add product to cart.');</script>";
            }

            $stmt->close();
        }
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="card" data-price="<?php echo $row['price']; ?>" data-brand="<?php echo $row['brand']; ?>">
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
            const cards = document.querySelectorAll('.card');

            cards.forEach(card => {
                const cardPrice = parseFloat(card.getAttribute('data-price'));
                const cardBrand = card.getAttribute('data-brand');

                const priceMatch = priceFilter === 'all' || 
                    (cardPrice >= parseInt(priceFilter.split('-')[0]) && cardPrice <= parseInt(priceFilter.split('-')[1]));
                const brandMatch = brandFilter === 'all' || cardBrand === brandFilter;

                card.style.display = priceMatch && brandMatch ? 'block' : 'none';
            });
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

        function addToWishlist(id, name, price, image) {
            const wishlistItem = { id, name, price, image };
            let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

            if (!wishlist.some(item => item.id === id)) {
                wishlist.push(wishlistItem);
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
                alert(`${name} has been added to your wishlist!`);
            } else {
                alert(`${name} is already in your wishlist!`);
            }
        }
    </script>
</body>

</html>