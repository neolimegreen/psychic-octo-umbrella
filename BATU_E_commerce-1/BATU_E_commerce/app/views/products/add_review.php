<?php require_once ROOT_DIR . '/app/views/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/BATU_E_commerce/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/BATU_E_commerce/products">Products</a></li>
                    <li class="breadcrumb-item"><a href="/BATU_E_commerce/products/<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></li>
                    <li class="breadcrumb-item"><a href="/BATU_E_commerce/products/<?php echo $product['id']; ?>/reviews">Reviews</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Review</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="<?php echo !empty($product['image_url']) ? $product['image_url'] : '/BATU_E_commerce/public/images/no-image.jpg'; ?>" class="card-img-top object-fit-contain" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="card-text"><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Write a Review</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="/BATU_E_commerce/products/<?php echo $product['id']; ?>/reviews/add" method="post">
                        <div class="form-group mb-3">
                            <label for="rating">Rating</label>
                            <div class="rating-input">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rating" id="rating1" value="1" <?php echo (isset($_POST['rating']) && $_POST['rating'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="rating1">1</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rating" id="rating2" value="2" <?php echo (isset($_POST['rating']) && $_POST['rating'] == 2) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="rating2">2</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rating" id="rating3" value="3" <?php echo (isset($_POST['rating']) && $_POST['rating'] == 3) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="rating3">3</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rating" id="rating4" value="4" <?php echo (isset($_POST['rating']) && $_POST['rating'] == 4) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="rating4">4</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rating" id="rating5" value="5" <?php echo (isset($_POST['rating']) && $_POST['rating'] == 5) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="rating5">5</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="comment">Review</label>
                            <textarea class="form-control" id="comment" name="comment" rows="5" required><?php echo isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                        <a href="/BATU_E_commerce/products/<?php echo $product['id']; ?>/reviews" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_DIR . '/app/views/includes/footer.php'; ?>
