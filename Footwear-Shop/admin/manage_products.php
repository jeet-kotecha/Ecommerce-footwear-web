<?php
session_start();
include('../includes/db_connect.php');

// Check if admin is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Check if user is admin
$email = $_SESSION['email'];
$query = "SELECT role FROM users WHERE email='$email'";
$result = $conn->query($query);
$row = $result->fetch_assoc();

if ($row['role'] != 'admin') {
    echo "Access Denied. Admins only.";
    exit();
}

include('../includes/header.php');
?>

<div style="padding: 30px;">
    <h2>📦 Manage Products</h2>
    <a href="add_product.php" style="background: #28a745; color: #fff; padding: 10px 15px; border-radius: 5px; text-decoration: none;">+ Add Product</a>
    <br><br>

    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; background:#fff;">
        <tr style="background:#f0f0f0;">
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price (₹)</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>

        <?php
        $sql = "SELECT * FROM products ORDER BY id DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($product = $result->fetch_assoc()) {
                echo "<tr>
                    <td>".$product['id']."</td>
                    <td><img src='../uploads/product-images/".$product['image']."' width='60'></td>
                    <td>".$product['name']."</td>
                    <td>".$product['category']."</td>
                    <td>₹".$product['price']."</td>
                    <td>".$product['stock']."</td>
                    <td>
                        <a href='edit_product.php?id=".$product['id']."' style='color:blue;'>Edit</a> | 
                        <a href='delete_product.php?id=".$product['id']."' style='color:red;' onclick='return confirmDelete()'>Delete</a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No products found.</td></tr>";
        }
        ?>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
