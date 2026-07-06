document.addEventListener('DOMContentLoaded', () => {
    initBenefitsCarousel();
    initCardsToggle();
    initFeaturesSlider();
});

function initBenefitsCarousel() {
    const carousel = document.querySelector('[data-benefits-carousel]');

    if (! carousel) {
        return;
    }

    const slides = JSON.parse(carousel.dataset.slides ?? '[]');

    if (slides.length === 0) {
        return;
    }

    const slide = carousel.querySelector('[data-benefits-slide]');
    const prevButton = carousel.querySelector('[data-benefits-prev]');
    const nextButton = carousel.querySelector('[data-benefits-next]');

    if (! slide || ! prevButton || ! nextButton) {
        return;
    }

    let index = 0;

    const showSlide = (nextIndex) => {
        index = (nextIndex + slides.length) % slides.length;
        slide.src = slides[index];
    };

    prevButton.addEventListener('click', () => showSlide(index - 1));
    nextButton.addEventListener('click', () => showSlide(index + 1));
}

function initCardsToggle() {
    const section = document.querySelector('[data-cards-toggle]');

    if (! section) {
        return;
    }

    const card1 = section.querySelector('.card-1');
    const card2 = section.querySelector('.card-2');

    if (!card1 || !card2) {
        return;
    }

    // Initialize default state
    section.dataset.activeCard = 'none';

    card1.addEventListener('click', () => {
        if (section.dataset.activeCard === '1') {
            section.dataset.activeCard = 'none';
        } else {
            section.dataset.activeCard = '1';
        }
    });

    card2.addEventListener('click', () => {
        if (section.dataset.activeCard === '2') {
            section.dataset.activeCard = 'none';
        } else {
            section.dataset.activeCard = '2';
        }
    });
}

function initFeaturesSlider() {
    const section = document.querySelector('[data-features-section]');
    if (!section) return;

    const track = section.querySelector('[data-features-track]');
    // No track means the "no slides" fallback is showing (e.g. an admin
    // deleted every slide) — nothing to wire up.
    if (!track) return;

    const slides = Array.from(track.querySelectorAll('[data-feature-slide]'));
    const prevBtn = section.querySelector('[data-features-prev]');
    const nextBtn = section.querySelector('[data-features-next]');
    const currentIndicator = section.querySelector('[data-features-current]');
    const totalIndicator = section.querySelector('[data-features-total]');

    if (slides.length === 0 || !prevBtn || !nextBtn) return;

    let currentIndex = 0;
    
    if (totalIndicator) {
        totalIndicator.textContent = slides.length.toString().padStart(2, '0');
    }

    const updateSlider = () => {
        const slideLeft = slides[currentIndex].offsetLeft;
        track.style.transform = `translateX(-${slideLeft}px)`;

        slides.forEach((slide, index) => {
            const body = slide.querySelector('[data-features-body]');
            if (index === currentIndex) {
                slide.classList.remove('opacity-50');
                slide.classList.add('opacity-100');
                if (body) {
                    body.classList.remove('opacity-0', 'invisible');
                }
            } else {
                slide.classList.remove('opacity-100');
                slide.classList.add('opacity-50');
                if (body) {
                    body.classList.add('opacity-0', 'invisible');
                }
            }
        });

        if (currentIndicator) {
            currentIndicator.textContent = (currentIndex + 1).toString().padStart(2, '0');
        }
        
        prevBtn.style.opacity = currentIndex === 0 ? '0.4' : '1';
        nextBtn.style.opacity = currentIndex === slides.length - 1 ? '0.4' : '1';
    };

    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlider();
        }
    });

    nextBtn.addEventListener('click', () => {
        if (currentIndex < slides.length - 1) {
            currentIndex++;
            updateSlider();
        }
    });

    updateSlider();
}
