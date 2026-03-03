/**
 * Модальное окно для увеличения изображений
 */
document.addEventListener('DOMContentLoaded', function () {
    // Инициализация модального окна
    function initImageModal() {
        // Получаем элементы модального окна
        const modal = document.getElementById('imageModal');
        if (!modal) return;

        const imageContainer = modal.querySelector('.modal-image__container');
        const closeBtn = modal.querySelector('.modal-image__close');
        const overlay = modal.querySelector('.modal-image__overlay');

        // Получаем все элементы с атрибутом data-zoom
        const imageElements = document.querySelectorAll('[data-zoom]');

        if (!imageElements.length) return;

        // Открытие модального окна
        function openModal(imageSrc) {
            // Создаем изображение
            const img = document.createElement('img');
            img.src = imageSrc;
            img.alt = 'Увеличенное изображение';

            // Очищаем контейнер и добавляем изображение
            imageContainer.innerHTML = '';
            imageContainer.appendChild(img);

            // Отображаем модальное окно
            modal.classList.add('active');

            // Устанавливаем стили напрямую (для надежности)
            modal.style.display = 'block';

            // Блокируем прокрутку страницы
            document.body.style.overflow = 'hidden';
        }

        // Закрытие модального окна
        function closeModal() {
            modal.classList.remove('active');
            modal.style.display = 'none';
            document.body.style.overflow = '';

            // Очищаем содержимое с задержкой
            setTimeout(() => {
                imageContainer.innerHTML = '';
            }, 300);
        }

        // Добавляем обработчики событий
        imageElements.forEach(element => {
            element.addEventListener('click', function (e) {
                e.preventDefault();

                // Получаем URL изображения
                let imageSrc;

                if (this.getAttribute('data-zoom') && this.getAttribute('data-zoom') !== '') {
                    // Если задан конкретный URL в атрибуте
                    imageSrc = this.getAttribute('data-zoom');
                } else if (this.tagName === 'IMG') {
                    // Если элемент сам является изображением
                    imageSrc = this.src;
                } else {
                    // Ищем изображение внутри элемента
                    const img = this.querySelector('img');
                    if (img) {
                        imageSrc = img.src;
                    } else {
                        console.error('Не удалось найти URL изображения');
                        return;
                    }
                }

                openModal(imageSrc);
            });
        });

        // Закрытие по кнопке
        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }

        // Закрытие по клику на оверлей
        if (overlay) {
            overlay.addEventListener('click', closeModal);
        }

        // Закрытие по клавише Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    // Запускаем инициализацию
    initImageModal();
});
