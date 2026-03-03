<?php

/**
 * Шаблон для отображения списка отзывов Яндекс
 * Переменная $reviews содержит массив отзывов
 */

if (!defined('ABSPATH')) {
    exit;
}

// Получаем общую статистику
$stats = MyYandexReviews::get_reviews_stats();

function triniti_format_date_ru($date)
{
    $months = [
        1 => 'января',
        2 => 'февраля',
        3 => 'марта',
        4 => 'апреля',
        5 => 'мая',
        6 => 'июня',
        7 => 'июля',
        8 => 'августа',
        9 => 'сентября',
        10 => 'октября',
        11 => 'ноября',
        12 => 'декабря'
    ];
    $ts = strtotime($date);
    $day = date('d', $ts);
    $month = $months[(int)date('m', $ts)];
    $year = date('Y', $ts);
    return "$day $month $year";
}
?>

<div class="container">
    <div class="review-head flex flex-col gap-5 md:gap-12">
        <div class="review-head-names flex gap-6 mb-5">
            <div class="g-map flex gap-1">
                <svg width="15" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_313_8520" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0"
                        width="15" height="20">
                        <path d="M0 0.5H15V19.5H0V0.5Z" fill="white" />
                    </mask>
                    <g mask="url(#mask0_313_8520)">
                        <path
                            d="M7 0.5C2.85793 0.5 0 3.66566 0 7.5C0 11.7213 3.35992 12.912 5 15.5C6.5269 17.9085 5.79419 19.5 7 19.5C8.25043 19.5 8.43749 18.0347 10 15.5C11.562 12.965 15 11.7213 15 7.5C15 3.66566 11.1422 0.5 7 0.5ZM7 10.5C5.45044 10.5 5 8.93444 5 7.5C5 6.78239 5.49211 5.97028 6 5.5C6.50775 5.03024 6.22522 4.5 7 4.5C8.54956 4.5 10 6.0653 10 7.5C10 8.93444 8.54956 10.5 7 10.5Z"
                            fill="#4CAF50" />
                    </g>
                    <mask id="mask1_313_8520" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="7" y="0"
                        width="8" height="20">
                        <path d="M7 0.5H15V19.5H7V0.5Z" fill="white" />
                    </mask>
                    <g mask="url(#mask1_313_8520)">
                        <path
                            d="M8 0.50006C7.79432 0.50006 7.20138 0.484545 7 0.50006C10.7952 0.79185 14 3.85894 14 7.50006C14 11.7214 10.5389 12.9651 9 15.5001C7.78078 17.5077 7.64463 18.965 7 19.5001C7.16532 19.6398 7.76021 19.5001 8 19.5001C9.23197 19.5001 8.46055 18.0346 10 15.5001C11.5389 12.965 15 11.7214 15 7.50006C15 3.66572 12.0811 0.50006 8 0.50006Z"
                            fill="#43A047" />
                    </g>
                    <mask id="mask2_313_8520" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="2"
                        width="6" height="9">
                        <path d="M0 2.5H6V10.5H0V2.5Z" fill="white" />
                    </mask>
                    <g mask="url(#mask2_313_8520)">
                        <path d="M6 5.5L1 10.5C0.607554 9.70671 0 8.68022 0 7.5C0 5.51669 0.545515 3.80792 2 2.5L6 5.5Z"
                            fill="#F44336" />
                    </g>
                    <path
                        d="M10 9.5C10.5012 9.04595 11 8.18606 11 7.5C11 6.10929 9.56533 5.5 8 5.5C7.21705 5.5 6.51301 5.04444 6 5.5L11 1.5C12.7939 2.19112 14.2458 3.8956 15 5.5L10 9.5Z"
                        fill="#42A5F5" />
                    <path
                        d="M10 1.5C11.5285 2.49865 12.5257 4.55824 13 6.5L14 5.5C13.3149 3.68235 11.6291 2.28292 10 1.5Z"
                        fill="#2196F3" />
                    <path
                        d="M10 9.5L5 14.5C3.81173 13.0744 1.82234 12.287 1 10.5L6 5.5C5.48748 5.98811 5 6.75525 5 7.5C5 8.9891 6.43601 10.5 8 10.5C8.79194 10.5 9.4862 9.99921 10 9.5Z"
                        fill="#FFC107" />
                    <mask id="mask3_313_8520" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="2" y="0"
                        width="8" height="6">
                        <path d="M2 0.5H10V5.5H2V0.5Z" fill="white" />
                    </mask>
                    <g mask="url(#mask3_313_8520)">
                        <path
                            d="M10 1.5L5 5.5L2 2.5C3.30755 1.26652 4.99218 0.5 7 0.5C8.01927 0.5 9.12108 1.14453 10 1.5Z"
                            fill="#1E88E5" />
                    </g>
                    <mask id="mask4_313_8520" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="7" y="0"
                        width="4" height="2">
                        <path d="M7 0.5H11V1.5H7V0.5Z" fill="white" />
                    </mask>
                    <g mask="url(#mask4_313_8520)">
                        <path
                            d="M8 0.499867C7.76825 0.499867 7.2269 0.483687 7 0.499867C8.28281 0.591233 9.948 0.960234 11 1.49987C9.98519 1.11659 9.17686 0.499867 8 0.499867Z"
                            fill="#1976D2" />
                    </g>
                </svg>
                <div class="flex">
                    <span class="g1-map__label">Google</span>
                    <span class="g2-maps__label">Maps</span>
                </div>
            </div>
            <div class="ya-map flex gap-1">
                <div class="imgs flex gap-1">
                    <img src="<?= esc_url(plugins_url('yandex.png', dirname(__FILE__))) ?>" alt="Яндекс">
                    <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8 0C5.87904 0.00245748 3.49975 0.527032 2 2C0.500255 3.47297 0.00250514 5.91691 2.98295e-06 8C-0.00194708 9.70221 0.952518 11.6439 2 13C1.96437 12.9593 2 13 2 13L8 20L14 13C14 13 14.032 12.9621 14 13C15.0468 11.6443 16.0017 9.70139 16 8C15.9975 5.91691 15.4998 3.47297 14 2C12.5003 0.527032 10.121 0.00245748 8 0ZM8 11C7.42464 11 6.4784 10.3139 6 10C5.52161 9.68605 5.22018 9.52207 5 9C4.77982 8.47793 4.88775 7.55423 5 7C5.11225 6.44577 5.59316 6.39958 6 6C6.40685 5.60042 6.43569 5.11024 7 5C7.56431 4.88976 8.46844 4.78375 9 5C9.53157 5.21625 9.68035 5.53015 10 6C10.3197 6.46985 11 7.43491 11 8C10.999 8.75747 10.5454 9.46439 10 10C9.45465 10.5356 8.77125 10.9991 8 11Z"
                            fill="#DD0000" />
                    </svg>
                </div>
                <p class="ya-map__label">Карты <?= esc_html($stats['average_rating']) ?></p>
            </div>
        </div>
    </div>
    <div class="review-count mb-6">
        <span class="review-count__label"><?= esc_html($stats['total_reviews'] ?: count($reviews)) ?> отзывов</span>
    </div>
</div>

<div class="rev-nav flex gap-4 mb-6 md:justify-between">
    <div class="btns flex gap-2">
        <div class="review-button-prev">
            <svg width="10" height="17" viewBox="0 0 10 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M1 8.99992L8 15.9999C8.11328 16.1144 7.8435 15.9999 8 15.9999C8.1565 15.9999 8.88673 16.1144 9 15.9999C8.97002 16.0739 9.0551 15.9445 9 15.9999C9.02998 15.9259 9 15.0805 9 14.9999C9.02998 15.0739 9 14.9194 9 14.9999C9.0551 15.0554 8.97002 14.9259 9 14.9999L2 7.99992L9 1.99992C8.97002 2.07393 9.0551 1.94445 9 1.99992C9 2.08047 9.02998 1.92592 9 1.99992C9 1.91938 9.02998 1.07393 9 0.999925C9.0551 1.0554 8.97002 0.925916 9 0.999925C8.88673 0.885497 8.1565 0.999925 8 0.999925C7.8435 0.999925 8.11328 0.885497 8 0.999925L1 7.99992C0.940296 8.0602 1.03248 7.9196 1 7.99992C0.967524 8.08025 1 8.91254 1 8.99992C0.967524 8.9196 1 9.08731 1 8.99992C0.940296 8.93965 1.03248 9.08025 1 8.99992Z"
                    fill="#222222" />
            </svg>
        </div>
        <div class="review-button-next">
            <svg width="10" height="17" viewBox="0 0 10 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M9 8.00008L2 1.00008C1.88672 0.885648 2.1565 1.00008 2 1.00008C1.8435 1.00008 1.11327 0.885648 0.999999 1.00008C1.02998 0.926067 0.944896 1.05555 0.999999 1.00008C0.970016 1.07408 0.999999 1.91953 0.999999 2.00008C0.970016 1.92607 0.999999 2.08062 0.999999 2.00008C0.944896 1.9446 1.02998 2.07408 0.999999 2.00008L8 9.00008L0.999999 15.0001C1.02998 14.9261 0.944896 15.0555 0.999999 15.0001C0.999999 14.9195 0.970016 15.0741 0.999999 15.0001C0.999999 15.0806 0.970016 15.9261 0.999999 16.0001C0.944896 15.9446 1.02998 16.0741 0.999999 16.0001C1.11327 16.1145 1.8435 16.0001 2 16.0001C2.1565 16.0001 1.88672 16.1145 2 16.0001L9 9.00008C9.0597 8.9398 8.96752 9.0804 9 9.00008C9.03248 8.91975 9 8.08746 9 8.00008C9.03248 8.0804 9 7.91269 9 8.00008C9.0597 8.06035 8.96752 7.91975 9 8.00008Z"
                    fill="#222222" />
            </svg>
        </div>
    </div>
    <a class="btn btn-review" href="https://yandex.by/maps/org/tri_niti/143027666928/reviews/" target="_blank"
        rel="noopener">
        Оставить отзыв
    </a>
</div>

<div class="yandex-reviews-grid">
    <div class="splide" id="splide-reviews">
        <div class="splide__track">
            <ul class="splide__list">
                <?php foreach ($reviews as $review): ?>
                    <li class="splide__slide">
                        <div class="review-card flex flex-col justify-between">
                            <div class="flex flex-col justify-between">
                                <div class="review-header">
                                    <div class="review-author-info flex">
                                        <?php if ($thumbnail = get_the_post_thumbnail_url($review->ID, 'thumbnail')): ?>
                                            <div class="review-avatar">
                                                <img src="<?= esc_url($thumbnail) ?>"
                                                    alt="<?= esc_attr(get_post_meta($review->ID, 'review_author', true)) ?>" />
                                            </div>
                                        <?php else: ?>
                                            <div class="review-avatar review-avatar-placeholder">
                                                <span><?= esc_html(mb_substr(get_post_meta($review->ID, 'review_author', true), 0, 1)) ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex flex-col">
                                            <div class="review-meta">
                                                <p class="review-author">
                                                    <?= esc_html(get_post_meta($review->ID, 'review_author', true) ?: 'Аноним') ?>
                                                </p>
                                                <?php if ($review_date = get_post_meta($review->ID, 'review_date', true)): ?>
                                                    <time
                                                        class="review-date"><?= esc_html(triniti_format_date_ru($review_date)) ?></time>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($rating = get_post_meta($review->ID, 'review_rating', true)): ?>
                                                <div class="review-rating">
                                                    <?= MyYandexReviews::render_stars(intval($rating)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="review-content">
                                    <div class="review-text">
                                        <?= wp_kses_post(wpautop($review->post_content)) ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($review_link = get_post_meta($review->ID, 'review_link', true)): ?>
                                <div class="review-footer flex items-center justify-between">
                                    <div class="flex gap-1">
                                        <svg width="16" height="20" viewBox="0 0 16 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M8 0C5.87904 0.00245748 3.49975 0.527032 2 2C0.500255 3.47297 0.00250514 5.91691 2.98295e-06 8C-0.00194708 9.70221 0.952518 11.6439 2 13C1.96437 12.9593 2 13 2 13L8 20L14 13C14 13 14.032 12.9621 14 13C15.0468 11.6443 16.0017 9.70139 16 8C15.9975 5.91691 15.4998 3.47297 14 2C12.5003 0.527032 10.121 0.00245748 8 0ZM8 11C7.42464 11 6.4784 10.3139 6 10C5.52161 9.68605 5.22018 9.52207 5 9C4.77982 8.47793 4.88775 7.55423 5 7C5.11225 6.44577 5.59316 6.39958 6 6C6.40685 5.60042 6.43569 5.11024 7 5C7.56431 4.88976 8.46844 4.78375 9 5C9.53157 5.21625 9.68035 5.53015 10 6C10.3197 6.46985 11 7.43491 11 8C10.999 8.75747 10.5454 9.46439 10 10C9.45465 10.5356 8.77125 10.9991 8 11Z"
                                                fill="#DD0000" />
                                        </svg>
                                        <span class="review-source">Карты <?= esc_html($stats['average_rating']) ?></span>
                                    </div>
                                    <a href="https://yandex.by/maps/org/tri_niti/143027666928/reviews/"
                                        class="review-link flex gap-2 items-center" target="_blank" rel="noopener">
                                        Читать на Яндексе
                                        <svg width="19" height="8" viewBox="0 0 19 8" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.9"
                                                d="M18.313 4.31304C18.4859 4.14015 18.4859 3.85985 18.313 3.68696L15.4957 0.869579C15.3228 0.696691 15.0425 0.696691 14.8696 0.869579C14.6967 1.04247 14.6967 1.32278 14.8696 1.49566L17.3739 4L14.8696 6.50434C14.6967 6.67722 14.6967 6.95753 14.8696 7.13042C15.0425 7.30331 15.3228 7.30331 15.4957 7.13042L18.313 4.31304ZM0 4V4.44271H18V4V3.55729H0V4Z"
                                                fill="#222222" />
                                        </svg>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Splide && document.getElementById('splide-reviews')) {
            const reviewsCount = <?= count($reviews) ?>;
            const perPage = window.innerWidth < 1024 ? 1 : 3;
            const loopMode = reviewsCount > perPage ? 'loop' : 'slide';
            const splide = new Splide('#splide-reviews', {
                type: loopMode,
                perPage: perPage,
                perMove: 1,
                gap: 20,
                arrows: false,
                pagination: false,
                breakpoints: {
                    640: {
                        perPage: 1,
                    },
                    1024: {
                        perPage: 2
                    }
                }
            });
            splide.mount();
            const prevBtn = document.querySelector('.review-button-prev');
            const nextBtn = document.querySelector('.review-button-next');
            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    splide.go('<');
                });
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    splide.go('>');
                });
            }
        }
    });
</script>