<?php
/**
 * Admin Product Form
 * 
 * Form for adding or editing products
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/BATU_E_commerce/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/BATU_E_commerce/admin/products">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= isset($product) ? 'Edit Product' : 'Add Product' ?></li>
        </ol>
    </nav>
    
    <h1 class="mb-4"><?= isset($product) ? 'Edit Product' : 'Add New Product' ?></h1>
    
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
    
    <div class="card">
        <div class="card-body">
            <form action="<?= isset($product) ? '/BATU_E_commerce/admin/products/update/' . $product['id'] : '/BATU_E_commerce/admin/products/store' ?>" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Product Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= isset($product) ? htmlspecialchars($product['name']) : '' ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['name'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Product Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" id="description" name="description" rows="5" required><?= isset($product) ? htmlspecialchars($product['description']) : '' ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['description'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Product Price -->
                        <div class="mb-3">
                            <label for="price" class="form-label">Price ($)</label>
                            <input type="number" class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" id="price" name="price" step="0.01" min="0" value="<?= isset($product) ? $product['price'] : '' ?>" required>
                            <?php if (isset($errors['price'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['price'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Product Stock -->
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control <?= isset($errors['stock']) ? 'is-invalid' : '' ?>" id="stock" name="stock" min="0" value="<?= isset($product) ? $product['stock'] : '' ?>" required>
                            <?php if (isset($errors['stock'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['stock'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Product Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>" id="image" name="image" accept="image/*">
                            <?php if (isset($errors['image'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['image'] ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($product) && $product['image_url']): ?>
                                <div class="mt-2">
                                    <p>Current Image:</p>
                                    <img src="/BATU_E_commerce/public/uploads/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-thumbnail" style="max-width: 200px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                        <label class="form-check-label" for="remove_image">
                                            Remove current image
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="/BATU_E_commerce/admin/products" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?= isset($product) ? 'Update Product' : 'Add Product' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
require_once dirname(__DIR__) . '/layouts/main.php';
?>