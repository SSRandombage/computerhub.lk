let index = 0;
const slides = document.querySelectorAll(".slide");
const totalSlides = slides.length;
const slidesToShow = 3; // Number of slides visible at a time
const slideshowContainer = document.querySelector(".slideshow-container");

function updateSlidePosition() {
    if (slides.length === 0) return;
    const slideWidth = slides[0].clientWidth;
    document.querySelector(".slide-track").style.transform = `translateX(${-index * slideWidth}px)`;
    
    // Update active dot
    const dots = document.querySelectorAll(".dot");
    dots.forEach((dot, i) => {
        if (i === index) {
            dot.classList.add("active");
        } else {
            dot.classList.remove("active");
        }
    });
}

function moveSlide(direction) {
    index += direction;

    // Loop back to start if at the end
    if (index > totalSlides - slidesToShow) index = 0;
    if (index < 0) index = totalSlides - slidesToShow;

    updateSlidePosition();
}

function goToSlide(n) {
    index = n;
    updateSlidePosition();
}

// Auto-slide every 3 seconds
let autoSlideInterval;

function startAutoSlide() {
    autoSlideInterval = setInterval(() => moveSlide(1), 3000);
}

function stopAutoSlide() {
    clearInterval(autoSlideInterval);
}

// Initialize slideshow features
document.addEventListener("DOMContentLoaded", () => {
    if (slideshowContainer && totalSlides > slidesToShow) {
        // Create indicator dots dynamically
        const dotsContainer = document.createElement("div");
        dotsContainer.className = "slideshow-dots";
        
        const numDots = totalSlides - slidesToShow + 1;
        for (let i = 0; i < numDots; i++) {
            const dot = document.createElement("span");
            dot.className = `dot${i === 0 ? " active" : ""}`;
            dot.addEventListener("click", () => goToSlide(i));
            dotsContainer.appendChild(dot);
        }
        
        slideshowContainer.appendChild(dotsContainer);
        
        // Pause auto-sliding on hover
        slideshowContainer.addEventListener("mouseenter", stopAutoSlide);
        slideshowContainer.addEventListener("mouseleave", startAutoSlide);
    }
    
    // Start sliding
    startAutoSlide();
    
    // Handle window resize to recalculate widths properly
    window.addEventListener("resize", updateSlidePosition);
});
