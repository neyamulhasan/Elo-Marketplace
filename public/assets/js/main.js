// Main JavaScript for Bio-Organic Herbal Solutions

document.addEventListener('DOMContentLoaded', function() {
    
    // Add to cart with AJAX (can be enhanced)
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/Bio-Organic-Herbal-Solutions/public/cart-action.php';
            
            const productInput = document.createElement('input');
            productInput.type = 'hidden';
            productInput.name = 'product_id';
            productInput.value = productId;
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'add';
            
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'quantity';
            quantityInput.value = '1';
            
            form.appendChild(productInput);
            form.appendChild(actionInput);
            form.appendChild(quantityInput);
            
            document.body.appendChild(form);
            form.submit();
        });
    });
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Form validation enhancement
    const forms = document.querySelectorAll('form[method="POST"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Image lazy loading fallback
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    
    // Confirm dialogs for delete actions
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Are you sure?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // Number input validation
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('input', function() {
            const min = parseFloat(this.getAttribute('min'));
            const max = parseFloat(this.getAttribute('max'));
            const value = parseFloat(this.value);
            
            if (min !== null && value < min) {
                this.value = min;
            }
            if (max !== null && value > max) {
                this.value = max;
            }
        });
    });
    
    // Product Image Carousel - Thumbnail Navigation
    const thumbnails = document.querySelectorAll('.thumbnail-nav');
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            // Remove active class from all thumbnails
            thumbnails.forEach(t => t.classList.remove('active'));
            // Add active class to clicked thumbnail
            this.classList.add('active');
        });
    });
    
    // Carousel slide event - Update active thumbnail
    const productCarousel = document.getElementById('productImageCarousel');
    if (productCarousel) {
        productCarousel.addEventListener('slide.bs.carousel', function(e) {
            const thumbnails = document.querySelectorAll('.thumbnail-nav');
            thumbnails.forEach(t => t.classList.remove('active'));
            if (thumbnails[e.to]) {
                thumbnails[e.to].classList.add('active');
            }
        });
        
        // Set first thumbnail as active on load
        const firstThumbnail = document.querySelector('.thumbnail-nav');
        if (firstThumbnail) {
            firstThumbnail.classList.add('active');
        }
    }
    
    console.log('Bio-Organic Herbal Solutions - Website loaded successfully!');
});
