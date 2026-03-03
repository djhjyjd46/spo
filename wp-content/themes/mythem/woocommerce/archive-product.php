<?php


get_header();

// Получаем общее количество товаров для текущего запроса
global $wp_query;
$total_products = $wp_query->found_posts;

// Подключаем стили и скрипты для AJAX фильтрации
if (function_exists('wc')) {
    wp_enqueue_script('ajax-filter-script', get_template_directory_uri() . '/woocommerce/js/ajax-filter.js', array(), '1.0.1', true);


    // Передаем переменные в JavaScript
    wp_localize_script('ajax-filter-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'shop_url' => get_permalink(wc_get_page_id('shop')),
        'total_products' => $total_products,
        'nonce' => wp_create_nonce('ajax_filter_nonce')
    ));
}
?>

<main class="catalog-page">
    <div class="main-container">
        <?php custom_breadcrumbs(); ?>

        <h1 class="page-title">
            <?php
            if (is_product_category()) {
                single_term_title();
            } else {
                woocommerce_page_title();
            }
            ?>
        </h1>

        <!-- Сортировка -->
        <div class="catalog-sorting">
            <span>Сортировать:</span>
            <ul class="sorting-options">
                <?php
                $current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
                $sorting_options = [
                    'price-desc' => 'Сначала дорогие',
                    'price-asc' => 'Сначала недорогие',
                    'popularity' => 'Сначала популярное',
                ];

                foreach ($sorting_options as $orderby => $label) {
                    $active_class = ($current_orderby === $orderby) ? 'active' : '';
                    echo '<li class="sorting-options__item ' . $active_class . '"><a href="' . add_query_arg('orderby', $orderby) . '">' . $label . '</a></li>';
                }
                ?>
            </ul>
        </div>

        <div class="catalog-wrapper">
            <!-- Фильтры по категориям -->
            <div class="catalog-filters">
                <?php
                $categories = get_terms([
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                    'parent' => 0, // Только корневые категории
                ]);

                // Определение текущей категории
                $current_category = '';

                // Если это страница категории, получаем текущую категорию
                if (is_product_category()) {
                    $queried_object = get_queried_object();
                    if ($queried_object instanceof WP_Term) {
                        $current_category = $queried_object->slug;
                    }
                }
                // Иначе берем из параметра GET
                else if (isset($_GET['category'])) {
                    $current_category = sanitize_text_field($_GET['category']);
                }

                if (!empty($categories) && !is_wp_error($categories)) : ?>
                    <ul class="categories-list">
                        <li class="category-item <?= empty($current_category) ? 'active' : '' ?>">
                            <a href="<?= get_permalink(wc_get_page_id('shop')) ?>">
                                Все категории
                            </a>
                        </li>
                        <?php foreach ($categories as $category) :
                            $is_active = ($current_category === $category->slug);
                        ?>
                            <li class="category-item <?= $is_active ? 'active' : '' ?>">
                                <a href="<?= get_term_link($category) ?>">
                                    <?= $category->name ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Вывод товаров -->
            <div class="catalog-products" data-total-products="<?= $total_products ?>">
                <?php
                if (have_posts()) : ?>
                    <ul class="catalog-productlist">
                        <?php while (have_posts()) : the_post();
                            $product = wc_get_product(get_the_ID()); ?>

                            <li class="products__item">
                                <?php get_template_part('woocommerce/product-card') ?>
                            </li>

                        <?php endwhile; ?>
                    </ul>

                <?php else : ?>
                    <p class="no-products">Товары не найдены.</p>
                <?php endif;
                ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>