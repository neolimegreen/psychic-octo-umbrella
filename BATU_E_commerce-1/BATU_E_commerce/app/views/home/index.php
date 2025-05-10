<?php
/**
 * Home page
 * 
 * Displays featured products and welcome message
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <!-- Hero Section -->
    <div class="row py-5">
        <div class="col-md-6 d-flex flex-column justify-content-center">
            <h1 class="display-4 fw-bold">Welcome to BATU E-Commerce</h1>
            <p class="lead">Your one-stop shop for all your needs. Browse our wide selection of products at competitive prices.</p>
            <div class="mt-4">
                <a href="/BATU_E_commerce/products" class="btn btn-primary btn-lg">Shop Now</a>
            </div>
        </div>
        <div class="col-md-6">
            <img src="/BATU_E_commerce/public/images/hero-image.jpg" alt="Shopping" class="img-fluid rounded shadow-sm object-fit-contain" onerror="this.src='/BATU_E_commerce/public/images/no-image.jpg'">
        </div>
    </div>
    
    <!-- Featured Products Section -->
    <div class="my-5">
        <h2 class="text-center mb-4">Featured Products</h2>
        
        <?php if (!empty($featuredProducts)): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col">
                        <div class="card h-100 product-card position-relative">
                            <!-- Wishlist Button -->
                            <?php include ROOT_DIR . '/app/views/components/wishlist_button.php'; ?>
                            
                            <!-- Product Image -->
                            <?php if ($product['image_url']): ?>
                                <img src="/BATU_E_commerce/public/images/<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top object-fit-contain" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='/BATU_E_commerce/public/img/no-image.jpg'">


                            <?php else: ?>
                                <img src="/BATU_E_commerce/public/img/no-image.jpg" class="card-img-top" alt="No image available">
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <!-- Product Name -->
                                <h5 class="card-title">
                                    <a href="/BATU_E_commerce/products/<?= $product['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </a>
                                </h5>
                                
                                <!-- Product Price -->
                                <p class="card-text product-price">$<?= number_format($product['price'], 2) ?></p>
                                
                                <!-- Product Description (truncated) -->
                                <p class="card-text">
                                    <?= htmlspecialchars(substr($product['description'], 0, 100)) ?>
                                    <?= strlen($product['description']) > 100 ? '...' : '' ?>
                                </p>
                            </div>
                            
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="/BATU_E_commerce/products/<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                    
                                    <?php if ($product['stock'] > 0): ?>
                                        <form action="/BATU_E_commerce/cart/add/<?= $product['id'] ?>" method="post" class="d-inline">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-shopping-cart"></i> Add to Cart
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled>Out of Stock</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="/BATU_E_commerce/products" class="btn btn-outline-primary">View All Products</a>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p>No featured products available at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Categories Section -->
    <div class="my-5">
        <h2 class="text-center mb-4">Shop by Category</h2>
        
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-mobile-alt fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Electronics</h5>
                        <p class="card-text">Discover the latest gadgets and electronics.</p>
                        <a href="/BATU_E_commerce/products" class="btn btn-outline-primary">Shop Electronics</a>
                    </div>
                </div>
            </div>
            
            <div class="col">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-tshirt fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Fashion</h5>
                        <p class="card-text">Stay stylish with our fashion collection.</p>
                        <a href="/BATU_E_commerce/products" class="btn btn-outline-primary">Shop Fashion</a>
                    </div>
                </div>
            </div>
            
            <div class="col">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-home fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Home & Living</h5>
                        <p class="card-text">Enhance your living space with our home products.</p>
                        <a href="/BATU_E_commerce/products" class="btn btn-outline-primary">Shop Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
require_once dirname(__DIR__) . '/layouts/main.php';
?>