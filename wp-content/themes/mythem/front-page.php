<?php
get_header();
$h1 = get_field('h1');
?>

<h1 class="visually-hidden"><?= the_field('h1'); ?></h1>
<div class="hero relative mt-[50px] md:mt-[148px]">
    <div id="hero-splide" class="hero-slider splide">
        <div class="splide__track">
            <ul class="splide__list">
                <?php
                $slides = get_field('слайдер');
                if ($slides) :
                    foreach ($slides as $i => $slide) :
                        $desktop_bg = esc_url($slide['изображение']['url']);
                        $mobile_bg = !empty($slide['мобильный_фон']['url']) ? esc_url($slide['мобильный_фон']['url']) : $desktop_bg;
                        $slide_id = 'hero-slide-' . $i;
                ?>
                        <li class="splide__slide">
                            <div class="hero-slide" id="<?= $slide_id ?>">
                                <?php $img_attrs = ($i === 0) ? 'fetchpriority="high" loading="eager" decoding="async"' : 'loading="lazy" decoding="async"'; ?>
                                <div class="slide-cont h-full">
                                    <picture class="hero-slide__bg absolute inset-0 w-full h-full">
                                        <source media="(min-width: 768px)" srcset="<?= esc_url($desktop_bg) ?>">
                                        <img src="<?= esc_url($mobile_bg) ?>" alt="" <?= $img_attrs ?>
                                            class="w-full h-full object-cover">
                                    </picture>
                                    <div class="slide-container">
                                        <div class="hero-slide__content fade-left">
                                            <h2 class="h1 hero-slide__title whitespace-pre-line md:whitespace-normal">
                                                <?= esc_html($slide['заголовок']); ?>
                                            </h2>
                                            <p class="hero-slide__text"><?= esc_html($slide['текст']); ?></p>
                                            <a href="<?= esc_url(home_url('/obuchenie/')); ?>" class="button button--hero">Перечень
                                                профессий</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                <?php endforeach;
                endif; ?>
            </ul>
        </div>
    </div>
    <div class="splide__pagination hero-pagination"></div>

</div>
<div class="main py-14 bg-el">
    <section class="category-products">
        <div class="container">
            <h2 class="section-title">Наши Услуги</h2>
            <div class="cards grid grid-col-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6 fade-up">
                <?php
                $uslugi = get_field('uslugi');
                foreach ($uslugi as $item):
                    $page_url = $item['stranicza'];
                    $page_id = url_to_postid($page_url);
                    if ($page_id) :
                        $page_title = get_the_title($page_id);
                        $page_excerpt = get_the_excerpt($page_id);
                        $page_link = get_permalink($page_id);
                        $page_image = get_the_post_thumbnail_url($page_id, 'full');
                ?>
                        <div class="card relative w-full h-auto  md:size-[384px]">
                            <?php if ($page_image) : ?>
                                <img class="w-full rounded-2xl" src="<?= esc_url($page_image); ?>"
                                    alt="<?= esc_attr($page_title); ?>" loading="lazy" decoding="async">
                                <a href="<?= esc_url($page_link); ?>"
                                    class="btn btn--service absolute bottom-[17px] mb:bottom-[14px] left-1/2"><?= $page_title ?></a>

                            <?php endif;
                            ?>
                        </div>
                <?php endif;
                endforeach; ?>
            </div>
        </div>
    </section>
    <?= get_template_part('template-parts/temp-part-banner'); ?>
    <section class="o-kompanii container">
        <h2>О компании</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end ">
            <div class="content fade-left">
                <?php the_field('company_text'); ?>
            </div>
            <img class="md w-full row-span-2 fade-right" src="<?= esc_url(get_field('company_image')['url']) ?>"
                data-zoom alt="<?= esc_attr(get_field('company_image')['alt'] ?: 'компания работников') ?>">
            <a class='btn' href="<?= esc_url(get_page_link('1322')); ?>">Узнать больше о компании</a>
        </div>
    </section>
    <?= get_template_part('template-parts/temp-part-preim'); ?>
    <section class="otzivi container" data-lazy-section>
        <h2 class="otzivi-title">Отзывы о нашей компании</h2>
        <div class="ya-section grid grid-cols-1 md:grid-cols-2 gap-0 zoom-in">
            <div style="width:100%;overflow:hidden;position:relative;"><iframe
                    style="width:100%;border:1px solid #e6e6e6;border-radius:8px;box-sizing:border-box"
                    title="Отзывы о СтройПромОбразование на Яндекс.Картах"
                    src="https://yandex.ru/maps-reviews-widget/213336516882?comments"></iframe><a
                    href="https://yandex.by/maps/org/stroypromobrazovaniye/213336516882/" target="_blank"
                    style="max-width: 100%;">СтройПромОбразование
                    на карте Минска — Яндекс Карты</a>
            </div>
            <div style="position:relative;overflow:hidden;"><iframe
                    title="Карта расположения СтройПромОбразование с отзывами"
                    src="https://yandex.by/map-widget/v1/org/stroypromobrazovaniye/213336516882/reviews/?ll=27.587523%2C53.889476&utm_content=more-reviews&utm_medium=reviews&utm_source=maps-reviews-widget&z=17"
                    width="100%" height="100%" frameborder="1" allowfullscreen="true" +
                    style="position:relative;"></iframe>
            </div>
            <style>
                .ya-section {
                    height: 600px;

                    div iframe {
                        height: 600px;
                    }
                }

                @media (max-width: 767px) {
                    .ya-section {
                        height: auto;

                        div {
                            height: 610px;
                        }
                    }
                }
            </style>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const sections = document.querySelectorAll('[data-lazy-section]');
                if (!sections.length) return;

                // Перенести src -> data-src у всех iframe в lazy секциях
                sections.forEach(section => {
                    section.querySelectorAll('iframe').forEach(iframe => {
                        const src = iframe.getAttribute('src');
                        if (src) {
                            iframe.setAttribute('data-src', src);
                            iframe.removeAttribute('src');
                        }
                    });
                });

                // Загрузить iframe в секции
                const loadSection = (section) => {
                    section.querySelectorAll('iframe[data-src]').forEach(iframe => {
                        if (!iframe.dataset.loaded) {
                            iframe.src = iframe.getAttribute('data-src');
                            iframe.dataset.loaded = '1';
                        }
                    });
                };

                // IntersectionObserver для отслеживания появления секций
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            loadSection(entry.target);
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    rootMargin: '100px'
                });

                sections.forEach(section => observer.observe(section));
            });
        </script>
    </section>
    <?php
    $faq = get_field('faq-voprosy');
    ?>
    <section id="faq" class="faq container">
        <h2><?= the_field('faq-zagolovok'); ?></h2>
        <div class="faq__accordion">
            <?php if ($faq) : ?>
                <div class="service__cards">
                    <?php

                    foreach ($faq as $i => $faqItem) :
                        $delay_class = 'delay-' . min(10, $i + 1);
                    ?>
                        <details name="faq-<?= $i + 1 ?>" class="fade-up <?= esc_attr($delay_class) ?>">
                            <summary>
                                <span class="faq__accordion__title"><?= $faqItem['vopros'] ?></span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M1.85455 0.318218L13.8274 12.2912V4.15912C13.8249 4.01487 13.8511 3.87159 13.9046 3.73761C13.958 3.60362 14.0376 3.48164 14.1387 3.37875C14.2399 3.27586 14.3604 3.19414 14.4935 3.13836C14.6265 3.08258 14.7693 3.05385 14.9136 3.05385C15.0578 3.05385 15.2006 3.08258 15.3337 3.13836C15.4667 3.19414 15.5873 3.27586 15.6884 3.37875C15.7894 3.48164 15.8692 3.60362 15.9225 3.73761C15.976 3.87159 16.0023 4.01487 15.9998 4.15912V14.9138C15.9997 15.2018 15.8852 15.4781 15.6816 15.6818C15.4779 15.8855 15.2016 16 14.9136 16L4.15908 16C3.87426 15.995 3.60279 15.8784 3.40314 15.6753C3.20347 15.4721 3.09159 15.1986 3.09159 14.9138C3.09159 14.6289 3.20347 14.3555 3.40314 14.1523C3.6028 13.9491 3.87426 13.8325 4.15908 13.8276H12.291L0.318189 1.85454C0.114456 1.65081 0 1.37448 0 1.08635C0 0.798219 0.114456 0.521891 0.318189 0.318218C0.521923 0.114449 0.798244 0 1.08637 0C1.37449 0 1.65082 0.114449 1.85455 0.318218Z"
                                        fill="#424242" />
                                </svg>
                            </summary>
                            <div class="faq__content">
                                <p><?= $faqItem['otvet'] ?>
                                </p>
                            </div>
                        </details>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
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
                    $i = 0;
                    while ($news_posts->have_posts()) : $news_posts->the_post();
                        $delay_class = 'delay-' . min(10, ++$i);
                ?>
                        <div
                            class="news__item fade-up relative overflow-hidden group rounded-2xl <?= esc_attr($delay_class) ?>">
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
            <div class="w-full flex justify-center fade-up">
                <a href="<?= esc_url(home_url('/blog/')); ?>" class="button">Смотреть все новости</a>
            </div>
    </section>
</div>


<?php


get_footer();
