<?php

/**
 * Шаблон страницы товара
 */

$defined = defined('ABSPATH') ? true : false;
defined('ABSPATH') || exit;

// Если WooCommerce не активен, показываем заглушку и выходим
if (!function_exists('wc_get_product') || !class_exists('WooCommerce')) {
    get_header();
?>
    <div class="main-container">
        <h1>Товар недоступен</h1>
        <p>Для отображения страницы товара требуется активный плагин WooCommerce.</p>
    </div>
<?php
    get_footer();
    return;
}

global $product;

// Убедитесь, что $product является объектом WC_Product
if (!is_a($product, 'WC_Product')) {
    $product = wc_get_product(get_the_ID());
}
$opt_price = get_field('цена_товара_оптом');

if (!empty($opt_price['оптовая_цена']) && $opt_price['оптовая_цена'] > 0) {
    $opt_price_value = $opt_price['оптовая_цена'];
} else {
    // Если оптовой цены нет или она равна 0, проверяем обычную цену
    if ($product->get_price() > 0) {
        $opt_price_value = wc_price($product->get_price());
    } else {
        $opt_price_value = 'Цена не указана';
    }
}

// Проверяем, есть ли оптовое количество
if (!empty($opt_price['опт_кол'])) {
    $opt_price_kol = $opt_price['опт_кол'];
} else {
    // Значение по умолчанию
    $opt_price_kol = 10;
}
get_header(); ?>

<div class="main-container">
    <?php custom_breadcrumbs(); ?>
    <div class="single-product md:grid md:grid-cols-2 md:grid-rows-[auto_auto_auto] md:gap-0 md:gap-x-6 md:mb-20">
        <div class="md:col-start-2 md:row-start-1">
            <h1 class="title"><?= esc_html($product->get_name()) ?></h1>
        </div>
        <div class="image h-auto md:col-start-1 md:row-span-full">
            <?php
            $product_image = wp_get_attachment_url($product->get_image_id());
            if ($product_image) : ?>
                <img class="w-full object-contain" src="<?= esc_url($product_image) ?>" alt="<?= the_title(); ?>">
            <?php else : ?>
                <img class="w-full object-contain" src="<?= esc_url(wc_placeholder_img_src()) ?>"
                    alt="Изображение товара отсутствует">
            <?php endif; ?>
        </div>
        <div class="flex flex-col gap-2 mb-9 md:col-span-1 md:col-start-2 md:row-start-2">
            <div class="price">
                <?php if ($product->get_price() > 0) : ?>
                    <?= wc_price($product->get_price()) ?>
                <?php else : ?>
                    Цена не указана
                <?php endif; ?>
            </div>
            <p class="opt-price">Оптовая цена:</p>
            <p class="opt-price-value"><?= $opt_price_value ?></p>
            <p class="opt-val">при заказе от <?= $opt_price_kol ?> шт.</p>
        </div>
        <div
            class="description flex flex-col mb-11 gap-8 md:mb-0 md:col-span-1 md:col-start-2 md:row-start-3 md:gap-20 md:justify-between">
            <div class="flex gap-11">
                <?php if ($product->is_in_stock()): ?>
                    <p class="text-lg font-medium text-orange">В наличии</p>
                <?php else: ?>
                    <p class="text-lg font-medium text-red-500">Нет в наличии</p>
                <?php endif; ?>
                <?php if ($product->get_sku()): ?>
                    <p class="article text-lg font-medium">код: <?= esc_html($product->get_sku()) ?></p>
                <?php endif; ?>
            </div>
            <div class="flex flex-col gap-8 md:col-span-1 md:gap-12">
                <div class="quantity-wrapper">
                    <button class="quantity-button decrease">-</button>
                    <input type="number" class="quantity-input" value="1" min="1"
                        max="<?= esc_attr($product->get_stock_quantity()) ?>">
                    <button class="quantity-button increase">+</button>
                </div>
                <button class="button add-to-cart-btn" data-product-id="<?= esc_attr($product->get_id()) ?>">Добавить в
                    корзину</button>
            </div>
        </div>
    </div>

    <div class="w-full min-h-[210px] mb-[82px] md:mb-[112px]">
        <!-- Вкладки -->
        <div class="tabs flex justify-between mb-12 text-xl font-medium md:text-2xl md:justify-start md:gap-20">
            <button class="tab-btn active" data-tab="description">
                Описание
            </button>
            <button class="tab-btn" data-tab="characteristics">
                Характеристики
            </button>
        </div>

        <!-- Контент -->
        <div id="description" class="tab-content">
            <div class="content w-full">
                <?= $product->get_description() ?>
            </div>
        </div>
        <div id="characteristics" class="tab-content hidden">
            <div class="content w-full">
                <?= get_field('characteristics') ?>
            </div>
        </div>
    </div>

    <script>
        // Табы
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.tab;

                // Сброс кнопок
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

                // Активируем кнопку
                btn.classList.add('active');

                // Скрыть/показать контент
                document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
                document.getElementById(target).classList.remove('hidden');
            });
        });

        // Количество товара
        const quantityInput = document.querySelector('.quantity-input');
        const decreaseBtn = document.querySelector('.quantity-button.decrease');
        const increaseBtn = document.querySelector('.quantity-button.increase');

        decreaseBtn.addEventListener('click', () => {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });

        increaseBtn.addEventListener('click', () => {
            const currentValue = parseInt(quantityInput.value);
            const maxValue = parseInt(quantityInput.max);
            if (!maxValue || currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
            }
        });

        // Добавление в корзину
        document.querySelector('.add-to-cart-btn').addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantity = document.querySelector('.quantity-input').value;
            const button = this;

            // Заблокируем кнопку на время запроса
            button.disabled = true;
            button.textContent = 'Добавляем...';

            // Используем стандартный WooCommerce AJAX
            const formData = new FormData();
            formData.append('add-to-cart', productId);
            formData.append('quantity', quantity);

            fetch('<?= wc_get_cart_url() ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    // Просто проверяем, что запрос прошел
                    button.textContent = 'Добавлено в корзину';

                    // Обновляем счетчик корзины
                    const cartCounters = document.querySelectorAll('.cart-count');
                    cartCounters.forEach(counter => {
                        const currentCount = parseInt(counter.textContent) || 0;
                        counter.textContent = currentCount + parseInt(quantity);
                    });

                    // Возвращаем кнопку в исходное состояние через 2 секунды
                    setTimeout(() => {
                        button.textContent = 'Добавить в корзину';
                        button.disabled = false;
                    }, 2000);
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    button.textContent = 'Ошибка добавления';
                    setTimeout(() => {
                        button.textContent = 'Добавить в корзину';
                        button.disabled = false;
                    }, 2000);
                });
        })
    </script>

</div>

<?php get_footer(); ?>