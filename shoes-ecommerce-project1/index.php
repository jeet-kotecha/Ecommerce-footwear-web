    <?php include 'includes/header.php'; ?>
    <title>Home - Shoe Store</title>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <main class="container">
        <section class="hero">
            <div class="hero-content">
                <h1>Step Into Style</h1>
                <p>Discover the latest collections for every occasion</p>
                <a href="products/index.php" class="btn btn-primary">Shop Now</a>
            </div>
        </section>
        
        <section class="featured-products">
            <h2>Featured Products</h2>
            <div class="products-grid">
                <?php
                $stmt = $pdo->prepare("SELECT * FROM products WHERE featured = 1 LIMIT 4");
                $stmt->execute();
                while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="product-card">
                        <a href="products/view.php?id='.$product['id'].'">
                            <div class="product-image">
                                <img src="assets/images/products/'.$product['image'].'" alt="'.$product['name'].'">
                            </div>
                            <h3>'.$product['name'].'</h3>
                            <p class="price">$'.$product['price'].'</p>
                        </a>
                        <button class="btn btn-secondary add-to-cart" data-id="'.$product['id'].'">Add to Cart</button>
                    </div>';
                }
                ?>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>