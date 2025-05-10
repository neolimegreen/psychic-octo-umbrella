<?php
/**
 * Admin Orders Management
 * 
 * Displays all orders with options to view details and update status
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <h1 class="mb-4">Manage Orders</h1>
    
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
            <form action="/BATU_E_commerce/admin/orders" method="get" class="row g-3">
                <!-- Status Filter -->
                <div class="col-md-4">
                    <label for="status" class="form-label">Filter by Status</label>
                    <select class="form-select" id="status" name="status" onchange="this.form.submit()">
                        <option value="" <?= !isset($status) || $status === '' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="pending" <?= isset($status) && $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="shipped" <?= isset($status) && $status === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option value="delivered" <?= isset($status) && $status === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="cancelled" <?= isset($status) && $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                
                <!-- Date Range Filter -->
                <div class="col-md-4">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?= isset($dateFrom) ? $dateFrom : '' ?>">
                </div>
                
                <div class="col-md-4">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?= isset($dateTo) ? $dateTo : '' ?>">
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="/BATU_E_commerce/admin/orders" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Orders Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($orders)): ?>
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
                            <?php foreach ($orders as $order): ?>
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
            <?php else: ?>
                <div class="alert alert-info">
                    <p>No orders found matching your criteria.</p>
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