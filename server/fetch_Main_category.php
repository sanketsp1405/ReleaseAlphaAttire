<?php
include 'db.php'; // Include your database connection

// Fetch all main categories with their associated subcategories
$query = $conn->query("
    SELECT main_category.name AS main_name, sub_category.name AS sub_name 
    FROM main_category
    LEFT JOIN sub_category ON sub_category.main_category_id = main_category.id
    ORDER BY main_category.name, sub_category.name;
");

// Initialize the $categories array
$categories = [];

if ($query && $query->num_rows > 0) {
    // Populate the array by grouping subcategories under their respective main categories
    while ($row = $query->fetch_assoc()) {
        $categories[$row['main_name']][] = $row['sub_name'];
    }
} 

?>