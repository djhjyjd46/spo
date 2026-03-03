<?php

/**
 * Основные настройки и поддержка функций темы
 */

/**
 * Отключаем админ панель для фронтенда
 */
show_admin_bar(false);

/**
 * Добавляем поддержку WooCommerce
 */
function mytheme_add_woocommerce_support()
{
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'mytheme_add_woocommerce_support');

// Поддержка миниатюр (featured image) для постов и страниц
function mytheme_setup_post_thumbnails()
{
    // Включаем поддержку миниатюр для всех типов постов, можно ограничить ['post','page']
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'mytheme_setup_post_thumbnails');

/**
 * Добавляем поддержку меню
 */
add_theme_support('menus');

/**
 * Добавляем поддержку пользовательских шаблонов страниц
 */
add_action('init', function () {
    add_post_type_support('page', 'excerpt');
});

/**
 * Включаем товары WooCommerce в поиск
 */
function include_woocommerce_in_search($query)
{
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        $query->set('post_type', array('post', 'page', 'product'));
    }
}
add_action('pre_get_posts', 'include_woocommerce_in_search');
