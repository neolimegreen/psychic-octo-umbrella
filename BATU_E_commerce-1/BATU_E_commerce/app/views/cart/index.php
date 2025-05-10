<?php
/**
 * Shopping Cart page
 * 
 * Displays cart contents and checkout options
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <h1 class="mb-4">Shopping Cart</h1>

    <?php if (!empty($cart)): ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Cart Items (<?= count($cart) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cart as $item): ?>
                            <div class="row mb-4 pb-3 border-bottom">
                                <!-- Product Image -->
                                <div class="col-md-2">
                                    <?php if ($item['image_url']): ?>
                                        <img src="/BATU_E_commerce/public/images/<?= htmlspecialchars($item['image_url']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($item['name']) ?>">
                                    <?php else: ?>
                                        <img src="/BATU_E_commerce/public/images/no-image.jpg" class="img-fluid rounded" alt="No image available">
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Product Details -->
                                <div class="col-md-4">
                                    <h5><a href="/BATU_E_commerce/products/<?= $item['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($item['name']) ?></a></h5>
                                    <p class="text-muted">Unit Price: $<?= number_format($item['price'], 2) ?></p>
                                </div>
                                
                                <!-- Quantity Controls -->
                                <div class="col-md-3">
                                    <form action="/BATU_E_commerce/cart/update/<?= $item['id'] ?>" method="post" class="d-flex align-items-center">
                                        <button type="submit" name="action" value="decrease" class="btn btn-sm btn-outline-secondary">-</button>
                                        <input type="text" name="quantity" value="<?= $item['quantity'] ?>" class="form-control form-control-sm mx-2 text-center" style="width: 50px;" readonly>
                                        <button type="submit" name="action" value="increase" class="btn btn-sm btn-outline-secondary">+</button>
                                    </form>
                                </div>
                                
                                <!-- Subtotal and Remove -->
                                <div class="col-md-3 text-end">
                                    <p class="fw-bold">$<?= number_format($item['subtotal'], 2) ?></p>
                                    <form action="/BATU_E_commerce/cart/remove/<?= $item['id'] ?>" method="post">
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Remove</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Continue Shopping -->
                <div class="d-flex justify-content-between">
                    <a href="/BATU_E_commerce/products" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
                    <form action="/BATU_E_commerce/cart/clear" method="post">
                        <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Clear Cart</button>
                    </form>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span>$<?= number_format($total, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping:</span>
                            <span>$<?= number_format($shipping = ($total > 0 ? 10 : 0), 2) ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3 fw-bold">
                            <span>Total:</span>
                            <span>$<?= number_format($total + $shipping, 2) ?></span>
                        </div>
                        
                        <!-- Checkout Button -->
                        <?php if (\App\Core\Auth::getInstance()->isLoggedIn()): ?>
                            <a href="/BATU_E_commerce/checkout" class="btn btn-success w-100"><i class="fas fa-lock"></i> Proceed to Checkout</a>
                        <?php else: ?>
                            <div class="alert alert-info mb-3">
                                <p class="mb-0">Please <a href="/BATU_E_commerce/login">login</a> to checkout.</p>
                            </div>
                            <a href="/BATU_E_commerce/login" class="btn btn-primary w-100">Login to Checkout</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <p>Your shopping cart is empty.</p>
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