<?php

/**
 * Подключение скриптов и стилей темы
 */

/**
 * Основная функция подключения скриптов и стилей
 */
function mytheme_enqueue_scripts()
{
    // Подключение Swiper
    wp_enqueue_style('splide-css', get_template_directory_uri() . '/js/splide/splide.min.css', array(), '1.0');
    wp_enqueue_script('splide-js', get_template_directory_uri() . '/js/splide/splide.min.js', array(), '1.0', true);

    // Подключение основных скриптов темы
    // wp_enqueue_script('swiper-init', get_stylesheet_directory_uri() . '/js/modules/slider-init.js', array('swiper-js'), '1.0', true);
    // wp_enqueue_script('mobile-menu', get_template_directory_uri() . '/js/modules/mobile-menu.js', array(), null, true);
    // wp_enqueue_script('modal-open', get_template_directory_uri() . '/js/modules/modal-open.js', array(), null, true);
    // wp_enqueue_script('modal-image', get_template_directory_uri() . '/js/modules/modal-image.js', array(), null, true);
    // wp_enqueue_script('fade-up', get_template_directory_uri() . '/js/modules/fade-up.js', array(), '1.0', true);
    // wp_enqueue_script('cart-notification', get_template_directory_uri() . '/js/modules/cart-notification.js', array(), '1.0', true);
    // Подключаем скрипт для выпадающего меню каталога
    // wp_enqueue_script('catalog-dropdown', get_template_directory_uri() . '/js/modules/catalog-dropdown.js', array(), '1.0', true);
    
    // Добавляем переменные для cart-handler.js
    if (function_exists('WC')) {
            wp_enqueue_script('cart-handler', get_template_directory_uri() . '/woocommerce/js/cart-handler.js', array(), '1.0.0', true);
            wp_localize_script('cart-handler', 'cart_ajax_object', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('cart_nonce')
    
            ));
           
        }
    
    // Подключение основных стилей
    wp_enqueue_style('mythem-style-tailwind', get_template_directory_uri() . '/css/tailwind.css', array(), '1.0');
    wp_enqueue_style('mythem-style', get_template_directory_uri() . '/css/styles.css', array(), '1.0');
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_scripts');

/**
 * Подключение минифицированного JS файла
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'mytheme-all-js',
        get_template_directory_uri() . '/js/modules/all.min.js',
        [],
        null,
        true // подключить в footer
    );
});
