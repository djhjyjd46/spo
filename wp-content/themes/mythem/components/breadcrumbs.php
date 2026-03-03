<?php
/**
 * Универсальные хлебные крошки: WooCommerce и кастомные
 */

// Отключаем стандартные крошки WooCommerce, если нужно
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

/**
 * Главная функция — выводит крошки в зависимости от типа страницы
 */
function custom_breadcrumbs() {
    echo '<div class="breadcrumbs">';

    if (function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page())) {
        // WooCommerce-крошки
        woocommerce_breadcrumb([
            'delimiter'    => ' > ',
            'wrap_before'  => '',
            'wrap_after'   => '',
            'before'       => '',
            'after'        => '',
        ]);
    } elseif (is_page()) {
        render_page_breadcrumbs(' > ');
    } elseif (is_single() && !is_singular('product')) {
        render_post_breadcrumbs(' > ');
    } else {
        render_misc_breadcrumbs(' > ');
    }

    echo '</div>';
}

/**
 * Крошки для обычных страниц
 */
function render_page_breadcrumbs($separator) {
    global $post;
    echo '<a href="' . home_url('/') . '">Главная</a>';

    if ($post->post_parent) {
        $ancestors = array_reverse(get_post_ancestors($post));
        foreach ($ancestors as $ancestor_id) {
            echo $separator . '<a href="' . get_permalink($ancestor_id) . '">' . get_the_title($ancestor_id) . '</a>';
        }
    }

    echo $separator . '<span>' . get_the_title() . '</span>';
}

/**
 * Крошки для записей
 */
function render_post_breadcrumbs($separator) {
    echo '<a href="' . home_url('/') . '">Главная</a>';

    $categories = get_the_category();
    if (!empty($categories)) {
        echo $separator . '<a href="' . get_category_link($categories[0]->term_id) . '">' . $categories[0]->name . '</a>';
    }

    echo $separator . '<span>' . get_the_title() . '</span>';
}

/**
 * Крошки для спецстраниц (категория, поиск, 404 и т.д.)
 */
function render_misc_breadcrumbs($separator) {
    echo '<a href="' . home_url('/') . '">Главная</a>';

    if (is_category()) {
        echo $separator . '<span>' . single_cat_title('', false) . '</span>';
    } else
    if (is_home()) {
        $blog_page_id = get_option('page_for_posts');
        echo $separator . '<span>' . ($blog_page_id ? get_the_title($blog_page_id) : 'Блог') . '</span>';
    } elseif (is_search()) {
        echo $separator . '<span>Поиск: ' . get_search_query() . '</span>';
    } elseif (is_404()) {
        echo $separator . '<span>Страница не найдена</span>';
    }
}
