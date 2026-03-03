<?php
get_header(); ?>

<div class="main-container articles">
    <div class="container">
        <?php custom_breadcrumbs(); ?>
        <h1><?php single_cat_title(); ?></h1>

        <?php
        // Описание категории если есть
        $category_description = category_description();
        if (!empty($category_description)) : ?>
            <div class="category-description mb-8">
                <?= $category_description ?>
            </div>
        <?php endif; ?>

        <div class="news grid grid-col-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6 mb-8 md:mb-16">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <div class="news__item relative overflow-hidden  group news__item">
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
                            <div class="news__content relative z-10 text-white p-4 h-full flex flex-col justify-end">
                                <div class=""><?= esc_html(get_the_date('d.m.Y')); ?></div>
                                <h3 class=""><?= esc_html(get_the_title()); ?></h3>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>

                <?php
                global $wp_query;
                if ($wp_query->max_num_pages > 1) : ?>
                    <div class="pagination w-full flex justify-center mt-8">
                        <?php
                        echo paginate_links(array(
                            'prev_text' => 'Предыдущая',
                            'next_text' => 'Следующая',
                        ));
                        ?>
                    </div>
                <?php endif; ?>

            <?php else : ?>
                <div class="no-posts w-full text-center py-20">
                    <h2 class="text-2xl mb-4">В этой категории пока нет записей</h2>
                    <p class="mb-6">Записи появятся здесь позже.</p>
                    <a href="<?= home_url('/novosti/') ?>" class="button">Вернуться к блогу</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>