<?php

/**
 * AJAX обработчики для WooCommerce фильтрации и сортировки
 */

defined('ABSPATH') || exit;

// Обработчик AJAX фильтрации товаров
function ajax_filter_products_handler()
{
    // Проверяем nonce для безопасности
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'ajax_filter_nonce')) {
        wp_die('Security check failed');
    }

    $category = sanitize_text_field($_POST['category'] ?? '');
    $orderby = sanitize_text_field($_POST['orderby'] ?? '');
    $paged = intval($_POST['paged'] ?? 1);
    $append = filter_var($_POST['append'] ?? false, FILTER_VALIDATE_BOOLEAN);

    // Параметры для WP_Query
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 8, // Количество товаров на страницу
        'paged' => $paged,
        'meta_query' => array(
            array(
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '='
            )
        )
    );

    // Добавляем фильтр по категории если указан
    if (!empty($category)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $category
            )
        );
    }

    // Добавляем сортировку
    switch ($orderby) {
        case 'price-asc':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            break;
        case 'price-desc':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'popularity':
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
    }

    // Выполняем запрос
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        ob_start();

        while ($query->have_posts()) {
            $query->the_post();
            global $product;
            $product = wc_get_product(get_the_ID());

            echo '<li class="products__item">';
            get_template_part('woocommerce/product-card');
            echo '</li>';
        }

        $html = ob_get_clean();
        wp_reset_postdata();

        // Определяем, есть ли еще товары
        $has_more = $query->max_num_pages > $paged;

        wp_send_json_success(array(
            'html' => $html,
            'has_more' => $has_more,
            'current_page' => $paged,
            'max_pages' => $query->max_num_pages,
            'total_products' => $query->found_posts
        ));
    } else {
        wp_send_json_success(array(
            'html' => '',
            'has_more' => false,
            'current_page' => $paged,
            'max_pages' => 0,
            'total_products' => 0
        ));
    }
}

// Регистрируем AJAX обработчики
add_action('wp_ajax_ajax_filter_products', 'ajax_filter_products_handler');
add_action('wp_ajax_nopriv_ajax_filter_products', 'ajax_filter_products_handler');

// Хук для модификации основного запроса товаров на странице каталога
function modify_product_query($query)
{
    if (!is_admin() && $query->is_main_query()) {
        // Защита: вызовы условных функций WooCommerce могут отсутствовать если плагин отключен
        $is_shop = function_exists('is_shop') && is_shop();
        $is_product_cat = function_exists('is_product_category') && is_product_category();

        if ($is_shop || $is_product_cat) {
            // Обрабатываем кастомные параметры сортировки
            $orderby = get_query_var('orderby');

            switch ($orderby) {
                case 'price-asc':
                    $query->set('meta_key', '_price');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'ASC');
                    break;
                case 'price-desc':
                    $query->set('meta_key', '_price');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                case 'popularity':
                    $query->set('meta_key', 'total_sales');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
            }
        }
    }
}
add_action('pre_get_posts', 'modify_product_query');
