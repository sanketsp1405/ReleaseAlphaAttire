<?php
// Include the database connection file
include 'db.php';

// SQL query to select all users
$sql = "SELECT * FROM users";

// Execute the query
$result = $conn->query($sql);

// Get the total count of users
$totalUsers = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS file -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4; /* Light gray background for a clean look */
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #343a40; /* Darker color for the header */
            font-size: 2.5em; /* Increased font size */
        }

        .user-count {
            text-align: center; /* Centered total count */
            font-size: 1.5em; /* Size for the user count */
            color: #007bff; /* Blue color for the count */
            margin-bottom: 20px; /* Spacing below the count */
        }

        table {
            width: 100%; /* Full width table */
            border-collapse: collapse; /* Remove spacing between cells */
            margin-top: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Shadow effect for the table */
            border-radius: 12px; /* Rounded corners for the table */
            overflow: hidden; /* Prevent overflow of rounded corners */
        }

        th, td {
            padding: 15px; /* Padding for cells */
            text-align: left; /* Align text to the left */
            font-size: 18px; /* Font size for table text */
            color: #343a40; /* Dark text color */
        }

        th {
            background-color: #007bff; /* Blue background for headers */
            color: #ffffff; /* White text color for headers */
        }

        tr:nth-child(even) {
            background-color: #f8f9fa; /* Light gray background for even rows */
        }

        tr:hover {
            background-color: #e2e6ea; /* Darker gray on hover */
        }

        /* Centering the no users found message */
        .no-users {
            text-align: center;
            font-size: 20px; /* Increased font size for emphasis */
            color: #6c757d; /* Gray color for the message */
        }

        @media (max-width: 768px) {
            th, td {
                font-size: 16px; /* Smaller font size for mobile */
            }

            h1 {
                font-size: 2em; /* Slightly smaller header on mobile */
            }
        }
    </style>
</head>
<body>

<h1>User List</h1>

<div class="user-count">
    Total Users: <?php echo $totalUsers; ?>
</div>

<?php
// Check if there are any results
if ($totalUsers > 0) {
    // Output data of each row
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Phone Number</th></tr>"; // Table headers
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='no-users'>No users found.</p>";
}

// Close the database connection
$conn->close();
?>

</body>
</html>
