
<?php

include('connection.php');
$stmt=$conn->prepare("select*from main_categories ");
$stmt->execute();
$main_category=$stmt->get_result();
?>