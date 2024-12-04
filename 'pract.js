// JavaScript for basic interactivity (e.g., hero slider)
document.addEventListener('DOMContentLoaded', () => {
    const heroImages = ['images/hero1.jpg', 'images/hero2.jpg', 'images/hero3.jpg'];
    let currentImageIndex = 0;
    const heroSection = document.querySelector('.hero');
    
    function changeHeroImage() {
        heroSection.style.backgroundImage = `url(${heroImages[currentImageIndex]})`;
        currentImageIndex = (currentImageIndex + 1) % heroImages.length;
    }

    setInterval(changeHeroImage, 3000); // Change image every 3 seconds
});
