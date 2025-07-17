<?php
session_start();
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $id => $quantity) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $quantity;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/header.php'; ?>
    <title>Shopping Cart - Shoe Store</title>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <main class="container">
        <h1>Your Shopping Cart</h1>
        
        <?php if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0): ?>
            <p>Your cart is empty.</p>
            <a href="products/index.php" class="btn btn-primary">Continue Shopping</a>
        <?php else: ?>
            <form method="post" action="cart.php">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $id => $quantity):
                            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                            $stmt->execute([$id]);
                            $product = $stmt->fetch(PDO::FETCH_ASSOC);
                            $subtotal = $product['price'] * $quantity;
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td class="product-info">
                                <img src="assets/images/products/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" width="80">
                                <div>
                                    <h3><?= $product['name'] ?></h3>
                                    <p><?= $product['description'] ?></p>
                                </div>
                            </td>
                            <td>$<?= number_format($product['price'], 2) ?></td>
                            <td>
                                <input type="number" name="quantity[<?= $id ?>]" value="<?= $quantity ?>" min="1">
                            </td>
                            <td>$<?= number_format($subtotal, 2) ?></td>
                            <td>
                                <a href="includes/remove_from_cart.php?id=<?= $id ?>" class="btn btn-danger">×</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"></td>
                            <td><strong>Total: $<?= number_format($total, 2) ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                
                <div class="cart-actions">
                    <button type="submit" name="update_cart" class="btn btn-secondary">Update Cart</button>
                    <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                </div>
            </form>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
