<?php
include 'db.php'; // Include your mysqli connection here

$query = $conn->query("SELECT * FROM featured_products");
$categories = $query->fetch_all(MYSQLI_ASSOC); // Fetch all results as an associative array
?>
