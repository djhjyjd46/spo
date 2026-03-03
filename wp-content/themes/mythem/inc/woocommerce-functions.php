<?php

// Подключаем AJAX обработчики для WooCommerce только если плагин активен
if (function_exists('mytheme_is_woocommerce_active') && mytheme_is_woocommerce_active()) {
    // Подключаем реальные обработчики
    require_once get_template_directory() . '/woocommerce/ajax-handlers.php';
    require_once get_template_directory() . '/woocommerce/cart-handlers.php';
} else {
    // WooCommerce неактивен — определяем заглушки, чтобы другие части темы могли вызывать эти функции без фатала
    if (!function_exists('woocommerce_mini_cart')) {
        function woocommerce_mini_cart()
        {
            return '';
        }
    }
}
/**
 * Дополнительные функции для WooCommerce
 */

/**
 * Добавляем поле "Порядковый номер" для категорий товаров WooCommerce
 */
add_action('product_cat_edit_form_fields', function ($term) {
    $order = get_term_meta($term->term_id, 'custom_order', true);
?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="custom_order">Порядковый номер</label></th>
        <td>
            <input type="number" name="custom_order" id="custom_order" value="<?= esc_attr($order) ?>" style="width: 80px;">
            <p class="description">Для ручной сортировки категорий.</p>
        </td>
    </tr>
<?php
});

/**
 * Сохранение поля "Порядковый номер" для категорий товаров
 */
add_action('edited_product_cat', function ($term_id) {
    if (isset($_POST['custom_order'])) {
        update_term_meta($term_id, 'custom_order', intval($_POST['custom_order']));
    }
});
