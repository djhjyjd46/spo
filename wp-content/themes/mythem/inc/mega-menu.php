<?php

/**
 * Функции для работы с меню навигации
 */

/**
 * Регистрируем меню темы
 */
function mytheme_register_menus()
{
    register_nav_menus(array(
        'primary' => 'Главное меню',
        'main' => 'Основное меню',
        'header' => 'Меню в шапке',
        'footer' => 'Меню в подвале',
    ));
}
add_action('init', 'mytheme_register_menus');

/**
 * Функция для получения категорий товаров для выпадающего меню
 */
function mytheme_get_product_categories_menu()
{
    if (!class_exists('WooCommerce')) {
        return '';
    }

    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => 0, // Только родительские категории
        'number' => 12 // Ограничиваем количество
    ));

    if (empty($categories) || is_wp_error($categories)) {
        return '';
    }

    $output = '<div class="catalog-dropdown absolute top-full left-0 min-w-[320px] z-50">';
    $output .= '<div class="catalog-dropdown__content flex flex-col gap-1">';

    foreach ($categories as $category) {
        $category_link = get_term_link($category);
        if (!is_wp_error($category_link)) {
            // Получаем иконку категории с заглушкой WooCommerce
            $image_data = mytheme_get_category_image($category, 'thumbnail');

            $output .= '<a href="' . esc_url($category_link) . '" class="dropdown-category flex items-center gap-3 text-sm text-gray-700 hover:text-orange transition-colors duration-200 py-2 px-4">';
            $output .= '<img src="' . esc_url($image_data['url']) . '" alt="' . esc_attr($image_data['alt']) . '" class="w-8 h-8 object-cover rounded flex-shrink-0">';
            $output .= '<span>' . esc_html($category->name) . '</span>';
            $output .= '</a>';
        }
    }

    $output .= '</div>';

    return $output;
}

/**
 * Класс Walker для добавления выпадающего меню к каталогу
 */
class Mytheme_Catalog_Walker extends Walker_Nav_Menu
{
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // Проверяем, не находимся ли мы на странице каталога (shop) или категории товаров
        $is_shop_page = (is_shop() || is_product_category() || is_product_tag() || is_product());

        // Проверяем, является ли этот пункт меню ссылкой на каталог
        $is_catalog_item = (
            strpos($item->url, '/shop') !== false ||
            strpos(strtolower($item->title), 'каталог') !== false ||
            strpos(strtolower($item->title), 'catalog') !== false
        );

        // Добавляем класс catalog-menu-item, если это каталог и не страница каталога
        if ($is_catalog_item && !$is_shop_page) {
            $classes[] = 'catalog-menu-item';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= '<li' . $id . $class_names . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        $link_before = isset($args->link_before) ? $args->link_before : '';
        $link_after = isset($args->link_after) ? $args->link_after : '';

        $output .= '<a' . $attributes . '>';
        $output .= $link_before . apply_filters('the_title', $item->title, $item->ID) . $link_after;

        // Добавляем иконку стрелки для каталога, если это каталог и не страница каталога
        if ($is_catalog_item && !$is_shop_page) {
            $output .= '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="catalog-arrow ml-1 transition-transform duration-200">
<path d="M1 6L6 11L11 6" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/>
</svg>';
        }

        $output .= '</a>';

        // Добавляем выпадающее меню, если это каталог и не страница каталога
        if ($is_catalog_item && !$is_shop_page) {
            $output .= mytheme_get_product_categories_menu();
        }
    }

    public function end_el(&$output, $item, $depth = 0, $args = null)
    {
        $output .= "</li>\n";
    }
}
