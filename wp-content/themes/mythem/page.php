<?php
/*

*/
get_header();
?>
<div class="main-container page-default">
    <?php if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
    <h1><?php the_title(); ?></h1>
    <div class="content">
        <?php
        while (have_posts()) : the_post();
            the_content();
        endwhile;
        ?>
    </div>
    <?= get_template_part('template-parts/temp-part-steps'); ?>
    <?= get_template_part('template-parts/temp-part-preim'); ?>
</div>
<?php get_footer(); ?>