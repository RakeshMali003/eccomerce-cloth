// Sticky Header Logic
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('sticky');
    } else {
        navbar.classList.remove('sticky');
    }
});

// Mock functions for UI feedback
function addToCart(id) {
    console.log("Added product " + id + " to cart");
    // Trigger an alert or mini-cart update here
}