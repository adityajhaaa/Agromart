// Mobile Menu Toggle
const hamburgerMenu = document.querySelector('.hamburger-menu');
const navMenu = document.querySelector('.nav-menu');

if (hamburgerMenu && navMenu) {
    hamburgerMenu.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
}

// Dropdown Menu Toggle
const dropdowns = document.querySelectorAll('.dropdown');

dropdowns.forEach(dropdown => {
    const link = dropdown.querySelector('a');
    const menu = dropdown.querySelector('.dropdown-menu');
    
    if (link && menu) {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            dropdown.classList.toggle('active');
        });
    }
});

// Product Image Gallery
const productGallery = document.querySelector('.product-gallery');
const mainImage = document.querySelector('.main-image');
const thumbnails = document.querySelectorAll('.thumbnail');

if (productGallery && mainImage && thumbnails.length > 0) {
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', () => {
            const newSrc = thumbnail.src;
            mainImage.src = newSrc;
            
            thumbnails.forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
        });
    });
}

// Quantity Selector
const quantityInputs = document.querySelectorAll('.quantity-input');

quantityInputs.forEach(input => {
    const minusBtn = input.previousElementSibling;
    const plusBtn = input.nextElementSibling;
    
    if (minusBtn && plusBtn) {
        minusBtn.addEventListener('click', () => {
            let value = parseInt(input.value);
            if (value > 1) {
                input.value = value - 1;
            }
        });
        
        plusBtn.addEventListener('click', () => {
            let value = parseInt(input.value);
            input.value = value + 1;
        });
    }
});

// Add to Cart
const addToCartButtons = document.querySelectorAll('.add-to-cart');

addToCartButtons.forEach(button => {
    button.addEventListener('click', async (e) => {
        e.preventDefault();
        
        const productId = button.dataset.productId;
        const quantity = button.closest('.product-actions').querySelector('.quantity-input').value;
        
        try {
            const response = await fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update cart count
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                }
                
                // Show success message
                alert('Product added to cart successfully!');
            } else {
                alert('Failed to add product to cart. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        }
    });
});

// Form Validation
const forms = document.querySelectorAll('form');

forms.forEach(form => {
    form.addEventListener('submit', (e) => {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});

// Testimonials Slider
const testimonials = document.querySelectorAll('.testimonial-item');
let currentTestimonial = 0;

if (testimonials.length > 0) {
    function showTestimonial(index) {
        testimonials.forEach((testimonial, i) => {
            testimonial.style.display = i === index ? 'block' : 'none';
        });
    }
    
    function nextTestimonial() {
        currentTestimonial = (currentTestimonial + 1) % testimonials.length;
        showTestimonial(currentTestimonial);
    }
    
    // Show first testimonial
    showTestimonial(currentTestimonial);
    
    // Auto-rotate testimonials every 5 seconds
    setInterval(nextTestimonial, 5000);
}

// Smooth Scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Lazy Loading Images
const lazyImages = document.querySelectorAll('img[data-src]');

if ('loading' in HTMLImageElement.prototype) {
    // Browser supports native lazy loading
    lazyImages.forEach(img => {
        img.src = img.dataset.src;
    });
} else {
    // Fallback for browsers that don't support lazy loading
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                observer.unobserve(img);
            }
        });
    });
    
    lazyImages.forEach(img => imageObserver.observe(img));
}

// Search Functionality
const searchForm = document.querySelector('.search-bar form');

if (searchForm) {
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const searchInput = searchForm.querySelector('input');
        const searchTerm = searchInput.value.trim();
        
        if (searchTerm) {
            window.location.href = `search.php?q=${encodeURIComponent(searchTerm)}`;
        }
    });
}

// Cart Management
const cartItems = document.querySelectorAll('.cart-item');
const updateCartButtons = document.querySelectorAll('.update-cart');
const removeCartButtons = document.querySelectorAll('.remove-from-cart');

if (cartItems.length > 0) {
    // Update cart item quantity
    updateCartButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            
            const cartItem = button.closest('.cart-item');
            const itemId = cartItem.dataset.itemId;
            const quantity = cartItem.querySelector('.quantity-input').value;
            
            try {
                const response = await fetch('update_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        quantity: quantity
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update total price
                    const totalPrice = cartItem.querySelector('.item-total');
                    if (totalPrice) {
                        totalPrice.textContent = data.item_total;
                    }
                    
                    // Update cart total
                    const cartTotal = document.querySelector('.cart-total');
                    if (cartTotal) {
                        cartTotal.textContent = data.cart_total;
                    }
                } else {
                    alert('Failed to update cart. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    });
    
    // Remove cart item
    removeCartButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            
            const cartItem = button.closest('.cart-item');
            const itemId = cartItem.dataset.itemId;
            
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                try {
                    const response = await fetch('remove_from_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            item_id: itemId
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        cartItem.remove();
                        
                        // Update cart total
                        const cartTotal = document.querySelector('.cart-total');
                        if (cartTotal) {
                            cartTotal.textContent = data.cart_total;
                        }
                        
                        // Update cart count
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;
                        }
                    } else {
                        alert('Failed to remove item from cart. Please try again.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            }
        });
    });
}

// Checkout Form Validation
const checkoutForm = document.querySelector('.checkout-form');

if (checkoutForm) {
    checkoutForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const requiredFields = checkoutForm.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        });
        
        if (isValid) {
            // Process checkout
            checkoutForm.submit();
        } else {
            alert('Please fill in all required fields.');
        }
    });
} 