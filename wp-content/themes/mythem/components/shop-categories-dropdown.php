<?php
$categories = get_terms([
    'taxonomy'   => 'product_cat',
    'parent'     => 0,
    'hide_empty' => false,
]);
?>
<ul class="shop-dropdown bg-white shadow-lg rounded absolute left-0 top-full min-w-[220px] z-50 hidden group-hover:block">
    <?php foreach ($categories as $category) : ?>
        <?php
        $subcats = get_terms([
            'taxonomy'   => 'product_cat',
            'parent'     => $category->term_id,
            'hide_empty' => false,
        ]);
        ?>
        <li class="relative group">
            <a href="<?= esc_url(get_term_link($category)) ?>" class="block px-4 py-2 hover:bg-orange-100"><?= esc_html($category->name) ?></a>
            <?php if ($subcats) : ?>
                <ul class="absolute left-full top-0 min-w-[200px] bg-white shadow-lg rounded hidden group-hover:block">
                    <?php foreach ($subcats as $subcat) : ?>
                        <li>
                            <a href="<?= esc_url(get_term_link($subcat)) ?>" class="block px-4 py-2 hover:bg-orange-100"><?= esc_html($subcat->name) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>