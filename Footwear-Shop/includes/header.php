<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Footwear Shop</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../js/scripts.js"></script>
</head>
<body>

<header>
    <div class="logo">
        <a href="../index.php" style="color: #fff; text-decoration: none;">
            <h1>👟 FootwearShop</h1>
        </a>
    </div>

    <nav>
        <a href="../index.php">Home</a>
        <a href="../categories.php">Categories</a>
        <a href="../cart/view_cart.php">Cart 🛒</a>
        <?php
        if (isset($_SESSION['email'])) {
            echo '<a href="../user/profile.php">Profile</a>';
            echo '<a href="../auth/logout.php">Logout</a>';
        } else {
            echo '<a href="../auth/login.php">Login</a>';
        }
        ?>
    </nav>
</header>

<!-- Search Bar -->
<div style="background: #333; padding: 10px; text-align: center;">
    <input type="text" id="searchBox" onkeyup="showSuggestions()" placeholder="Search footwear..." style="padding: 10px; width: 300px; border-radius: 5px; border: none;">
    <div id="suggestions" style="background: #fff; color: #333; position: absolute; margin: 5px auto; width: 300px; z-index: 100;"></div>
</div>

