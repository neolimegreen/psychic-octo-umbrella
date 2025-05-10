<?php
/**
 * Admin Users Management
 * 
 * Displays all users with options to view details and manage accounts
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <h1 class="mb-4">Manage Users</h1>
    
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
    
    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($users)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Registered</th>
                                <th>Orders</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <?php if (isset($userOrderCounts[$user['id']])): ?>
                                            <?= $userOrderCounts[$user['id']] ?>
                                        <?php else: ?>
                                            0
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/BATU_E_commerce/admin/users/view/<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                                <?php if ($user['role'] === 'customer'): ?>
                                                    <form action="/BATU_E_commerce/admin/users/make-admin/<?= $user['id'] ?>" method="post" class="d-inline">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Make this user an admin?')">
                                                            <i class="fas fa-user-shield"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form action="/BATU_E_commerce/admin/users/remove-admin/<?= $user['id'] ?>" method="post" class="d-inline">
                                                        <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Remove admin privileges?')">
                                                            <i class="fas fa-user"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <p>No users found.</p>
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