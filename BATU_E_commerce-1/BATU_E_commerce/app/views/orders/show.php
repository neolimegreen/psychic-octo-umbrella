<?php
/**
 * Order details page
 * 
 * Displays detailed information about a specific order
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/BATU_E_commerce/">Home</a></li>
            <li class="breadcrumb-item"><a href="/BATU_E_commerce/orders">My Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Order #<?= $order['id'] ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order #<?= $order['id'] ?> Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Order Date:</strong> <?= date('F d, Y', strtotime($order['created_at'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Status:</strong> 
                                <?php 
                                $statusClass = '';
                                switch ($order['status']) {
                                    case 'pending':
                                        $statusClass = 'bg-warning';
                                        break;
                                    case 'shipped':
                                        $statusClass = 'bg-info';
                                        break;
                                    case 'delivered':
                                        $statusClass = 'bg-success';
                                        break;
                                    case 'cancelled':
                                        $statusClass = 'bg-danger';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= ucfirst($order['status']) ?></span>
                            </p>
                        </div>
                    </div>
                    
                    <h6 class="border-bottom pb-2 mb-3">Order Items</h6>
                    
                    <?php if (!empty($orderItems)): ?>
                        <?php foreach ($orderItems as $item): ?>
                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-md-2">
                                    <?php if ($item['image_url']): ?>
                                        <img src="/BATU_E_commerce/public/images/<?= htmlspecialchars($item['image_url']) ?>" class="img-fluid rounded object-fit-contain" alt="<?= htmlspecialchars($item['name']) ?>">
                                    <?php else: ?>
                                        <img src="/BATU_E_commerce/public/images/no-image.jpg" class="img-fluid rounded object-fit-contain" alt="No image available">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <h6><a href="/BATU_E_commerce/products/<?= $item['product_id'] ?>" class="text-decoration-none"><?= htmlspecialchars($item['name']) ?></a></h6>
                                    <p class="text-muted">Unit Price: $<?= number_format($item['price_each'], 2) ?></p>
                                </div>
                                <div class="col-md-2 text-center">
                                    <p>Quantity: <?= $item['quantity'] ?></p>
                                </div>
                                <div class="col-md-2 text-end">
                                    <p class="fw-bold">$<?= number_format($item['price_each'] * $item['quantity'], 2) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <p>No items found for this order.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <a href="/BATU_E_commerce/orders" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <span>$<?= number_format($order['total_price'] - 10, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping:</span>
                        <span>$10.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3 fw-bold">
                        <span>Total:</span>
                        <span>$<?= number_format($order['total_price'], 2) ?></span>
                    </div>
                </div>
            </div>
            
            <?php if ($order['status'] === 'pending'): ?>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Need to cancel?</h6>
                        <p class="card-text">You can cancel this order as it hasn't been shipped yet.</p>
                        <form action="/BATU_E_commerce/orders/cancel/<?= $order['id'] ?>" method="post" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            <button type="submit" class="btn btn-danger w-100">Cancel Order</button>
                        </form>
                    </div>
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