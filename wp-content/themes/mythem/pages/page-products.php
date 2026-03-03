<?php
/* Template Name: Категория записей */

get_header(); ?>
<div class="bg-el">
    <div class="main-container page-company">
        <?php custom_breadcrumbs(); ?>
        <section class="o-kompanii container">
            <h1 class="mb-2 md:mb-6"><?php the_title(); ?></h1>
            <div class="subtitle mb-16 md:mb-10 md:whitespace-pre-line">
                <p class="font-semibold text-lg md:text-2xl"><?= the_field('sub-title'); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                <div class="content fade-left">
                    <?php the_field('opisanie'); ?>
                </div>
                <?php
                $image = get_field('img');
                $file = get_field('file');
                if (isset($image)):
                ?>
                    <img class="md w-full row-span-2 fade-right" src="<?= esc_url($image['url']) ?>" data-zoom
                        alt=" <?= esc_attr($image['alt']) ?>">
                <?php endif;
                if (isset($file)): ?>
                    <a class='btn' download href="<?= esc_url($file); ?>">Скачать направление</a>
                <?php endif; ?>
            </div>
        </section>
        <?php
        $galery = get_field('galereya');
        if ($galery): ?>
            <section>
                <h2>Выдаваемые документы после окончания обучения</h2>
                <div class="galery__inner grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($galery as $image) : ?>
                        <div class="galery__item">
                            <img src="<?= esc_url($image['url']); ?>" alt="<?= esc_attr($image['alt'] ?: 'Документ'); ?>"
                                class="w-full h-auto object-cover" loading="lazy" data-zoom>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?= get_template_part('template-parts/temp-part-table'); ?>

    </div>
</div>
<?php

get_footer();
