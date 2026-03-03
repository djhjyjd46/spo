<?php

/**
 * Кастомная карточка товара для популярных товаров
 */

defined('ABSPATH') || exit;

global $product;

// Проверяем что у нас есть объект товара
if (empty($product) || !$product->is_visible()) {
    return;
}

$product_id = $product->get_id();
$product_url = apply_filters('woocommerce_loop_product_link', get_the_permalink(), $product);
?>

<div class="product-card">
    <!-- Изображение товара -->
    <div class="card-top">

        <div class="product-card__image">
            <a href="<?= esc_url($product_url); ?>">
                <?= woocommerce_get_product_thumbnail('medium'); ?>
            </a>
        </div>
        <!-- Название товара -->
        <h3 class="product-card__title">
            <a href="<?= esc_url($product_url); ?>">
                <?= esc_html(get_the_title()); ?>
            </a>
        </h3>
        <!-- Отображение наличия -->
    </div>

    <!-- Кнопки действий -->
    <div class="card-botom flex flex-col gap-2">

        <div class="product-card__stock">
            <?php
            $stock_status = $product->get_stock_status();
            $stock_quantity = $product->get_stock_quantity();

            if ($stock_status === 'instock') {
                if ($stock_quantity && $product->managing_stock()) {
                    echo '<span class="stock-status stock-status--instock">В наличии: ' . esc_html($stock_quantity) . ' шт.</span>';
                } else {
                    echo '<span class="stock-status stock-status--instock">В наличии</span>';
                }
            } elseif ($stock_status === 'outofstock') {
                echo '<span class="stock-status stock-status--outofstock">Нет в наличии</span>';
            } elseif ($stock_status === 'onbackorder') {
                echo '<span class="stock-status stock-status--backorder">Под заказ</span>';
            }
            ?>
        </div>
        <div class="product-card__actions flex flex-col justify-between w-full">
            <div class="price-cart flex justify-between mb-2">
                <!-- Цена -->
                <div class="product-card__price">
                    <?php
                    $price_html = $product->get_price_html();
                    $regular_price = $product->get_regular_price();

                    // Если цена не указана или равна 0, выводим текст
                    if (empty($regular_price) || (float)$regular_price == 0) : ?>
                        <span class="price-not-specified">Цена не указана</span>
                    <?php else : ?>
                        <?= $price_html ?>
                    <?php endif; ?>
                </div>
                <!-- Кнопка добавления в корзину -->
                <?php if ($product->is_purchasable() && $product->is_in_stock()) : ?>
                    <form method="post" action="" class="product-card__cart-form">
                        <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                        <input type="hidden" name="add-to-cart" value="<?= esc_attr($product_id); ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="button-cart button--transparent">
                            <img src="<?= esc_url(get_template_directory_uri() . '/images/icons/cart.png'); ?>"
                                alt="корзина">
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <!-- Кнопка подробнее -->
            <a href="<?= esc_url($product_url); ?>" class="button">
                Подробнее
            </a>
        </div>
    </div>

</div>