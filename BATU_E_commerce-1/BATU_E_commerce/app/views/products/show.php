<?php
/**
 * Product detail page
 * 
 * Displays detailed information about a single product
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <?php if (isset($product)): ?>
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/BATU_E_commerce/">Home</a></li>
                <li class="breadcrumb-item"><a href="/BATU_E_commerce/products">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <!-- Product Image -->
            <div class="col-md-5">
                <div class="card position-relative">
                    <!-- Wishlist Button -->
                    <?php include ROOT_DIR . '/app/views/components/wishlist_button.php'; ?>
                    
                    <?php if ($product['image_url']): ?>
                        <img src="/BATU_E_commerce/public/images/<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top img-fluid object-fit-contain" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php else: ?>
                        <img src="/BATU_E_commerce/public/images/no-image.jpg" class="card-img-top img-fluid" alt="No image available">
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Product Details -->
            <div class="col-md-7">
                <h1 class="mb-3"><?= htmlspecialchars($product['name']) ?></h1>
                
                <!-- Product Price -->
                <h3 class="text-primary mb-4">$<?= number_format($product['price'], 2) ?></h3>
                
                <!-- Stock Status -->
                <?php if ($product['stock'] > 0): ?>
                    <p class="badge bg-success mb-3">In Stock (<?= $product['stock'] ?> available)</p>
                <?php else: ?>
                    <p class="badge bg-danger mb-3">Out of Stock</p>
                <?php endif; ?>
                
                <!-- Product Description -->
                <div class="mb-4">
                    <h5>Description</h5>
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>
                
                <!-- Add to Cart Form -->
                <?php if ($product['stock'] > 0): ?>
                    <form action="/BATU_E_commerce/cart/add/<?= $product['id'] ?>" method="post" class="mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label for="quantity" class="col-form-label">Quantity:</label>
                            </div>
                            <div class="col-auto">
                                <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="<?= $product['stock'] ?>">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Reviews Section -->
        <div class="mt-5">
            <h3>Customer Reviews</h3>
            <hr>
            
            <?php if (isset($reviews) && !empty($reviews)): ?>
                <!-- Review Statistics -->
                <?php 
                $totalRating = 0;
                foreach ($reviews as $review) {
                    $totalRating += $review['rating'];
                }
                $averageRating = count($reviews) > 0 ? round($totalRating / count($reviews), 1) : 0;
                ?>
                
                <div class="mb-4">
                    <h4>Average Rating: <?= $averageRating ?> / 5</h4>
                    <div class="mb-3">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= $averageRating): ?>
                                <i class="fas fa-star text-warning"></i>
                            <?php elseif ($i - 0.5 <= $averageRating): ?>
                                <i class="fas fa-star-half-alt text-warning"></i>
                            <?php else: ?>
                                <i class="far fa-star text-warning"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <span class="ms-2">(<?= count($reviews) ?> reviews)</span>
                    </div>
                </div>
                
                <!-- Review List -->
                <?php foreach ($reviews as $review): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($review['user_name']) ?></h5>
                                <small class="text-muted"><?= date('M d, Y', strtotime($review['created_at'])) ?></small>
                            </div>
                            
                            <div class="mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="<?= $i <= $review['rating'] ? 'fas' : 'far' ?> fa-star text-warning"></i>
                                <?php endfor; ?>
                            </div>
                            
                            <p class="card-text"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <p>No reviews yet. Be the first to review this product!</p>
                </div>
            <?php endif; ?>
            
            <!-- Add Review Form -->
            <?php if (\App\Core\Auth::getInstance()->isLoggedIn()): ?>
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Write a Review</h5>
                    </div>
                    <div class="card-body">
                        <form action="/BATU_E_commerce/reviews/add/<?= $product['id'] ?>" method="post">
                            <!-- Rating Field -->
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating</label>
                                <select class="form-select" id="rating" name="rating" required>
                                    <option value="">Select rating</option>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Very Good</option>
                                    <option value="3">3 - Good</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>
                            
                            <!-- Comment Field -->
                            <div class="mb-3">
                                <label for="comment" class="form-label">Your Review</label>
                                <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-4">
                    <p>Please <a href="/BATU_E_commerce/login">login</a> to write a review.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <p>Product not found.</p>
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