<?php
/* Template Name: Категории услуг */

get_header(); ?>
<div class="bg-el">
    <div class="main-container page-category">
        <?php custom_breadcrumbs(); ?>
        <h1><?php the_title(); ?></h1>
        <div class="cards grid grid-col-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
            <?php
            $parent_id = get_the_ID();
            $pages = get_pages([
                'parent' => $parent_id, // только непосредственные дочерние
                'sort_column' => 'menu_order',
                'sort_order' => 'ASC',
                'post_status' => 'publish',
                'number' => 0,
            ]);

            $children = [];
            if (!empty($pages) && !is_wp_error($pages)) {
                foreach ($pages as $p) {
                    $children[] = [
                        'id' => $p->ID,
                        'title' => get_the_title($p->ID),
                        'excerpt' => get_the_excerpt($p->ID),
                        'link' => get_permalink($p->ID),
                        'thumb' => get_the_post_thumbnail_url($p->ID, 'full') ?: '',
                    ];
                }
            }

            if (!empty($children)) :
                foreach ($children as $i => $child) :
                    // Безопасный вывод
                    $c_link = esc_url($child['link']);
                    $c_title = esc_html($child['title']);
                    $c_thumb = $child['thumb'] ? esc_url($child['thumb']) : '';
                    $delay = sprintf('%.2fs', $i * 0.1);

            ?>
                    <div class="card relative w-full h-auto rounded-2xl overflow-hidden shadow-lg fade-up"
                        style="--anim-delay: <?= esc_attr($delay) ?>;">
                        <a href="<?= $c_link ?>" class="block h-full">
                            <?php if ($c_thumb) : ?>
                                <div class="imgcont w-full h-auto overflow-hidden max-h-[180px] md:max-h-[220px]">
                                    <img class="w-full h-auto bg-center" src="<?= $c_thumb ?>"
                                        alt="<?= esc_attr($child['title']) ?>" loading="lazy">
                                </div>
                            <?php endif; ?>

                            <div class="flex justify-between w-full items-center h-15 bg-[#FDFDFD] px-4 md:px-6">
                                <span class="font-semibold text-base md:text-lg"><?= $c_title ?></span>
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="40" height="40" rx="20" fill="#007DC6" />
                                    <path
                                        d="M15.8384 14.3432C15.8385 13.9896 15.979 13.6506 16.229 13.4006C16.479 13.1506 16.818 13.0101 17.1716 13.01L25.6569 13.01C26.0104 13.0101 26.3495 13.1506 26.5995 13.4006C26.8495 13.6506 26.9899 13.9896 26.99 14.3432L26.99 22.8284C26.9839 23.178 26.8408 23.5112 26.5914 23.7562C26.3421 24.0013 26.0065 24.1386 25.6569 24.1386C25.3072 24.1386 24.9716 24.0013 24.7223 23.7562C24.4729 23.5112 24.3298 23.178 24.3237 22.8284L24.2426 17.643L15.286 26.5997C15.0359 26.8497 14.6968 26.9902 14.3431 26.9902C13.9895 26.9902 13.6504 26.8497 13.4003 26.5997C13.1503 26.3496 13.0098 26.0105 13.0098 25.6569C13.0098 25.3032 13.1503 24.9641 13.4003 24.7141L22.357 15.7574L17.1716 15.6763C16.818 15.6762 16.479 15.5358 16.229 15.2858C15.979 15.0358 15.8385 14.6967 15.8384 14.3432Z"
                                        fill="#FDFDFD" />
                                </svg>

                            </div>
                        </a>
                    </div>
                <?php endforeach;
            else : ?>
                <p class="no-children">Курсы не найдены.</p>
            <?php endif; ?>
        </div>
        <!-- <?= get_template_part('template-parts/temp-part-table'); ?>
        <?= get_template_part('template-parts/temp-part-banner'); ?> -->
        <?= get_template_part('template-parts/temp-part-preim'); ?>
    </div>
</div>
<?php get_footer();