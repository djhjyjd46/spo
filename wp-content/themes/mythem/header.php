<?php

$phones = get_field('телефоны', 'option');
$firsttel = $phones[0]['телефон'];
?>
<!DOCTYPE html>
<html lang="<?= get_bloginfo('language'); ?>">

<head>
    <meta charset="<?= get_bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if (! function_exists('rank_math_the_title')) : ?>
        <title><?= wp_get_document_title(); ?></title>
    <?php endif; ?>
    <?php wp_head(); ?>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <?php
    // -------------------------------------------------------------
    // JSON-LD Schema.org LocalBusiness (динамически из ACF Options)
    // -------------------------------------------------------------
    // Собираем данные: телефоны, email, адрес, график, соцсети
    $schema_phones_raw = get_field('телефоны', 'option');
    $schema_phones = [];
    if ($schema_phones_raw && is_array($schema_phones_raw)) {
        foreach ($schema_phones_raw as $ph) {
            $num = is_array($ph) ? ($ph['телефон'] ?? '') : $ph;
            if ($num) {
                $schema_phones[] = preg_replace('/[^0-9+]/', '', $num);
            }
        }
    }
    $schema_email = get_field('email', 'option');
    // Адрес может быть разбит — если появятся отдельные поля (город, улица и т.п.), можно расширить.
    $schema_address_line = get_field('adress-2', 'option');
    $schema_schedule = get_field('график', 'option'); // Ожидаем строку вида: Пн-Пт 9:00-18:00; Сб 10:00-15:00
    $schema_logo = get_field('logo', 'option');
    $schema_name = get_bloginfo('name');
    $schema_url = home_url('/');
    // Соц.сети
    $schema_social = [];
    $socseti = get_field('соцсети', 'option');
    if ($socseti && is_array($socseti)) {
        foreach ($socseti as $soc) {
            if (!empty($soc['ссылка'])) {
                $schema_social[] = esc_url($soc['ссылка']);
            }
        }
    }

    // Рейтинг бизнеса (статический по запросу: всегда 5 звёзд и 12 отзывов)
    $fixed_rating_value = 5;
    $fixed_rating_count = 12;

    // Преобразование графика работы (простый парсер: ищем пары День и время)
    $openingHoursSpecification = [];
    if ($schema_schedule) {
        // Примеры поддерживаемых форматов: "Пн-Пт 09:00-18:00; Сб 10:00-15:00; Вс выходной"
        $parts = preg_split('/[;\n]+/u', $schema_schedule);
        $mapDays = [
            'пн' => 'Monday', 'вт' => 'Tuesday', 'ср' => 'Wednesday', 'чт' => 'Thursday', 'пт' => 'Friday', 'сб' => 'Saturday', 'вс' => 'Sunday'
        ];
        foreach ($parts as $segment) {
            $segment = trim($segment);
            if (!$segment) continue;
            // Определяем есть ли слово "выход" для выходного дня
            if (preg_match('/^(Пн|Вт|Ср|Чт|Пт|Сб|Вс)(?:-?(Пн|Вт|Ср|Чт|Пт|Сб|Вс))?\s+.*выход/iu', $segment, $m)) {
                // Пропускаем выходные (можно явно добавить если нужно closed)
                continue;
            }
            if (preg_match('/^(Пн|Вт|Ср|Чт|Пт|Сб|Вс)(?:\-(Пн|Вт|Ср|Чт|Пт|Сб|Вс))?\s+([0-9]{1,2}:[0-9]{2})\s*[-–—]\s*([0-9]{1,2}:[0-9]{2})/iu', $segment, $m)) {
                $d1 = mb_strtolower($m[1]);
                $d2 = !empty($m[2]) ? mb_strtolower($m[2]) : null;
                $opens = $m[3];
                $closes = $m[4];
                $daysList = [];
                if ($d2) {
                    // Диапазон дней
                    $keys = array_keys($mapDays);
                    $startIndex = array_search($d1, $keys, true);
                    $endIndex = array_search(mb_strtolower($d2), $keys, true);
                    if ($startIndex !== false && $endIndex !== false && $startIndex <= $endIndex) {
                        for ($i = $startIndex; $i <= $endIndex; $i++) {
                            $daysList[] = $mapDays[$keys[$i]];
                        }
                    }
                } else {
                    $daysList[] = $mapDays[$d1] ?? null;
                }
                $daysList = array_filter($daysList);
                if ($daysList) {
                    foreach ($daysList as $dayName) {
                        $openingHoursSpecification[] = [
                            '@type' => 'OpeningHoursSpecification',
                            'dayOfWeek' => $dayName,
                            'opens' => $opens,
                            'closes' => $closes,
                        ];
                    }
                }
            }
        }
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => $schema_name,
        'url' => $schema_url,
        'image' => $schema_logo ? esc_url($schema_logo) : '',
        'logo' => $schema_logo ? esc_url($schema_logo) : '',
        'telephone' => $schema_phones,
        'email' => $schema_email ?: '',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $schema_address_line ?: ''
        ],
        'openingHoursSpecification' => $openingHoursSpecification,
    ];
    if ($schema_social) {
        $schema['sameAs'] = $schema_social;
    }
    // Статический aggregateRating без зависимостей от ACF
    $schema['aggregateRating'] = [
        '@type' => 'AggregateRating',
        'ratingValue' => (string)$fixed_rating_value,
        'bestRating' => '5',
        'worstRating' => '1',
        'ratingCount' => $fixed_rating_count,
        'reviewCount' => $fixed_rating_count,
    ];
    ?>
    <script type="application/ld+json">
        <?= wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
    <?php
    // -------------------------------------------------------------
    // BreadcrumbList (хлебные крошки) для всех страниц кроме главной
    // -------------------------------------------------------------
    if (!is_front_page()) {
        $breadcrumb_items = [];
        $position = 1;
        // Главная
        $breadcrumb_items[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_bloginfo('name'),
            'item' => home_url('/')
        ];

        // Ветка для различных типов
        if (is_page()) {
            $page_id = get_queried_object_id();
            $ancestors = array_reverse(get_post_ancestors($page_id));
            foreach ($ancestors as $ancestor_id) {
                $breadcrumb_items[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => get_the_title($ancestor_id),
                    'item' => get_permalink($ancestor_id)
                ];
            }
            // Текущая страница
            $breadcrumb_items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => get_the_title($page_id),
                'item' => get_permalink($page_id)
            ];
        } elseif (is_single()) {
            global $post;
            // Категории (для постов / записей блога)
            if ('post' === get_post_type($post)) {
                $cats = get_the_category($post->ID);
                if (!empty($cats)) {
                    // Берём первую категорию как основную
                    $primary_cat = $cats[0];
                    // Родительские категории
                    $cat_ancestors = array_reverse(get_ancestors($primary_cat->term_id, 'category'));
                    foreach ($cat_ancestors as $cat_ancestor_id) {
                        $c = get_category($cat_ancestor_id);
                        if ($c) {
                            $breadcrumb_items[] = [
                                '@type' => 'ListItem',
                                'position' => $position++,
                                'name' => $c->name,
                                'item' => get_category_link($c->term_id)
                            ];
                        }
                    }
                    // Основная категория
                    $breadcrumb_items[] = [
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => $primary_cat->name,
                        'item' => get_category_link($primary_cat->term_id)
                    ];
                }
            } else {
                // Кастомный тип: добавим ссылку на архив если есть
                $pt = get_post_type_object(get_post_type($post));
                if ($pt && !empty($pt->has_archive)) {
                    $breadcrumb_items[] = [
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => $pt->labels->name,
                        'item' => get_post_type_archive_link($pt->name)
                    ];
                }
            }
            // Сам пост
            $breadcrumb_items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => get_the_title($post->ID),
                'item' => get_permalink($post->ID)
            ];
        } elseif (is_category()) {
            $cat = get_queried_object();
            if ($cat && isset($cat->term_id)) {
                $cat_ancestors = array_reverse(get_ancestors($cat->term_id, 'category'));
                foreach ($cat_ancestors as $cat_ancestor_id) {
                    $c = get_category($cat_ancestor_id);
                    if ($c) {
                        $breadcrumb_items[] = [
                            '@type' => 'ListItem',
                            'position' => $position++,
                            'name' => $c->name,
                            'item' => get_category_link($c->term_id)
                        ];
                    }
                }
                $breadcrumb_items[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $cat->name,
                    'item' => get_category_link($cat->term_id)
                ];
            }
        } elseif (is_tax()) {
            $term = get_queried_object();
            if ($term && isset($term->term_id)) {
                $tax_ancestors = array_reverse(get_ancestors($term->term_id, $term->taxonomy));
                foreach ($tax_ancestors as $term_ancestor_id) {
                    $t = get_term($term_ancestor_id, $term->taxonomy);
                    if ($t && !is_wp_error($t)) {
                        $breadcrumb_items[] = [
                            '@type' => 'ListItem',
                            'position' => $position++,
                            'name' => $t->name,
                            'item' => get_term_link($t)
                        ];
                    }
                }
                $breadcrumb_items[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $term->name,
                    'item' => get_term_link($term)
                ];
            }
        } elseif (is_post_type_archive()) {
            $pt = get_queried_object();
            if ($pt && isset($pt->name)) {
                $breadcrumb_items[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $pt->labels->name,
                    'item' => get_post_type_archive_link($pt->name)
                ];
            }
        } elseif (is_search()) {
            $breadcrumb_items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => sprintf('Поиск: %s', get_search_query()),
                'item' => esc_url(home_url(add_query_arg([], $wp->request)))
            ];
        } elseif (is_404()) {
            $breadcrumb_items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => '404',
                'item' => home_url($_SERVER['REQUEST_URI'])
            ];
        }

        $breadcrumb_schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumb_items
        ];
        ?>
        <script type="application/ld+json">
            <?= wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
        </script>
    <?php } ?>
    <link rel="preload" as="font" href="<?= get_template_directory_uri() ?>/fonts/Exo2-Regular.woff2" type="font/woff2"
        crossorigin>
    <link rel="preload" as="font" href="<?= get_template_directory_uri() ?>/fonts/Exo2-Semi-Bold.woff2"
        type="font/woff2" crossorigin>

    <style>
        @font-face {
            font-family: 'Exo2';
            src: url('<?= get_template_directory_uri() ?>/fonts/Exo2-Regular.woff2') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Exo2';
            src: url('<?= get_template_directory_uri() ?>/fonts/Exo2-Semi-Bold.woff2') format('woff2');
            font-weight: 600;
            font-style: normal;
            font-display: swap;
        }

        body {
            font-family: 'Exo2', sans-serif;
        }
    </style>

</head>

<body <?php body_class(); ?>>
   <?php wp_body_open()?> 
    <header class="header hidden md:flex h-[148px] w-full relative transition-all duration-300 ease-in" id="header">
        <span class="absolute w-full h-[2px] bg-black opacity-[0.12] left-0 top-[87px] pointer-events-none "></span>
        <div class="header-container">
            <div class="header-top flex items-center justify-between">
                <a href="<?= home_url(); ?>" class="flex items-center">
                    <img src="<?= the_field('logo', 'option') ?>" alt="logo">
                </a>
                <?= get_template_part('components/input-search') ?>
                <div class="contacts flex flex-col justify-between">
                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $firsttel) ?>" class="phone"><?= $firsttel ?></a>
                </div>
                <?= get_template_part('components/social'); ?>
                <div class="zakazat-zvonok">
                    <button class="button button--header" data-modal="modalCall">Заказать звонок</button>
                </div>
            </div>
            <div class="header-bottom">
                <nav class="main-menu relative ">
                   
                </nav>
            </div>
        </div>
    </header>
    <?php get_template_part('components/mobile-menu'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('.header');
            let lastScrollTop = 0;
            let stickyPoint = 85;
            window.addEventListener('scroll', function() {
                const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
                if (currentScroll >= stickyPoint) {
                    header.classList.add('header-fixed');
                } else {
                    header.classList.remove('header-fixed');
                }
            });
        });
    </script>