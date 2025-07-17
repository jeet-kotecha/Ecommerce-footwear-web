<?php
session_start();
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header('Location: cart.php');
    exit;
}

if (isset($_POST['place_order'])) {
    // Process the order
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $payment_method = $_POST['payment_method'];
    
    // Calculate total
    $total = 0;
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $total += $product['price'] * $quantity;
    }
    
    // Insert order into database
    $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, customer_address, customer_city, customer_state, customer_zip, payment_method, total_amount, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $address, $city, $state, $zip, $payment_method, $total]);
    $order_id = $pdo->lastInsertId();
    
    // Insert order items
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $id, $quantity, $product['price']]);
    }
    
    // Clear cart and redirect to confirmation
    unset($_SESSION['cart']);
    header('Location: order_confirmation.php?id=' . $order_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/header.php'; ?>
    <title>Checkout - Shoe Store</title>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <main class="container">
        <h1>Checkout</h1>
        
        <div class="checkout-container">
            <section class="shipping-info">
                <h2>Shipping Information</h2>
                <form method="post" action="checkout.php">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                    <div class="form-group-row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="state">State</label>
                            <input type="text" id="state" name="state" required>
                        </div>
                        <div class="form-group">
                            <label for="zip">ZIP Code</label>
                            <input type="text" id="zip" name="zip" required>
                        </div>
                    </div>
                    
                    <h2>Payment Method</h2>
                    <div class="form-group">
                        <div class="payment-option">
                            <input type="radio" id="credit" name="payment_method" value="Credit Card" checked>
                            <label for="credit">Credit Card</label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="paypal" name="payment_method" value="PayPal">
                            <label for="paypal">PayPal</label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="cod" name="payment_method" value="Cash on Delivery">
                            <label for="cod">Cash on Delivery</label>
                        </div>
                    </div>
                    
                    <div class="payment-details" id="credit-card-details">
                        <div class="form-group">
                            <label for="card-number">Card Number</label>
                            <input type="text" id="card-number" name="card-number">
                        </div>
                        <div class="form-group-row">
                            <div class="form-group">
                                <label for="expiry">Expiry Date</label>
                                <input type="text" id="expiry" name="expiry" placeholder="MM/YY">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="place_order" class="btn btn-primary">Place Order</button>
                </form>
            </section>
            
            <section class="order-summary">
                <h2>Order Summary</h2>
                <div class="summary-items">
                    <?php
                    $total = 0;
                    foreach ($_SESSION['cart'] as $id => $quantity):
                        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                        $stmt->execute([$id]);
                        $product = $stmt->fetch(PDO::FETCH_ASSOC);
                        $subtotal = $product['price'] * $quantity;
                        $total += $subtotal;
                    ?>
                    <div class="summary-item">
                        <div class="item-image">
                            <img src="assets/images/products/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" width="60">
                        </div>
                        <div class="item-details">
                            <p><?= $product['name'] ?></p>
                            <p><?= $quantity ?> × $<?= number_format($product['price'], 2) ?></p>
                        </div>
                        <div class="item-price">
                            $<?= number_format($subtotal, 2) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="summary-totals">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>
                    <div class="total-row">
                        <span>Shipping</span>
                        <span>$5.00</span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Total</span>
                        <span>$<?= number_format($total + 5, 2) ?></span>
                    