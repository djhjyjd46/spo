<?php
/* Template Name: Страница записей */

get_header(); ?>
<div class="bg-el main-container articles">
    <div class="container">
        <?php custom_breadcrumbs(); ?>
        <h1><?php the_title(); ?></h1>
        <div class="news grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6 mb-8 md:mb-16">
            <?php
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

            $news_posts = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 9,
                'paged' => $paged
            ));

            if ($news_posts->have_posts()) :
                $i = 0;
                while ($news_posts->have_posts()) : $news_posts->the_post();
                    $delay_class = 'delay-' . min(10, ++$i);
            ?>
                    <div
                        class="news__item relative overflow-hidden group news__item rounded-2xl fade-up <?= esc_attr($delay_class) ?>">
                        <a href="<?= esc_url(get_permalink()); ?>">
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
                            <div class="news__content relative z-10 text-white p-4 h-full flex flex-col justify-end">
                                <div class=""><?= esc_html(get_the_date('d.m.Y')); ?></div>
                                <h3 class=""><?= esc_html(get_the_title()); ?></h3>
                            </div>
                        </a>
                    </div>
                <?php endwhile;

                // Пагинация
                if ($news_posts->max_num_pages > 1) : ?>
                    <div class="pagination w-full flex justify-center mt-8">
                        <?php
                        echo paginate_links(array(
                            'total' => $news_posts->max_num_pages,
                            'current' => $paged,
                            'prev_text' => 'Предыдущая',
                            'next_text' => 'Следующая',
                        ));
                        ?>
                    </div>
            <?php endif;

            endif;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</div>
</section>

<?php get_footer(); ?>