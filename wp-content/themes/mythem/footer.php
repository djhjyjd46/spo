<section id="contact-form" class="contact-form relative">
    <picture>
        <source srcset="<?= esc_url(get_field('form-bg', 'options')) ?>" media="(min-width: 720px)">
        <img class="contact-form__bg absolute z-0 top-0 left-0 w-full h-full"
            src="<?= esc_url(get_field('form-bg-mobile', 'options')) ?>" alt="книга на голубом фоне">
    </picture>
    <div class="container">
        <h2 class="whitespace-pre-line"><?= get_field('form-zagolovok', 'options'); ?></h2>
        <p><?= get_field('form-text', 'options'); ?></p>
        <?= get_template_part('components/form'); ?>
    </div>
</section>
<footer id="footer" class="footer ">
    <div class="container">
        <div class="footer__logo">
            <img src="<?= get_field('logo-footer', 'options') ?>" alt="logo" class="footer__logo-img">
            <div class="footer__info">
                <div class="flex flex-col gap-2">
                    <?= get_template_part('components/get-adress'); ?>
                </div>
            </div>
            <div class="footer__logo-time">
                <div class="flex flex-col gap-2">
                    <p class="font-semibold text-lg">Время работы:</p>
                    <p class="text-sm"><?= the_field('график', 'option') ?></p>
                </div>
            </div>
            <div class="footer__links hidden-mobile">
                <a href="<?= home_url('/policy') ?>">Политика обработки персональных данных</a>
            </div>
        </div>

        <div class="footer__menu">
            <h3><a href="#header">Меню сайта</a></h3>
            <?php wp_nav_menu([
                'namespace' => 'footer',
                'container' => false,
                'depth' => 1
            ]); ?>
        </div>
        <div class="footer__contacts">
            <h3>Контакты</h3>
            <div>
                <div class="tel">
                    <span>Номера телефонов:</span>
                    <div class="footer__tel">
                        <?php get_template_part('components/get-phones'); ?>
                    </div>
                </div>
                <div class="email">
                    <span>Электронная почта: </span>
                    <a href="mailto: <?= the_field('email', 'options') ?>"><svg width="20" height="14"
                            viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M3.8151 0.328125H16.1901C16.9511 0.32808 17.6832 0.592753 18.2363 1.06785C18.7894 1.54295 19.1216 2.19245 19.1647 2.88312L19.1693 3.03646V10.9531C19.1693 11.6449 18.8782 12.3105 18.3556 12.8133C17.833 13.3162 17.1185 13.6181 16.3588 13.6573L16.1901 13.6615H3.8151C3.05414 13.6615 2.32201 13.3968 1.76889 12.9217C1.21577 12.4466 0.883603 11.7971 0.840521 11.1065L0.835938 10.9531V3.03646C0.835888 2.34468 1.12703 1.6791 1.64964 1.17627C2.17224 0.673431 2.8867 0.371457 3.64644 0.332292L3.8151 0.328125ZM17.7943 4.80562L10.3234 8.38062C10.2391 8.42114 10.1464 8.44524 10.0512 8.45141C9.95605 8.45758 9.86051 8.44568 9.77069 8.41646L9.68269 8.38146L2.21094 4.80646V10.9531C2.21095 11.3191 2.36234 11.6717 2.63506 11.941C2.90778 12.2102 3.28188 12.3763 3.6831 12.4065L3.8151 12.4115H16.1901C16.5928 12.4114 16.9808 12.2737 17.277 12.0256C17.5732 11.7775 17.7559 11.4372 17.7888 11.0723L17.7943 10.9531V4.80562ZM16.1901 1.57812H3.8151C3.41251 1.57814 3.02465 1.71577 2.7285 1.96369C2.43235 2.21162 2.24956 2.55171 2.21644 2.91646L2.21094 3.03646V3.39396L10.0026 7.12229L17.7943 3.39312V3.03646C17.7942 2.67033 17.6427 2.31762 17.3698 2.04837C17.0969 1.77912 16.7226 1.61304 16.3212 1.58312L16.1901 1.57812Z"
                                fill="#FFC400" />
                        </svg>
                        <?= the_field('email', 'options') ?></a>
                </div>
                <div class="mess">
                    <span>Мессенджеры и соц.сети:</span>
                    <div class="footer__socials">
                        <?= get_template_part('components/social'); ?>
                    </div>
                </div>
            </div>

        </div>
        <div class="footer__links visible-mobile">
            <a href="<?= get_permalink(get_page_by_path('policy')); ?>" class="policy">Политика обработки персональных
                данных</a>
        </div>
    </div>
</footer>

<!-- Подключение всех модальных окон -->
<?= get_template_part('components/modal-orderCall'); ?>
<?= get_template_part('components/modal-policy'); ?>
<?= get_template_part('components/modal-Thnx'); ?>
<?= get_template_part('components/modal-image'); ?>
<div id="cookieConsent" class="fixed bottom-4 left-4 right-4 md:left-auto md:right-8 z-50 md:bottom-8 max-w-[980px] mx-auto pointer-events-auto">
        <div class="bg-white shadow-lg rounded-lg p-1 md:p-2 flex flex-col md:flex-row items-start md:items-center gap-4 transition-all duration-200 ease-in-out opacity-0 translate-y-3 pointer-events-none" id="cookieConsentInner">
            <p class="mb-1 flex flex-wrap gap-1">Мы используем <a class="font-medium underline" href="<?= home_url() ?>/policy">cookie</a></p>
        <div class="flex items-center gap-3">
            <button id="cookieDecline" class="text-sm px-3 py-2 rounded-md border border-gray-200">Отклонить</button>
            <button id="cookieAccept" class="text-sm px-4 py-2 rounded-md bg-blue text-white">Принять</button>
        </div>
    </div>
</div>

<?php
wp_footer(); ?>
</body>

</html>