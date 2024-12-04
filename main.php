<?php
$category_name = "Casual Shirts"; // For example, retrieving products from "Casual Shirts" category

// Connect to MySQL database
$connection = new mysqli("localhost", "root", "sanket1405", "TrendBro");

// Query to get products by category
$query = "SELECT p.product_name, p.description, p.price, p.image_path, p.rating 
          FROM products p
          JOIN categories c ON p.category_id = c.id
          WHERE c.category_name = ?";

$stmt = $connection->prepare($query);
$stmt->bind_param('s', $category_name);
$stmt->execute();
$result = $stmt->get_result();

// Display products
while ($row = $result->fetch_assoc()) {
    echo "<div class='product'>";
    echo "<img src='" . $row['image_path'] . "' alt='" . $row['product_name'] . "'>";
    echo "<h2>" . $row['product_name'] . "</h2>";
    echo "<p>" . $row['description'] . "</p>";
    echo "<p>Price: ₹" . $row['price'] . "</p>";
    echo "<p>Rating: " . $row['rating'] . "/5</p>";
    echo "</div>";
}
?>
