<?php
/**
 * Admin Order Detail
 * 
 * Displays detailed information about a specific order with options to update status
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/BATU_E_commerce/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/BATU_E_commerce/admin/orders">Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Order #<?= $order['id'] ?></li>
        </ol>
    </nav>
    
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
    
    <div class="row">
        <div class="col-md-8">
            <!-- Order Details -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order #<?= $order['id'] ?> Details</h5>
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
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Order Date:</strong> <?= date('F d, Y', strtotime($order['created_at'])) ?></p>
                            <p><strong>Customer:</strong> <?= $user['name'] ?? 'Unknown' ?></p>
                            <p><strong>Email:</strong> <?= $user['email'] ?? 'Unknown' ?></p>
                        </div>
                    </div>
                    
                    <h6 class="border-bottom pb-2 mb-3">Order Items</h6>
                    
                    <?php if (!empty($orderItems)): ?>
                        <?php foreach ($orderItems as $item): ?>
                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-md-2">
                                    <?php if ($item['image_url']): ?>
                                        <img src="/BATU_E_commerce/public/uploads/<?= htmlspecialchars($item['image_url']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($item['name']) ?>">
                                    <?php else: ?>
                                        <img src="/BATU_E_commerce/public/img/no-image.jpg" class="img-fluid rounded" alt="No image available">
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
        </div>
        
        <div class="col-md-4">
            <!-- Order Summary -->
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
            
            <!-- Order Status Update -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Update Order Status</h5>
                </div>
                <div class="card-body">
                    <form action="/BATU_E_commerce/admin/orders/update-status/<?= $order['id'] ?>" method="post">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="/BATU_E_commerce/admin/orders" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>
</div>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
require_once dirname(__DIR__) . '/layouts/main.php';
?>