<?php
/**
 * Orders page
 * 
 * Displays user's order history
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <h1 class="mb-4">My Orders</h1>
    
    <?php if (!empty($orders)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Order #</th>
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
                                <a href="/BATU_E_commerce/orders/<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <p>You haven't placed any orders yet.</p>
            <a href="/BATU_E_commerce/products" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
require_once dirname(__DIR__) . '/layouts/main.php';
?>