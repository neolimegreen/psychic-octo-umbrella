<?php
/**
 * Admin Dashboard
 * 
 * Main admin interface showing key statistics and recent orders
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <h1 class="mb-4">Admin Dashboard</h1>
    
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
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Products</h5>
                            <h2 class="display-4"><?= $productCount ?></h2>
                        </div>
                        <i class="fas fa-box fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="/BATU_E_commerce/admin/products" class="text-white text-decoration-none">View Details</a>
                    <i class="fas fa-angle-right"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Orders</h5>
                            <h2 class="display-4"><?= $orderCount ?></h2>
                        </div>
                        <i class="fas fa-shopping-cart fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="/BATU_E_commerce/admin/orders" class="text-white text-decoration-none">View Details</a>
                    <i class="fas fa-angle-right"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Users</h5>
                            <h2 class="display-4"><?= $userCount ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="/BATU_E_commerce/admin/users" class="text-white text-decoration-none">View Details</a>
                    <i class="fas fa-angle-right"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Recent Orders</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($recentOrders)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td><?= $order['id'] ?></td>
                                    <td><?= isset($order['user_name']) ? htmlspecialchars($order['user_name']) : 'Unknown' ?></td>
                                    <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                    <td>$<?= number_format($order['total_price'], 2) ?></td>
                                    <td>
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
                                    </td>
                                    <td>
                                        <a href="/BATU_E_commerce/admin/orders/view/<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <a href="/BATU_E_commerce/admin/orders" class="btn btn-primary">View All Orders</a>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <p>No recent orders found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Product Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/BATU_E_commerce/admin/products" class="btn btn-outline-primary">View All Products</a>
                        <a href="/BATU_E_commerce/admin/products/add" class="btn btn-outline-success">Add New Product</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">User Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/BATU_E_commerce/admin/users" class="btn btn-outline-primary">View All Users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
require_once dirname(__DIR__) . '/layouts/main.php';
?>