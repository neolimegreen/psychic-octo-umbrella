/**
 * BATU E-Commerce - Main JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Cart quantity update
    const quantityInputs = document.querySelectorAll('.cart-quantity-input');
    if (quantityInputs) {
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                const form = this.closest('form');
                if (form) {
                    form.submit();
                }
            });
        });
    }
    
    // Product image preview
    const productImageInput = document.getElementById('product-image');
    const imagePreview = document.getElementById('image-preview');
    
    if (productImageInput && imagePreview) {
        productImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Confirm delete
    const deleteButtons = document.querySelectorAll('.delete-confirm');
    if (deleteButtons) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    }
    
    // Star rating system
    const ratingInputs = document.querySelectorAll('.rating-input input');
    if (ratingInputs) {
        ratingInputs.forEach(input => {
            input.addEventListener('change', function() {
                const ratingValue = document.getElementById('rating-value');
                if (ratingValue) {
                    ratingValue.textContent = this.value;
                }
            });
        });
    }
    
    // Address form toggle for checkout
    const sameAddressCheckbox = document.getElementById('same-address');
    const shippingAddressSection = document.getElementById('shipping-address-section');
    
    if (sameAddressCheckbox && shippingAddressSection) {
        sameAddressCheckbox.addEventListener('change', function() {
            if (this.checked) {
                shippingAddressSection.style.display = 'none';
            } else {
                shippingAddressSection.style.display = 'block';
            }
        });
        
        // Trigger the change event on page load
        sameAddressCheckbox.dispatchEvent(new Event('change'));
    }
});