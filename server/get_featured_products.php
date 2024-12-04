<?php

include('connection.php');
$stmt=$conn->prepare("select*from products ");
$stmt->execute();
$featured_products=$stmt->get_result();
?>