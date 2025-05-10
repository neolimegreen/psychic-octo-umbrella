<?php
/**
 * Wishlist Button Component
 * 
 * Displays a heart icon button for adding/removing products from wishlist
 * 
 * @param array $product The product data with at least 'id' key
 */

// Check if user is logged in
$isLoggedIn = \App\Core\Auth::getInstance()->isLoggedIn();

// If logged in, check if product is in wishlist
$inWishlist = false;
if ($isLoggedIn) {
    $wishlistModel = new \App\Models\Wishlist();
    $userId = \App\Core\Auth::getInstance()->getUserId();
    $productId = isset($product['id']) ? $product['id'] : (isset($product['product_id']) ? $product['product_id'] : null);
    if ($productId) {
        $inWishlist = $wishlistModel->exists($userId, $productId);
    }
}

// Ensure productId is set for both logged in and not logged in users
$productId = isset($product['id']) ? $product['id'] : (isset($product['product_id']) ? $product['product_id'] : null);
?>

<!-- Wishlist Button -->
<div class="wishlist-container d-inline-block" style="position: absolute; top: 10px; right: 10px; z-index: 10;">
    <?php if ($isLoggedIn): ?>
        <?php if ($inWishlist): ?>
            <form action="/BATU_E_commerce/wishlist/remove/<?= $productId ?>" method="post" class="d-inline">
                <button type="submit" class="btn p-0 border-0 bg-transparent wishlist-btn" title="إزالة من المفضلة">
                    <i class="fas fa-heart fa-lg text-danger"></i>
                </button>
            </form>
        <?php else: ?>
            <form action="/BATU_E_commerce/wishlist/add/<?= $productId ?>" method="post" class="d-inline">
                <button type="submit" class="btn p-0 border-0 bg-transparent wishlist-btn" title="أضف إلى المفضلة">
                    <i class="far fa-heart fa-lg text-secondary"></i>
                </button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <a href="/BATU_E_commerce/login" class="btn p-0 border-0 bg-transparent" title="سجل دخول للإضافة إلى المفضلة">
            <i class="far fa-heart fa-lg text-secondary"></i>
        </a>
    <?php endif; ?>
</div>

<style>
    .wishlist-btn:hover .fa-heart.text-secondary {
        color: #dc3545 !important; /* Changes to danger color on hover */
    }
</style>
