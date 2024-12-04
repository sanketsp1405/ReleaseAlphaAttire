<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Admin Dashboard</title>
</head>
<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    .dashboard {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
    }

    .card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 35%;
        /* Width of the card */
        height: 300px;
        /* Fixed height for uniformity */
        text-align: center;
        padding: 20px;
        /* Padding around the content */
        transition: transform 0.2s;
        position: relative;
        overflow: hidden;
    }

    .card:hover {
        transform: scale(1.05);
    }

    .card-icon {
        margin-bottom: 15px;
        display: flex;
        justify-content: center;
        /* Center align the image */
    }

    .card-icon img {
        width: 100%;
        /* Make the image take the full width of the card */
        height: auto;
        /* Maintain aspect ratio */
        max-height: 200px;
        /* Set a maximum height for the image */
        object-fit: contain;
        /* Ensure the image scales nicely within the bounds */
    }

    .card-content h2 {
        margin: 10px 0 5px;
        /* Add some margin for spacing */
        font-size: 28px;
        /* Increase heading font size */
        color: #333;
    }

    .card-content p {
        font-size: 22px;
        /* Increase paragraph font size */
        color: #666;
    }

    /* Add background graphic to cards */
    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('path/to/your-background-graphic.png');
        /* Replace with your graphic path */
        background-size: cover;
        background-position: center;
        opacity: 0.1;
        /* Adjust opacity for visibility */
        z-index: 0;
    }

    .card-content {
        position: relative;
        /* Ensure content appears above background */
        z-index: 1;
    }
</style>

<body>
    <h1>Admin Dashboard</h1>
    <div class="dashboard">


        <div class="card">

            <div class="card-icon">
                <img src="images/inventory_control.jpg" alt="Inventory Icon">
            </div>

            <div class="card-content">
                <a href="inventory.php">
                    <h2>Inventory</h2>
                </a>
            </div>

        </div>

        <div class="card">
            <div class="card-icon">
                <img src="images/total_product.jpg" alt="Orders Icon">
            </div>
            <div class="card-content">
              <a href="orderDetails.php">  <h2>Total Orders</h2></a>

            </div>
        </div>
        <div class="card">
            <div class="card-icon">
                <img src="images/total_users.jpg" alt="Users Icon">
            </div>
            <div class="card-content">
            <a href="total_user.php">    <h2>Total Users</h2>

            </div>
        </div>
    </div>
</body>

</html>