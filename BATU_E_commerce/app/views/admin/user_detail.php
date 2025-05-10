<?php
/**
 * Admin User Detail
 * 
 * Displays detailed information about a specific user
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/BATU_E_commerce/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/BATU_E_commerce/admin/users">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($user['name']) ?></li>
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
        <div class="col-md-4">
            <!-- User Profile Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">User Profile</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h4 class="text-center mb-3"><?= htmlspecialchars($user['name']) ?></h4>
                    <p class="text-center">
                        <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                            <?= ucfirst($user['role']) ?>
                        </span>
                    </p>
                    <hr>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Registered:</strong> <?= date('F d, Y', strtotime($user['created_at'])) ?></p>
                    <p><strong>Total Orders:</strong> <?= count($userOrders) ?></p>
                    
                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                        <div class="mt-4">
                            <?php if ($user['role'] === 'customer'): ?>
                                <form action="/BATU_E_commerce/admin/users/make-admin/<?= $user['id'] ?>" method="post">
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Make this user an admin?')">
                                        <i class="fas fa-user-shield"></i> Make Admin
                                    </button>
                                </form>
                            <?php else: ?>
                                <form action="/BATU_E_commerce/admin/users/remove-admin/<?= $user['id'] ?>" method="post">
                                    <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Remove admin privileges?')">
                                        <i class="fas fa-user"></i> Remove Admin Privileges
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- User Orders -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order History</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($userOrders)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userOrders as $order): ?>
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
                            <p>This user has not placed any orders yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="/BATU_E_commerce/admin/users" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>
</div>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
require_once dirname(__DIR__) . '/layouts/main.php';
?>