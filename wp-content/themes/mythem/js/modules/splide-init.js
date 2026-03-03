document.addEventListener('DOMContentLoaded', function () {
    const heroSplide = document.getElementById('hero-splide');
    if (heroSplide && window.Splide) {
        const splide = new Splide(heroSplide, {
            accessibility: false,
            type: 'fade',
            rewind: true,
            autoplay: true,
            interval: 5000,
            speed: 2000,
            pauseOnHover: false,
            pauseOnFocus: false,
            arrows: false,
            pagination: true,
            paginationDirection: 'ltr',
            classes: {
                pagination: 'hero-pagination',
            },
            breakpoints: {
                768: {
                    // Можно добавить специфические настройки для мобилок, если нужно
                }
            }
        });

        // Удаляем aria-label, aria-roledescription и role у слайдов после монтирования/обновления
        function removeAriaAttributes() {
            document.querySelectorAll('#hero-splide .splide__slide').forEach(el => {
                if (el.hasAttribute('aria-label')) el.removeAttribute('aria-label');
                if (el.hasAttribute('aria-roledescription')) el.removeAttribute('aria-roledescription');
                if (el.hasAttribute('role')) el.removeAttribute('role');
            });
        }

        splide.on('mounted', removeAriaAttributes);
        splide.on('refresh', removeAriaAttributes);
        splide.mount();
    }
});