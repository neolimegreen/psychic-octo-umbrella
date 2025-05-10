<?php
/**
 * Wishlist page
 * 
 * Displays user's wishlist items
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <h1 class="mb-4">My Wishlist</h1>
    
    <?php if (!empty($wishlistItems)): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php foreach ($wishlistItems as $item): ?>
                <div class="col">
                    <div class="card h-100 product-card position-relative">
                        <!-- Wishlist Button -->
                        <?php $product = $item; // Set $product variable for wishlist_button component ?>
                        <?php include ROOT_DIR . '/app/views/components/wishlist_button.php'; ?>
                        
                        <!-- Product Image -->
                        <?php if ($item['image_url']): ?>
                            <img src="/BATU_E_commerce/public/uploads/<?= htmlspecialchars($item['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>">
                        <?php else: ?>
                            <img src="/BATU_E_commerce/public/img/no-image.jpg" class="card-img-top" alt="No image available">
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <!-- Product Name -->
                            <h5 class="card-title">
                                <a href="/BATU_E_commerce/products/<?= $item['product_id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($item['name']) ?>
                                </a>
                            </h5>
                            
                            <!-- Product Price -->
                            <p class="card-text product-price">$<?= number_format($item['price'], 2) ?></p>
                            
                            <!-- Product Description (truncated) -->
                            <p class="card-text">
                                <?= htmlspecialchars(substr($item['description'], 0, 100)) ?>
                                <?= strlen($item['description']) > 100 ? '...' : '' ?>
                            </p>
                        </div>
                        
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex justify-content-between">
                                <a href="/BATU_E_commerce/products/<?= $item['product_id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                
                                <div>
                                    <?php if ($item['stock'] > 0): ?>
                                        <form action="/BATU_E_commerce/cart/add/<?= $item['product_id'] ?>" method="post" class="d-inline">
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
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <p>Your wishlist is empty.</p>
            <a href="/BATU_E_commerce/products" class="btn btn-primary">Browse Products</a>
        </div>
    <?php endif; ?>
</div>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
require_once dirname(__DIR__) . '/layouts/main.php';
?>