<?php

/**
 * Шаблон страницы поиска
 */

get_header();

// Получаем поисковый запрос
$search_query = get_search_query();
?>

<main class="search-page">
    <div class="main-container">
        <?php custom_breadcrumbs(); ?>

        <h1 class="page-title">
            <?php if (!empty($search_query)) : ?>
                Поиск по запросу: "<?= esc_html($search_query) ?>"
            <?php else : ?>
                Поиск товаров
            <?php endif; ?>
        </h1>

        <?php
        // Используем собственный запрос, чтобы искать и по записям, и по страницам
        $paged = max(1, get_query_var('paged') ? get_query_var('paged') : 1);
        // Получаем все публичные типы, доступные для поиска
        $post_types = get_post_types([
            'public' => true,
            'exclude_from_search' => false,
        ], 'names');
        if (! in_array('post', $post_types, true)) $post_types[] = 'post';
        if (! in_array('page', $post_types, true)) $post_types[] = 'page';

        $args = [
            's' => $search_query,
            'post_type' => $post_types,
            'posts_per_page' => get_option('posts_per_page', 10),
            'paged' => $paged,
        ];

        $search_results = new WP_Query($args);

        if ($search_results->have_posts()) : ?>
            <div class="search-products">
                <ul class="catalog-productlist">
                    <?php while ($search_results->have_posts()) : $search_results->the_post();
                        global $product;

                        $ptype = get_post_type();
                        if ($ptype === 'product' && function_exists('wc_get_product')) {
                            $product = wc_get_product(get_the_ID());
                    ?>
                            <li class="products__item" data-type="<?= esc_attr($ptype) ?>">
                                <?php get_template_part('woocommerce/product-card'); ?>
                            </li>
                        <?php } else { ?>
                            <li class="search-item" data-type="<?= esc_attr($ptype) ?>">
                                <h3><a href="<?= esc_url(get_permalink()) ?>"><?= esc_html(get_the_title()) ?></a></h3>
                                <p><?= esc_html(get_the_excerpt()) ?></p>
                            </li>
                        <?php } ?>
                    <?php endwhile; ?>
                </ul>

                <!-- Пагинация -->
                <div class="pagination">
                    <?php
                    echo paginate_links(array(
                        'base' => esc_url_raw(get_pagenum_link(1)) . '%_%',
                        'format' => (get_option('permalink_structure') ? 'page/%#%/' : '&paged=%#%'),
                        'current' => $paged,
                        'total' => $search_results->max_num_pages,
                        'prev_text' => '← Предыдущая',
                        'next_text' => 'Следующая →',
                    ));
                    ?>
                </div>
            </div>


        <?php else : ?>
            <div class="no-results">
                <p>По вашему запросу "<?= esc_html($search_query) ?>" ничего не найдено.</p>
            </div>
        <?php endif;
        wp_reset_postdata(); ?>
    </div>
</main>

<?php get_footer(); ?>