<?php
$page_title = "Product Management";
require_once 'includes/admin_header.php';

// Handle product actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    // Add new product
    if ($action == 'add') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate and process form data
            $errors = validateProductData($_POST);
            
            if (empty($errors)) {
                // Handle image upload
                $imageName = '';
                if (!empty($_FILES['image']['name'])) {
                    $uploadResult = uploadProductImage($_FILES['image']);
                    if ($uploadResult === true) {
                        $imageName = basename($_FILES['image']['name']);
                    } else {
                        $errors[] = $uploadResult;
                    }
                }
                
                if (empty($errors)) {
                    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, image, stock, featured) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['name'],
                        $_POST['description'],
                        $_POST['price'],
                        $_POST['category_id'],
                        $imageName,
                        $_POST['stock'],
                        isset($_POST['featured']) ? 1 : 0
                    ]);
                    
                    $_SESSION['success_message'] = "Product added successfully!";
                    header('Location: products.php');
                    exit;
                }
            }
        }
        
        // Show add product form
        $categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="admin-form">
            <h2>Add New Product</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <!-- Product form fields -->
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" required>
                </div>
                <!-- More form fields... -->
                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
        </div>
        <?php
        require_once 'includes/admin_footer.php';
        exit;
    }
}

// Default view - product list
$products = $pdo->query("SELECT p.*, c.name as category_name 
                        FROM products p 
                        JOIN categories c ON p.category_id = c.id
                        ORDER BY p.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Product List -->
<div class="admin-table-container">
    <div class="table-header">
        <h2>Product List</h2>
        <a href="products.php?action=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Featured</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?= $product['id'] ?></td>
                <td>
                    <img src="../../assets/images/products/<?= $product['image'] ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" width="50">
                </td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= $product['category_name'] ?></td>
                <td>$<?= number_format($product['price'], 2) ?></td>
                <td><?= $product['stock'] ?></td>
                <td>
                    <?php if ($product['featured']): ?>
                        <span class="badge badge-success">Yes</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">No</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="products.php?action=edit&id=<?= $product['id'] ?>" class="btn-edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="products.php?action=delete&id=<?= $product['id'] ?>" 
                       class="btn-delete" onclick="return confirm('Are you sure?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once 'includes/admin_footer.php';
?>
