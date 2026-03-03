/**
 * Современный обработчик корзины для карточек товаров
 */

document.addEventListener('DOMContentLoaded', function () {
    // Обработчик добавления товара в корзину
    document.addEventListener('submit', function (e) {
        // Проверяем, что это форма добавления в корзину из карточки товара
        if (e.target.classList.contains('product-card__cart-form')) {
            e.preventDefault();

            const form = e.target;
            const button = form.querySelector('button[type="submit"]');
            const originalContent = button.innerHTML;

            // Показываем состояние загрузки
            button.active = true;

            // Получаем данные формы
            const formData = new FormData(form);

            // Отправляем стандартную форму WooCommerce
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
                .then(response => {

                    // Обновляем счетчик корзины
                    updateCartCount();

                    // Вызываем событие для других скриптов
                    document.dispatchEvent(new CustomEvent('productAddedToCart', {
                        detail: { productId: formData.get('add-to-cart') }
                    }));
                })
                .catch(error => {
                    console.error('Ошибка при добавлении в корзину:', error);
                })
                .finally(() => {
                    // Восстанавливаем кнопку
                    button.active = false;

                });
        }
    });

    // Функция для показа уведомлений


    // Функция для обновления счетчика корзины
    function updateCartCount() {
        // Просто увеличиваем счетчик на 1 на клиенте
        const cartCounters = document.querySelectorAll('.cart-count');
        cartCounters.forEach(counter => {
            const currentCount = parseInt(counter.textContent) || 0;
            counter.textContent = currentCount + 1;
        });
    }

    // Функция для обработки изменения количества товара в корзине
    function handleQuantityChange(productId, newQuantity) {
        if (newQuantity <= 0) {
            removeFromCart(productId);
            return;
        }

        const formData = new FormData();
        formData.append('action', 'update_cart_item');
        formData.append('product_id', productId);
        formData.append('quantity', newQuantity);
        formData.append('nonce', cart_ajax_object?.nonce || '');

        fetch(cart_ajax_object?.ajax_url || '/wp-admin/admin-ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Количество товара обновлено', 'success');
                    // Обновляем интерфейс корзины
                    updateCartInterface(data.data);
                } else {
                    showNotification('Ошибка при обновлении количества', 'error');
                }
            })
            .catch(error => {
                console.error('Ошибка при обновлении количества:', error);
                showNotification('Произошла ошибка. Попробуйте позже.', 'error');
            });
    }

    // Функция для удаления товара из корзины
    function removeFromCart(cartItemKey) {
        const formData = new FormData();
        formData.append('action', 'remove_cart_item');
        formData.append('cart_item_key', cartItemKey);
        formData.append('nonce', cart_ajax_object?.nonce || '');

        fetch(cart_ajax_object?.ajax_url || '/wp-admin/admin-ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Товар удален из корзины', 'success');
                    // Обновляем интерфейс корзины
                    updateCartInterface(data.data);
                } else {
                    showNotification('Ошибка при удалении товара', 'error');
                }
            })
            .catch(error => {
                console.error('Ошибка при удалении товара:', error);
                showNotification('Произошла ошибка. Попробуйте позже.', 'error');
            });
    }

    // Функция для обновления интерфейса корзины
    function updateCartInterface(data) {
        // Обновляем счетчики и суммы если данные предоставлены
        if (data.cart_total) {
            const cartTotals = document.querySelectorAll('.cart-total');
            cartTotals.forEach(total => {
                total.textContent = data.cart_total;
            });
        }

        if (data.cart_count) {
            const cartCounts = document.querySelectorAll('.cart-count');
            cartCounts.forEach(count => {
                count.textContent = data.cart_count;
            });
        }
    }

    // Экспортируем функции для использования в других скриптах
    window.CartHandler = {
        updateCartCount,
        handleQuantityChange,
        removeFromCart
    };
});
