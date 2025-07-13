<?php
session_start();
include('../includes/db_connect.php');

// Check admin login
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Check admin role
$email = $_SESSION['email'];
$query = "SELECT role FROM users WHERE email='$email'";
$result = $conn->query($query);
$row = $result->fetch_assoc();

if ($row['role'] !== 'admin') {
    echo "Access denied. Admins only.";
    exit();
}

include('../includes/header.php');
?>

<div style="padding: 30px;">
    <h2>👥 Manage Users</h2>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; background: #fff;">
        <tr style="background: #f0f0f0;">
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>

        <?php
        $sql = "SELECT * FROM users ORDER BY id DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($user = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$user['id']}</td>
                    <td>{$user['name']}</td>
                    <td>{$user['email']}</td>
                    <td>{$user['phone']}</td>
                    <td>{$user['address']}</td>
                    <td>{$user['role']}</td>
                    <td>
                        <a href='edit_user.php?id={$user['id']}' style='color:blue;'>Edit</a> | 
                        <a href='delete_user.php?id={$user['id']}' style='color:red;' onclick='return confirmDelete()'>Delete</a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No users found.</td></tr>";
        }
        ?>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
