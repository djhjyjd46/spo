<?php

get_header(); ?>
<div class=" bg-el">
    <div class="main-container page-single">
                <?php custom_breadcrumbs(); ?>
        <h1><?php the_title(); ?></h1>
        <div class="content">
            <?php
            while (have_posts()) : the_post();
                the_content();
            endwhile;
            ?>
        </div>
        <section class="news">
            <div class="container">
                <h2 class="section-title"><?= the_field('blog-title'); ?></h2>
                <div class="news grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6 mb-8 md:mb-16">
                    <?php
                    $news_posts = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 3,
                    ));
    
                    if ($news_posts->have_posts()) :
                        while ($news_posts->have_posts()) : $news_posts->the_post(); ?>
                    <div class="news__item relative overflow-hidden  group news__item rounded-2xl">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                            <?php
                                        $thumb_id = get_post_thumbnail_id();
                                        $thumb_url = wp_get_attachment_image_url($thumb_id, 'medium');
                                        $thumb_alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
                                        ?>
                            <img src="<?= esc_url($thumb_url); ?>" alt="<?= esc_attr($thumb_alt ?: get_the_title()); ?>"
                                class="w-full h-full object-cover absolute inset-0 z-0" loading="lazy">
    
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-black/60"></div>
                            <div class="news__content relative z-10 text-white p-4 md:p-5 h-full flex flex-col justify-end">
                                <div class="mb-5"><?= esc_html(get_the_date('d.m.Y')); ?></div>
                                <h3 class=""><?= esc_html(get_the_title()); ?></h3>
                            </div>
                        </a>
                    </div>
                    <?php endwhile;
                    endif;
                    wp_reset_postdata();
                    ?>
                </div>
                <div class="w-full flex justify-center">
                    <a href="<?= esc_url(home_url('/blog/')); ?>" class="button">Вернуться к статьям</a>
                </div>
            </div>
        </section>
    </div>
</div>
<?php get_footer();