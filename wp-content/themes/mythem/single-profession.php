<?php

$profession = get_field('professiya');
$galery = get_field('galery');


get_header(); ?>
<div class="bg-el">
    <div class="main-container page-profession">
        <?php custom_breadcrumbs(); ?>
        <h1 class="mb-1 md:mb-0"><?php the_title(); ?></h1>
        <section class="professiya flex flex-col md:flex-row-reverse gap-4 md:gap-6">
            <img class="prof-img fade-right" src="<?= esc_url($profession['img']['url']); ?>"
                alt="<?= esc_attr($profession['img']['alt']); ?>">
            <div class="profession-info flex flex-col gap-6 justify-between fade-left">
                <div class="profession-info__item">
                    <span class="profession-info__item-title">Код профессии:</span>
                    <span class="profession-info__item-text"><?= esc_html($profession['kod_professii']); ?></span>
                </div>
                <div class="profession-info__item">
                    <span class="profession-info__item-title">Срок обучения:</span>
                    <span class="profession-info__item-text"><?= esc_html($profession['srok_obucheniya']); ?></span>
                </div>
                <div class="profession-info__item">
                    <span class="profession-info__item-title">Стоимость:</span>
                    <span class="profession-info__item-text"><?= esc_html($profession['stoimost']); ?></span>
                </div>
                <div class="profession-info__item">
                    <span class="profession-info__item-title">Форма обучения:</span>
                    <span class="profession-info__item-text"><?= esc_html($profession['forma_obucheniya']); ?></span>
                </div>
                <div class="profession-info__item">
                    <span class="profession-info__item-title">Документы по окончанию обучения:</span>
                    <span
                        class="profession-info__item-text"><?= esc_html($profession['dokumenty_po_okonchaniyu_obucheniya']); ?></span>
                </div>
                <div class="profession-info__item">
                    <span class="profession-info__item-title">Программа обучения включает:</span>
                    <span
                        class="profession-info__item-text"><?= esc_html($profession['programma_obucheniya_vklyuchaet']); ?></span>
                </div>
            </div>
        </section>
        <?php
         if (have_posts()) :
        ?>
        <section class="content">
            <?php
            while (have_posts()) : the_post();
                the_content();
            endwhile;
            ?>
        </section>
        <?php
        endif;
        ?>
        <?php if(isset($galery)) :?>
        <section class="galery">
            <h2 class="section-title"><?= esc_html($galery['title']); ?></h2>
            <div class="galery__inner grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($galery['dokumenty'] as $image) : ?>
                    <div class="galery__item">
                        <img src="<?= esc_url($image['url']); ?>" alt="<?= esc_attr($image['alt'] ?: 'Документ'); ?>"
                            class="w-full h-auto object-cover" loading="lazy" data-zoom>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        <?= get_template_part('template-parts/temp-part-table'); ?>
        <?= get_template_part('template-parts/temp-part-preim'); ?>

    </div>

</div>
<style>
    .prof-img {
        max-width: 486px;
        max-height: 345px;
        border-radius: 16px;
    }

    .profession-info__item {
        display: grid;
        grid-template-columns: 1fr 1fr;
        font-size: 18px;
    }

    .profession-info__item-title {
        font-weight: 600;
    }

    .profession-info__item-text {
        font-weight: 500;
        opacity: 0.8;
        text-align: right;
    }

    @media (max-width: 767px) {
        .profession-info__item {
            font-size: 14px;
        }


    }
</style>
<?php get_footer(); ?>