<?php
/**
 * Admin Reviews Management
 * 
 * Displays all product reviews with options to approve, edit, or delete
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <h1 class="mb-4">Manage Reviews</h1>
    
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash']['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash']['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash']['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash']['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Filter Options -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="/BATU_E_commerce/admin/reviews" method="get" class="row g-3">
                <!-- Product Filter -->
                <div class="col-md-4">
                    <label for="product_id" class="form-label">Filter by Product</label>
                    <select class="form-select" id="product_id" name="product_id" onchange="this.form.submit()">
                        <option value="" <?= !isset($productId) || $productId === '' ? 'selected' : '' ?>>All Products</option>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['id'] ?>" <?= isset($productId) && $productId == $product['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($product['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <!-- Rating Filter -->
                <div class="col-md-4">
                    <label for="rating" class="form-label">Filter by Rating</label>
                    <select class="form-select" id="rating" name="rating" onchange="this.form.submit()">
                        <option value="" <?= !isset($rating) || $rating === '' ? 'selected' : '' ?>>All Ratings</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= isset($rating) && $rating == $i ? 'selected' : '' ?>>
                                <?= $i ?> Star<?= $i > 1 ? 's' : '' ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                    <a href="/BATU_E_commerce/admin/reviews" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Reviews Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($reviews)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>User</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td><?= $review['id'] ?></td>
                                    <td>
                                        <a href="/BATU_E_commerce/products/<?= $review['product_id'] ?>" target="_blank">
                                            <?= htmlspecialchars($review['product_name'] ?? 'Unknown Product') ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($review['user_name'] ?? 'Unknown User') ?></td>
                                    <td>
                                        <div class="text-warning">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $review['rating']): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (strlen($review['comment']) > 50): ?>
                                            <?= htmlspecialchars(substr($review['comment'], 0, 50)) ?>...
                                            <button type="button" class="btn btn-sm btn-link p-0" data-bs-toggle="modal" data-bs-target="#reviewModal<?= $review['id'] ?>">
                                                Read More
                                            </button>
                                            
                                            <!-- Review Modal -->
                                            <div class="modal fade" id="reviewModal<?= $review['id'] ?>" tabindex="-1" aria-labelledby="reviewModalLabel<?= $review['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="reviewModalLabel<?= $review['id'] ?>">Review for <?= htmlspecialchars($review['product_name'] ?? 'Unknown Product') ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <?= htmlspecialchars($review['comment']) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($review['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <form action="/BATU_E_commerce/admin/reviews/delete/<?= $review['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <p>No reviews found matching your criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
require_once dirname(__DIR__) . '/layouts/main.php';
?>