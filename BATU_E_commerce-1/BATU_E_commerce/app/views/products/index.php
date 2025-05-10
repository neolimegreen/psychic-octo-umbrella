<?php
/**
 * Products listing page
 * 
 * Displays all products with filtering and pagination
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <h1 class="mb-4">Products</h1>
    
    <!-- Search and Filter Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="/BATU_E_commerce/products" method="get" class="row g-3">
                <!-- Search Field -->
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search products..." name="search" value="<?= isset($search) ? htmlspecialchars($search) : '' ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                
                <!-- Sort Options -->
                <div class="col-md-3">
                    <select class="form-select" name="sort" onchange="this.form.submit()">
                        <option value="newest" <?= (isset($sort) && $sort === 'newest') ? 'selected' : '' ?>>Newest First</option>
                        <option value="price_low" <?= (isset($sort) && $sort === 'price_low') ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_high" <?= (isset($sort) && $sort === 'price_high') ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="name_asc" <?= (isset($sort) && $sort === 'name_asc') ? 'selected' : '' ?>>Name: A to Z</option>
                        <option value="name_desc" <?= (isset($sort) && $sort === 'name_desc') ? 'selected' : '' ?>>Name: Z to A</option>
                    </select>
                </div>
                
                <!-- Items Per Page -->
                <div class="col-md-3">
                    <select class="form-select" name="limit" onchange="this.form.submit()">
                        <option value="12" <?= (isset($limit) && $limit == 12) ? 'selected' : '' ?>>12 per page</option>
                        <option value="24" <?= (isset($limit) && $limit == 24) ? 'selected' : '' ?>>24 per page</option>
                        <option value="48" <?= (isset($limit) && $limit == 48) ? 'selected' : '' ?>>48 per page</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Products Grid -->
    <?php if (!empty($products)): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php foreach ($products as $product): ?>
                <div class="col">
                    <div class="card h-100 product-card position-relative">
                        <!-- Wishlist Button -->
                        <?php include ROOT_DIR . '/app/views/components/wishlist_button.php'; ?>
                        
                        <!-- Product Image -->
                        <?php if ($product['image_url']): ?>
                            <img  src="/BATU_E_commerce/public/images/<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top object-fit-contain" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='/BATU_E_commerce/public/img/no-image.jpg'">
                        <?php else: ?>
                            <img src="/BATU_E_commerce/public/img/no-image.jpg" class="card-img-top object-fit-contain" alt="No image available">
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
        
        <!-- Pagination -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <nav aria-label="Product pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Previous Page Link -->
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="/BATU_E_commerce/products?page=<?= $currentPage - 1 ?><?= isset($search) ? '&search=' . urlencode($search) : '' ?><?= isset($sort) ? '&sort=' . $sort : '' ?><?= isset($limit) ? '&limit=' . $limit : '' ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <!-- Page Number Links -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="/BATU_E_commerce/products?page=<?= $i ?><?= isset($search) ? '&search=' . urlencode($search) : '' ?><?= isset($sort) ? '&sort=' . $sort : '' ?><?= isset($limit) ? '&limit=' . $limit : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <!-- Next Page Link -->
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="/BATU_E_commerce/products?page=<?= $currentPage + 1 ?><?= isset($search) ? '&search=' . urlencode($search) : '' ?><?= isset($sort) ? '&sort=' . $sort : '' ?><?= isset($limit) ? '&limit=' . $limit : '' ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info">
            <p>No products found. Please try a different search or check back later.</p>
        </div>
    <?php endif; ?>
</div>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
require_once dirname(__DIR__) . '/layouts/main.php';
?>