<?php
get_header();

$current_category = get_queried_object();
$subcategories = get_terms([
    'taxonomy' => 'product_cat',
    'parent' => $current_category->term_id,
    'hide_empty' => false,
]);
if ($subcategories && !is_wp_error($subcategories)) : ?>

<div class="main-container">
    <?php custom_breadcrumbs(); ?>

    <h1><?= esc_html($current_category->name) ?></h1>
    <div class="category-products__grid lg:grid  lg:grid-cols-3 flex flex-wrap justify-between gap-3 md:gap-6">
        <?php foreach ($subcategories as $category) :
                $image_data = mytheme_get_category_image($category, 'medium');
            ?>
        <div class="category-product flex flex-col  md:p-10 p-5 rounded-lg md:h-[326px] h-[220px] bg-white">
            <a href="<?= esc_url(get_term_link($category)); ?>"
                class="category-product__link flex justify-between flex-col h-full items-center">
                <img src="<?= esc_url($image_data['url']); ?>" alt="<?= esc_attr($image_data['alt']); ?>"
                    class="category-product__image object-contain max-w-full md:max-w-[220px]">
                <h3 class="category-product__title text-center"><?= esc_html($category->name); ?></h3>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php else: ?>
<?php get_template_part('woocommerce/archive-product'); // Используем стандартный шаблон для вывода товаров 
    ?>
<?php endif;
get_footer();