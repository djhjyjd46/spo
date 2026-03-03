<?php
/* Template Name: Избранное */
get_header();
defined('ABSPATH') || exit;
?>
<div class="main-container page-default">
    <?php custom_breadcrumbs(); ?>
    <h1><?php the_title(); ?></h1>
    <div class="content">
        <?php if (WC()->cart->is_empty()): ?>
            <div class="cart-empty">
                <p>Ваша корзина пуста. <a href="<?= esc_url(wc_get_page_permalink('shop')); ?>">Перейти в магазин</a>
                </p>
            </div>
        <?php else: ?>
            <div class="cart">
                <div class="product__list">
                    <ul>
                        <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                            $product = $cart_item['data'];
                            if (!$product || !$product->exists()) {
                                continue;
                            }
                            $product_permalink = $product->is_visible() ? $product->get_permalink($cart_item) : '';
                        ?>
                            <li class="product-cart" data-cart-item-key="<?= $cart_item_key; ?>">
                                <div class="flex gap-4 md:gap-5">
                                    <div class="image">
                                        <?php if ($product_permalink): ?>
                                            <a href="<?= esc_url($product_permalink); ?>">
                                                <?= $product->get_image(); ?>
                                            </a>
                                        <?php else: ?>
                                            <?= $product->get_image(); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="info">
                                        <h3><?= $product->get_name(); ?></h3>
                                        <p>Код: <?= $product->get_sku(); ?></p>
                                    </div>
                                </div>
                                <div class="flex gap-6 md:gap-11 justify-center md:justify-start items-stretch md:items-center">
                                    <div class="calc">
                                        <div class="quantity-wrapper">
                                            <button class="quantity-decrease"
                                                data-cart-item-key="<?= $cart_item_key; ?>">-</button>
                                            <input type="number" class="quantity-input" value="<?= $cart_item['quantity']; ?>"
                                                min="1">
                                            <button class="quantity-increase"
                                                data-cart-item-key="<?= $cart_item_key; ?>">+</button>
                                        </div>
                                        <div class="sum">
                                            <span class="item-total"><?= wc_price($cart_item['line_total']); ?></span>
                                        </div>
                                    </div>
                                    <div class="cart-price">
                                        <span><?= wc_price($product->get_price()); ?></span>
                                    </div>
                                </div>
                                <button class="remove-item" data-cart-item-key="<?= $cart_item_key; ?>"><svg width="14"
                                        height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L13 13M1 13L13 1" stroke="#656565" />
                                    </svg>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="product__summ">
                    <div class="product">
                        <div class="prod-cointainer">
                            <span
                                class="cart-count"><?= WC()->cart->get_cart_contents_count(); ?></span><span>товар(ов)</span>
                        </div>
                        <div class="prod-summ">
                            <span><?= WC()->cart->get_cart_total(); ?></span>
                        </div>
                    </div>
                    <div class="summ">
                        <span class="summ-text">Итого</span>
                        <div class="prod-summ">
                            <span class="cart-total"><?= WC()->cart->get_cart_total(); ?></span>
                        </div>
                    </div>
                    <div class="product-page__buttons">
                        <button class="button" data-modal="modalCall" data='get-cart'>Оформить заказ</button>
                        <div class="button button--white">
                            <a href="<?= esc_url(wc_get_page_permalink('shop')) ?>">Продолжить покупки</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Изменение количества
        document.querySelectorAll('.quantity-increase, .quantity-decrease').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const cartItemKey = btn.dataset.cartItemKey;
                const li = btn.closest('li[data-cart-item-key]');
                const input = li.querySelector('.quantity-input');
                let quantity = parseInt(input.value);

                if (btn.classList.contains('quantity-increase')) {
                    quantity++;
                } else if (btn.classList.contains('quantity-decrease') && quantity > 1) {
                    quantity--;
                }

                // Показываем состояние загрузки
                btn.disabled = true;

                fetch('<?= admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            action: 'update_cart_quantity',
                            cart_item_key: cartItemKey,
                            quantity: quantity
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            input.value = quantity;
                            li.querySelector('.item-total').innerHTML = data.data.item_total;
                            document.querySelectorAll('.cart-total').forEach(el => el
                                .innerHTML = data.data.cart_total);
                            document.querySelectorAll('.cart-count').forEach(el => el
                                .innerHTML = data.data.cart_count);
                        } else {
                            console.error('Ошибка обновления количества:', data.data);
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка:', error);
                    })
                    .finally(() => {
                        btn.disabled = false;
                    });
            });
        });

        // Удаление товара
        document.querySelectorAll('.remove-item').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const cartItemKey = btn.dataset.cartItemKey;
                const li = btn.closest('li[data-cart-item-key]');

                // Показываем состояние загрузки
                btn.disabled = true;

                fetch('<?= admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            action: 'remove_cart_item',
                            cart_item_key: cartItemKey
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Удаляем товар из DOM
                            li.remove();

                            // Обновляем итоги
                            document.querySelectorAll('.cart-total').forEach(el => el
                                .innerHTML = data.data.cart_total);
                            document.querySelectorAll('.cart-count').forEach(el => el
                                .innerHTML = data.data.cart_count);

                            // Проверяем, осталась ли корзина пустой
                            const remainingItems = document.querySelectorAll(
                                'li[data-cart-item-key]');
                            if (remainingItems.length === 0) {
                                // Перезагружаем страницу, чтобы показать пустую корзину
                                window.location.reload();
                            }
                        } else {
                            console.error('Ошибка удаления товара:', data.data);
                            alert('Ошибка: ' + data.data);
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка:', error);
                        alert('Произошла ошибка при удалении товара');
                    })
                    .finally(() => {
                        btn.disabled = false;
                    });
            });
        });
    });
</script><?php get_footer(); ?>