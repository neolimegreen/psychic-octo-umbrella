<?php
/**
 * Registration page
 * 
 * Allows users to create a new account
 */

// Start output buffering
ob_start();
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Create an Account</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['register'])): ?>
                        <div class="alert alert-danger">
                            <?= $errors['register'] ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="/BATU_E_commerce/register" method="post">
                        <!-- Name Field -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= $name ?? '' ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['name'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Email Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= $email ?? '' ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['email'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" required>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['password'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Confirm Password Field -->
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" required>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['confirm_password'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">Already have an account? <a href="/BATU_E_commerce/login">Login</a></p>
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