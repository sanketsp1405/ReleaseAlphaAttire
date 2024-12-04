<?php
include 'db.php'; 

$query = $conn->query("SELECT sub_category.id , sub_category.name ,main_category.name FROM sub_category
JOIN main_category ON sub_category.main_category_id = main_category.id;");
$categories = $query->fetch_all(MYSQLI_ASSOC); 
?>
