<?php
// Include database connection
include 'db.php';

// Handle adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_product') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $sub_category_id = $_POST['sub_category_id'];
    $brand = $_POST['brand']; // New field
    $size = $_POST['size'];   // New field
    $stock = $_POST['stock']; // New field

    // Handle file uploads
    $target_dir = "images/"; // Directory for images
    $image1 = $target_dir . basename($_FILES["image1"]["name"]);
    if (!move_uploaded_file($_FILES["image1"]["tmp_name"], $image1)) {
        echo "Error uploading Image 1.";
    }

    $image2 = $target_dir . basename($_FILES["image2"]["name"]);
    if (!empty($_FILES["image2"]["name"]) && !move_uploaded_file($_FILES["image2"]["tmp_name"], $image2)) {
        echo "Error uploading Image 2.";
        $image2 = NULL;
    }

    $image3 = $target_dir . basename($_FILES["image3"]["name"]);
    if (!empty($_FILES["image3"]["name"]) && !move_uploaded_file($_FILES["image3"]["tmp_name"], $image3)) {
        echo "Error uploading Image 3.";
        $image3 = NULL;
    }

    // Insert product into the database (no serial number in DB)
    $query = "INSERT INTO products (name, price, image1, image2, image3, sub_category_id, brand, size, stock)
              VALUES ('$name', '$price', '$image1', '$image2', '$image3', '$sub_category_id', '$brand', '$size', '$stock')";

    if (mysqli_query($conn, $query)) {
        header('Location: inventory.php');
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle deleting a product
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Delete the product from the database
    $query = "DELETE FROM products WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        header('Location: inventory.php');
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

// Fetch products from the database
$query = "SELECT p.id, p.name, p.price, p.image1, p.image2, p.image3, sc.name AS sub_category_name, p.brand, p.size, p.stock
          FROM products p
          JOIN sub_category sc ON p.sub_category_id = sc.id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="inventoy.css">
</head>

<body>

    <div class="container">
        <h1>Inventory Management</h1>

        <div>
            <button onclick="document.getElementById('addProductModal').style.display='block'">Add Product</button>
        </div>

        <div id="addProductModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('addProductModal').style.display='none'">&times;</span>
                <h2>Add New Product</h2>
                <form action="inventory.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_product">
                    <label for="name">Product Name:</label>
                    <input type="text" id="name" name="name" required>

                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" required>

                    <label for="brand">Brand:</label>
                    <input type="text" id="brand" name="brand" required> <!-- New Field -->

                    <label for="size">Size:</label>
                    <input type="text" id="size" name="size" required> <!-- New Field -->

                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" required>

                    <label for="image1">Image 1:</label>
                    <input type="file" id="image1" name="image1" accept="image/*" required>

                    <label for="image2">Image 2 (optional):</label>
                    <input type="file" id="image2" name="image2" accept="image/*">

                    <label for="image3">Image 3 (optional):</label>
                    <input type="file" id="image3" name="image3" accept="image/*">

                    <label for="sub_category_id">Subcategory:</label>
                    <select id="sub_category_id" name="sub_category_id" required>
                        <option value="">Select Subcategory</option>
                        <?php
                        // Fetch subcategories for the dropdown
                        $subcategories = mysqli_query($conn, "SELECT * FROM sub_category");
                        while ($sub = mysqli_fetch_assoc($subcategories)) {
                            echo "<option value='{$sub['id']}'>{$sub['name']}</option>";
                        }
                        ?>
                    </select>

                    <button type="submit">Add Product</button>
                </form>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Sr No.</th> <!-- Added Serial Number Column -->
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Brand</th> <!-- New Column -->
                    <th>Size</th>  <!-- New Column -->
                    <th>Stock</th> <!-- New Column -->
                    <th>Images</th>
                    <th>Subcategory</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sr_no = 1; // Start serial number from 1
                while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $sr_no++; ?></td> <!-- Display Serial Number -->
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo '' . number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['brand']; ?></td> <!-- Display Brand -->
                        <td><?php echo $row['size']; ?></td>  <!-- Display Size -->
                        <td><?php echo $row['stock']; ?></td> <!-- Display Stock -->
                        <td>
                            <img src="<?php echo $row['image1']; ?>" alt="Image 1" style="height: 50px;">
                            <?php if ($row['image2']): ?>
                                <img src="<?php echo $row['image2']; ?>" alt="Image 2" style="height: 50px;">
                            <?php endif; ?>
                            <?php if ($row['image3']): ?>
                                <img src="<?php echo $row['image3']; ?>" alt="Image 3" style="height: 50px;">
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['sub_category_name']; ?></td>
                        <td class="action-buttons">
                      
                            <a href="inventory.php?delete_id=<?php echo $row['id']; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('addProductModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>

</html>
