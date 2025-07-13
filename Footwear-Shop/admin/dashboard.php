<?php
session_start();
include('../includes/db_connect.php');

// Check if admin is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Optional: Check if user is an admin (if your users table has a 'role' field)
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
    <h2>👑 Admin Dashboard</h2>
    <hr><br>

    <div style="display: flex; flex-wrap: wrap; gap: 20px;">

        <div class="dashboard-card">
            <h3>📦 Manage Products</h3>
            <p>Add, edit, or delete products.</p>
            <a href="manage_products.php">Go to Products</a>
        </div>

        <div class="dashboard-card">
            <h3>👥 Manage Users</h3>
            <p>View, edit, or delete user accounts.</p>
            <a href="manage_users.php">Go to Users</a>
        </div>

        <div class="dashboard-card">
            <h3>📝 Manage Orders</h3>
            <p>View and process customer orders.</p>
            <a href="manage_orders.php">Go to Orders</a>
        </div>

        <div class="dashboard-card">
            <h3>📊 Site Stats (Optional)</h3>
            <p>Track total products, users, and orders.</p>
        </div>

    </div>
</div>

<?php include('../includes/footer.php'); ?>

<!-- Inline CSS for Dashboard cards (or move to your styles.css) -->
<style>
.dashboard-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    width: 250px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}
.dashboard-card:hover {
    transform: translateY(-5px);
}
.dashboard-card h3 {
    margin-bottom: 10px;
}
.dashboard-card p {
    color: #555;
}
.dashboard-card a {
    display: inline-block;
    margin-top: 10px;
    background: #ff6600;
    color: #fff;
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 4px;
}
.dashboard-card a:hover {
    background: #e65c00;
}
</style>
