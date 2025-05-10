<?php
/**
 * Admin Products Management
 * 
 * Displays all products with options to add, edit, and delete
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Products</h1>
        <a href="/BATU_E_commerce/admin/products/add" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>
    
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
    
    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($products)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= $product['id'] ?></td>
                                    <td>
                                        <?php if ($product['image_url']): ?>
                                            <img src="/BATU_E_commerce/public/images/<?= htmlspecialchars($product['image_url']) ?>" 
                                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                                 class="img-thumbnail" style="max-width: 50px;">
                                        <?php else: ?>
                                            <img src="/BATU_E_commerce/public/images/no-image.jpg" 
                                                 alt="No image" 
                                                 class="img-thumbnail" style="max-width: 50px;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td>$<?= number_format($product['price'], 2) ?></td>
                                    <td>
                                        <?php if ($product['stock'] > 0): ?>
                                            <span class="badge bg-success"><?= $product['stock'] ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($product['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/BATU_E_commerce/products/<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/BATU_E_commerce/admin/products/edit/<?= $product['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="/BATU_E_commerce/admin/products/delete/<?= $product['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
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
                    <p>No products found. <a href="/BATU_E_commerce/admin/products/add">Add your first product</a>.</p>
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