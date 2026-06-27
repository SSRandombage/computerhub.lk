/**
 * ComputerHub Client-Side Scripts
 * 
 * This file manages standard user interface interactivity, such as:
 * 1. Checking if a user is logged in to show a dynamic welcome message or login buttons.
 * 2. Adding products to the shopping cart without reloading the page (AJAX).
 * 3. Displaying popup notifications (toasts) when a product is added.
 * 4. Providing a live search filter on category pages.
 */

document.addEventListener("DOMContentLoaded", function () {
    // ----------------------------------------------------
    // 1. PAGE DETECTION & INITIALIZATION
    // ----------------------------------------------------
    
    // We check the URL of the current page to customize search placeholders
    const currentPage = decodeURIComponent(window.location.pathname.split('/').pop().replace('.html', ''));
    console.log("Current page loaded:", currentPage);

    // Helper function to escape text to prevent security vulnerabilities (XSS)
    // It replaces characters like < and > with HTML entities (&lt; and &gt;)
    function escapeHtml(str) {
        return str.replace(/&/g, "&amp;")
                  .replace(/</g, "&lt;")
                  .replace(/>/g, "&gt;")
                  .replace(/"/g, "&quot;")
                  .replace(/'/g, "&#039;");
    }

    // ----------------------------------------------------
    // 2. DYNAMIC NAVBAR (Check Login Status)
    // ----------------------------------------------------
    
    // This function checks the server session to see if the user is logged in
    function updateNavbar() {
        // We use fetch() to request check_login.php in the background without reloading
        fetch('NewBackend/check_login.php')
            .then(response => response.json()) // Convert the server's reply to a JS object
            .then(data => {
                const navRight = document.querySelector('.nav-right');
                if (!navRight) return; // If navbar right section is not on the page, stop
                
                if (data.logged_in) {
                    // If the user is logged in, show their name, the cart, and a Logout button
                    navRight.innerHTML = `
                        <span>Welcome, ${escapeHtml(data.user_name)}!</span>
                        <a href="Cart.php" class="cart-link">
                            <img class="cart-icon" src="Cart.png" alt="Cart">
                            ${data.cart_count > 0 ? `<span class="cart-badge">${data.cart_count}</span>` : ''}
                        </a>
                        <a href="NewBackend/actions/logout.php" class="logout-btn">Logout</a>
                    `;
                } else {
                    // If not logged in, show the standard Login link and cart icon
                    navRight.innerHTML = `
                        <a href="Login.html">Login</a>
                        <a href="Cart.php" class="cart-link"><img class="cart-icon" src="Cart.png" alt="Cart"></a>
                    `;
                }
            })
            .catch(error => console.error("Error updating navbar:", error));
    }

    // Call updateNavbar immediately when the page loads
    updateNavbar();

    // ----------------------------------------------------
    // 3. TOAST NOTIFICATION SYSTEM
    // ----------------------------------------------------
    
    // Displays a nice, non-blocking toast alert popup on the screen
    function showToast(message, type = 'success') {
        // Find or create the notification container
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }
        
        // Create the individual toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Choose appropriate icon based on message type
        let icon = 'ℹ️';
        if (type === 'success') icon = '🛒';
        else if (type === 'error') icon = '⚠️';
        
        // Set the inner HTML content of the toast popup
        toast.innerHTML = `
            <span class="toast-icon">${icon}</span>
            <span class="toast-message">${message}</span>
            <button class="toast-close">&times;</button>
        `;
        
        container.appendChild(toast);
        
        // Set up the close button
        toast.querySelector('.toast-close').addEventListener('click', () => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 400);
        });
        
        // Auto-remove the notification after 4 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.add('fade-out');
                setTimeout(() => toast.remove(), 400);
            }
        }, 4000);
    }

    // ----------------------------------------------------
    // 4. ADD TO CART FUNCTIONALITY
    // ----------------------------------------------------
    
    // Sends the product ID to the backend using an AJAX request
    function addToCart(productId) {
        console.log("Adding product ID to cart:", productId);
        
        // 1st: Check login status
        fetch('NewBackend/check_login.php')
            .then(response => response.json())
            .then(data => {
                if (data.logged_in) {
                    // User is logged in, prepare form data
                    const formData = new FormData();
                    formData.append('product_id', productId);
                    formData.append('quantity', 1);

                    // Send the request to add_to_cart_ajax.php
                    fetch('NewBackend/actions/add_to_cart_ajax.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            const prodName = result.product_name || "Product";
                            showToast(`<strong>${escapeHtml(prodName)}</strong> added to cart! <a href="Cart.php" style="color: #00b4d8; text-decoration: underline; margin-left: 8px; font-weight: 600;">View Cart</a>`, 'success');
                            updateNavbar(); // Refresh navbar cart count badge
                        } else {
                            showToast(result.message, 'error');
                        }
                    })
                    .catch(error => {
                        showToast("Error adding to cart. Please try again.", 'error');
                    });
                } else {
                    // User is not logged in, show toast alert, then redirect to login page
                    showToast("Please login to add items to cart!", 'error');
                    setTimeout(() => {
                        window.location.href = 'Login.html';
                    }, 1500);
                }
            })
            .catch(error => {
                showToast("Error checking login status. Please try again.", 'error');
            });
    }

    // Bind event listeners to all "Add to cart" buttons
    // Instead of a giant configuration map in JavaScript, we read the product ID
    // directly from the HTML attribute `data-product-id` on the clicked button.
    const cartButtons = document.querySelectorAll('.cart-button');
    console.log(`Configuring event listeners for ${cartButtons.length} cart buttons.`);
    
    cartButtons.forEach(button => {
        button.addEventListener("click", function() {
            // "this" refers to the specific button that was clicked
            const productId = this.getAttribute('data-product-id');
            if (productId) {
                addToCart(productId);
            } else {
                console.warn("Cart button clicked, but data-product-id was missing.");
            }
        });
    });

    // ----------------------------------------------------
    // 5. LIVE SEARCH FILTER (Category Pages Only)
    // ----------------------------------------------------
    const targetContainer = document.querySelector('.grid-container') || document.querySelector('.card-container');
    
    // We only display the search filter on product listing pages
    if (targetContainer && currentPage !== 'Laptops' && currentPage !== 'Categories' && currentPage !== 'index' && currentPage !== '' && currentPage !== 'Home Page' && currentPage !== 'Cart') {
        const searchContainer = document.createElement('div');
        searchContainer.className = 'search-container';
        
        let placeholderText = "Search products in this category...";
        if (currentPage === 'Computer parts and Accessories') {
            placeholderText = "Search parts & accessories...";
        }
        
        // HTML structure for the search bar
        searchContainer.innerHTML = `
            <input type="text" class="search-input" placeholder="${placeholderText}">
            <div class="search-icon">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
        `;
        
        // Insert search bar right before the products grid
        targetContainer.parentNode.insertBefore(searchContainer, targetContainer);
        
        const searchInput = searchContainer.querySelector('.search-input');
        
        // Listen to keystrokes in the search input
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            const cards = targetContainer.querySelectorAll('.product-card, .card');
            
            // Loop through all product cards and show/hide them depending on search query
            cards.forEach(card => {
                const title = card.querySelector('h3') ? card.querySelector('h3').textContent.toLowerCase() : '';
                const category = card.querySelector('.category') ? card.querySelector('.category').textContent.toLowerCase() : '';
                
                if (title.includes(query) || category.includes(query)) {
                    card.classList.remove('hidden'); // Show card
                } else {
                    card.classList.add('hidden');    // Hide card
                }
            });
        });
    }
});