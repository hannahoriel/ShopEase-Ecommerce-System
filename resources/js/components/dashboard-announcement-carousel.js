document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-dashboard-announcement-carousel]')
        .forEach((carousel) => {
            const slides = Array.from(
                carousel.querySelectorAll('[data-dashboard-announcement-slide]')
            );
            const dotsContainer = carousel.querySelector('.dashboard-announcement-dots');
            const previousButton = carousel.querySelector('[data-dashboard-announcement-previous]');
            const nextButton = carousel.querySelector('[data-dashboard-announcement-next]');

            if (slides.length < 2) {
                return;
            }

            let activeIndex = 0;
            let timer;
            const dots = slides.map((slide, index) => {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.setAttribute('role', 'tab');
                dot.setAttribute('aria-label', `Show announcement ${index + 1}`);
                dot.addEventListener('click', () => {
                    showSlide(index);
                    restartTimer();
                });
                dotsContainer?.append(dot);
                return dot;
            });

            const showSlide = (index) => {
                activeIndex = (index + slides.length) % slides.length;

                slides.forEach((slide, slideIndex) => {
                    const isActive = slideIndex === activeIndex;
                    slide.classList.toggle('is-active', isActive);
                    slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
                });

                dots.forEach((dot, dotIndex) => {
                    const isActive = dotIndex === activeIndex;
                    dot.classList.toggle('is-active', isActive);
                    dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
            };

            const stopTimer = () => {
                window.clearInterval(timer);
            };

            const restartTimer = () => {
                stopTimer();
                timer = window.setInterval(() => showSlide(activeIndex + 1), 5000);
            };

            previousButton?.addEventListener('click', () => {
                showSlide(activeIndex - 1);
                restartTimer();
            });

            nextButton?.addEventListener('click', () => {
                showSlide(activeIndex + 1);
                restartTimer();
            });

            carousel.addEventListener('mouseenter', stopTimer);
            carousel.addEventListener('mouseleave', restartTimer);
            carousel.addEventListener('focusin', stopTimer);
            carousel.addEventListener('focusout', restartTimer);

            showSlide(0);
            restartTimer();
        });
});
