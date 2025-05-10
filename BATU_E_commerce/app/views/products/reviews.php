<?php require_once ROOT_DIR . '/app/views/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/BATU_E_commerce/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/BATU_E_commerce/products">Products</a></li>
                    <li class="breadcrumb-item"><a href="/BATU_E_commerce/products/<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reviews</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="<?php echo !empty($product['image_url']) ? $product['image_url'] : '/BATU_E_commerce/public/img/no-image.jpg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <div class="mb-2">
                        <?php if ($averageRating): ?>
                            <div class="d-flex align-items-center">
                                <div class="ratings mr-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= round($averageRating)): ?>
                                            <i class="fa fa-star text-warning"></i>
                                        <?php else: ?>
                                            <i class="fa fa-star text-secondary"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <span class="ml-2"><?php echo $averageRating; ?> (<?php echo count($reviews); ?> reviews)</span>
                            </div>
                        <?php else: ?>
                            <div class="text-muted">No reviews yet</div>
                        <?php endif; ?>
                    </div>
                    <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="card-text"><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                    <p class="card-text"><strong>In Stock:</strong> <?php echo $product['stock']; ?></p>
                    <div class="d-flex justify-content-between">
                        <a href="/BATU_E_commerce/products/<?php echo $product['id']; ?>" class="btn btn-primary">View Product</a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="/BATU_E_commerce/cart/add/<?php echo $product['id']; ?>" class="btn btn-success">Add to Cart</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Reviews</h5>
                    <?php if (isset($_SESSION['user_id']) && !$userReview): ?>
                        <a href="/BATU_E_commerce/products/<?php echo $product['id']; ?>/reviews/add" class="btn btn-primary btn-sm">Write a Review</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($reviews)): ?>
                        <div class="alert alert-info">No reviews yet. Be the first to review this product!</div>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review mb-4 pb-4 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0"><?php echo htmlspecialchars($review['user_name']); ?></h6>
                                    <small class="text-muted"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                                </div>
                                <div class="ratings mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $review['rating']): ?>
                                            <i class="fa fa-star text-warning"></i>
                                        <?php else: ?>
                                            <i class="fa fa-star text-secondary"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
                                    <div class="mt-2">
                                        <a href="/BATU_E_commerce/products/<?php echo $product['id']; ?>/reviews/edit/<?php echo $review['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="/BATU_E_commerce/products/<?php echo $product['id']; ?>/reviews/delete/<?php echo $review['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this review?')">Delete</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (isset($_SESSION['user_id']) && $userReview): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Your Review</h5>
                    </div>
                    <div class="card-body">
                        <div class="review">
                            <div class="ratings mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $userReview['rating']): ?>
                                        <i class="fa fa-star text-warning"></i>
                                    <?php else: ?>
                                        <i class="fa fa-star text-secondary"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($userReview['comment'])); ?></p>
                            <div class="mt-2">
                                <a href="/BATU_E_commerce/products/<?php echo $product['id']; ?>/reviews/edit/<?php echo $userReview['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="/BATU_E_commerce/products/<?php echo $product['id']; ?>/reviews/delete/<?php echo $userReview['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this review?')">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once ROOT_DIR . '/app/views/includes/footer.php'; ?>
