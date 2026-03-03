<?php
/* Template Name: О компании */

get_header(); ?>
<div class="bg-el">
    <div class="main-container page-company ">
        <?php custom_breadcrumbs(); ?>
        <section class="o-kompanii container">
            <h1><?php the_title(); ?></h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                <div class="content fade-left">
                    <?php the_field('company_text'); ?>
                </div>
                <img class="md w-full row-span-2 fade-right" src="<?= esc_url(get_field('company_image')['url']) ?>"
                    data-zoom alt=" <?= esc_attr(get_field('company_image')['alt']) ?>">
                <a class='btn' download href="<?= esc_url(get_field('file')); ?>">Скачать направление</a>
            </div>
        </section>

        <?= get_template_part('template-parts/temp-part-steps'); ?>
        <?= get_template_part('template-parts/temp-part-preim'); ?>
    </div>
</div>
<?php get_footer(); ?>