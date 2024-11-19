let currentSlide = 0;

function moveSlide(step) {
    const slides = document.querySelectorAll('.review-item');
    const totalSlides = slides.length;
    currentSlide = (currentSlide + step + totalSlides) % totalSlides;
    showSlide(currentSlide);
}

function showSlide(index) {
    const slides = document.querySelectorAll('.review-item');
    slides.forEach((slide, i) => {
        slide.classList.remove('active');
        if (i === index) {
            slide.classList.add('active');
        }
    });
}

// Charger les avis depuis le serveur
document.addEventListener('DOMContentLoaded', function() {
    fetch('fetch_verified_reviews.php')  // Appelle la page PHP pour récupérer les avis
        .then(response => response.json())
        .then(data => {
            const slider = document.querySelector('.reviews-slider');
            data.forEach(review => {
                const reviewElement = document.createElement('div');
                reviewElement.classList.add('review-item');
                reviewElement.innerHTML = `
                    <p class="review-text">"${review.avis}"</p>
                    <p class="review-author">- ${review.pseudo}</p>
                `;
                slider.appendChild(reviewElement);
            });

            // Afficher le premier avis
            showSlide(currentSlide);
        })
        .catch(error => console.error('Erreur lors du chargement des avis:', error));
});
