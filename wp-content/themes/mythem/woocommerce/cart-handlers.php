<?php

/**
 * AJAX обработчики для операций с корзиной WooCommerce
 */

defined('ABSPATH') || exit;

// Обработчик обновления количества товара в корзине (для страницы корзины)
function ajax_update_cart_quantity_handler()
{
    $cart_item_key = sanitize_text_field($_POST['cart_item_key'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);

    if (empty($cart_item_key) || $quantity < 0) {
        wp_send_json_error('Неверные параметры');
    }

    // Обновляем количество
    if ($quantity > 0) {
        WC()->cart->set_quantity($cart_item_key, $quantity, true);
    } else {
        WC()->cart->remove_cart_item($cart_item_key);
    }

    // Получаем обновленные данные товара
    $cart_item = WC()->cart->get_cart_item($cart_item_key);
    $item_total = '';
    if ($cart_item && $quantity > 0) {
        $item_total = wc_price($cart_item['line_total']);
    }

    // Получаем обновленные данные корзины
    $cart_data = array(
        'cart_total' => WC()->cart->get_cart_total(),
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_subtotal' => WC()->cart->get_cart_subtotal(),
        'item_total' => $item_total
    );

    wp_send_json_success($cart_data);
}

// Обработчик обновления количества товара в корзине
function ajax_update_cart_item_handler()
{
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'cart_nonce')) {
        wp_die('Security check failed');
    }

    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($product_id <= 0 || $quantity < 0) {
        wp_send_json_error('Неверные параметры');
    }

    // Находим товар в корзине
    $cart_item_key = null;
    foreach (WC()->cart->get_cart() as $key => $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            $cart_item_key = $key;
            break;
        }
    }

    if (!$cart_item_key) {
        wp_send_json_error('Товар не найден в корзине');
    }

    // Обновляем количество
    if ($quantity > 0) {
        WC()->cart->set_quantity($cart_item_key, $quantity, true);
    } else {
        WC()->cart->remove_cart_item($cart_item_key);
    }

    // Получаем обновленные данные корзины
    $cart_data = array(
        'cart_total' => WC()->cart->get_cart_total(),
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_subtotal' => WC()->cart->get_cart_subtotal(),
        'fragments' => array()
    );

    wp_send_json_success($cart_data);
}

// Обработчик удаления товара из корзины
function ajax_remove_cart_item_handler()
{
    $cart_item_key = sanitize_text_field($_POST['cart_item_key'] ?? '');

    if (empty($cart_item_key)) {
        wp_send_json_error('Неверный ключ товара');
    }

    // Удаляем товар из корзины
    $removed = WC()->cart->remove_cart_item($cart_item_key);

    if ($removed) {
        // Получаем обновленные данные корзины
        $cart_data = array(
            'cart_total' => WC()->cart->get_cart_total(),
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_subtotal' => WC()->cart->get_cart_subtotal(),
            'fragments' => array()
        );

        wp_send_json_success($cart_data);
    } else {
        wp_send_json_error('Ошибка при удалении товара');
    }
}

// Обработчик получения мини-корзины
function ajax_get_mini_cart_handler()
{
    ob_start();
    woocommerce_mini_cart();
    $mini_cart = ob_get_clean();

    $cart_data = array(
        'mini_cart' => $mini_cart,
        'cart_total' => WC()->cart->get_cart_total(),
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_subtotal' => WC()->cart->get_cart_subtotal()
    );

    wp_send_json_success($cart_data);
}

// Регистрируем AJAX обработчики
add_action('wp_ajax_update_cart_quantity', 'ajax_update_cart_quantity_handler');
add_action('wp_ajax_nopriv_update_cart_quantity', 'ajax_update_cart_quantity_handler');

add_action('wp_ajax_update_cart_item', 'ajax_update_cart_item_handler');
add_action('wp_ajax_nopriv_update_cart_item', 'ajax_update_cart_item_handler');

add_action('wp_ajax_remove_cart_item', 'ajax_remove_cart_item_handler');
add_action('wp_ajax_nopriv_remove_cart_item', 'ajax_remove_cart_item_handler');

add_action('wp_ajax_get_mini_cart', 'ajax_get_mini_cart_handler');
add_action('wp_ajax_nopriv_get_mini_cart', 'ajax_get_mini_cart_handler');

// Добавляем поддержку AJAX для стандартных операций WooCommerce
function enable_ajax_add_to_cart()
{
    // Включаем AJAX добавление в корзину для простых товаров
    add_filter('woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');
}
add_action('init', 'enable_ajax_add_to_cart');

// Фрагмент для обновления счетчика корзины в шапке
function woocommerce_header_add_to_cart_fragment($fragments)
{
    ob_start();
    $count = WC()->cart->get_cart_contents_count();
?>
    <span class="cart-count"><?= $count ?></span>
<?php
    $fragments['.cart-count'] = ob_get_clean();

    return $fragments;
}

// Обеспечиваем правильную работу корзины для незарегистрированных пользователей
function ensure_cart_session()
{
    if (is_admin()) return;

    global $woocommerce;
    if ($woocommerce && $woocommerce->session && method_exists($woocommerce->session, 'has_session')) {
        if (!WC()->session->has_session()) {
            WC()->session->set_customer_session_cookie(true);
        }
    }
}
add_action('wp_loaded', 'ensure_cart_session');

// Добавляем nonce для безопасности AJAX запросов корзины
function add_cart_nonce_to_js()
{
    if (is_cart()) {
        wp_localize_script('cart-handler', 'cart_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cart_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'add_cart_nonce_to_js', 20);
