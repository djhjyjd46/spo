<div class="mob-inner visible-mobile">
    <a href="<?= home_url(); ?>">
        <img src="<?= the_field('logo', 'option') ?>" alt="logo">
    </a>
    <div class="flex gap-4">
        <div class="mob-inner__ico">
            <button class="header__burger-button burger-button" type="button" onclick="mobileOverlay.showModal()">
                <span class="visually-hidden">Open navigation menu</span>
            </button>
            <dialog class="mobile-overlay" id="mobileOverlay">
                <div class="mobileheader">
                    <img src="<?= the_field('logo', 'option') ?>" alt="logo">
                    <form class="mobile-overlay__close-button-wrapper" method="dialog">
                        <button class="mobile-overlay__close-button cross-button" type="submit">
                            <span class="visually-hidden">Close navigation menu</span>
                        </button>
                    </form>
                </div>
                <div class="mobsearch mt-16 px-[10px]">
                    <?= get_template_part('components/input-search') ?>
                </div>
                <div class="mobile-overlay__body">
                    <?php wp_nav_menu([
                        'theme_location' => 'header',
                        'container' => false,
                        'menu_class' => 'main-menu-list',
                        'container' => false,
                        'depth' => 1,
                        'menu_class' => 'mobile-overlay__list',
                    ]); ?>
                </div>
                <div class="mobile-overlay__contacts">
                    <div class="tel">
                        <?php get_template_part('components/get-phones'); ?>
                    </div>
                    <div class="email">
                        <?php $email = get_field('email', 'option');
                        if ($email) : ?>
                            <a href="mailto:<?= esc_attr($email); ?>">
                                <svg width="20" height="14" viewBox="0 0 20 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M3.8151 0.328125H16.1901C16.9511 0.32808 17.6832 0.592753 18.2363 1.06785C18.7894 1.54295 19.1216 2.19245 19.1647 2.88312L19.1693 3.03646V10.9531C19.1693 11.6449 18.8782 12.3105 18.3556 12.8133C17.833 13.3162 17.1185 13.6181 16.3588 13.6573L16.1901 13.6615H3.8151C3.05414 13.6615 2.32201 13.3968 1.76889 12.9217C1.21577 12.4466 0.883603 11.7971 0.840521 11.1065L0.835938 10.9531V3.03646C0.835888 2.34468 1.12703 1.6791 1.64964 1.17627C2.17224 0.673431 2.8867 0.371457 3.64644 0.332292L3.8151 0.328125ZM17.7943 4.80562L10.3234 8.38062C10.2391 8.42114 10.1464 8.44524 10.0512 8.45141C9.95605 8.45758 9.86051 8.44568 9.77069 8.41646L9.68269 8.38146L2.21094 4.80646V10.9531C2.21095 11.3191 2.36234 11.6717 2.63506 11.941C2.90778 12.2102 3.28188 12.3763 3.6831 12.4065L3.8151 12.4115H16.1901C16.5928 12.4114 16.9808 12.2737 17.277 12.0256C17.5732 11.7775 17.7559 11.4372 17.7888 11.0723L17.7943 10.9531V4.80562ZM16.1901 1.57812H3.8151C3.41251 1.57814 3.02465 1.71577 2.7285 1.96369C2.43235 2.21162 2.24956 2.55171 2.21644 2.91646L2.21094 3.03646V3.39396L10.0026 7.12229L17.7943 3.39312V3.03646C17.7942 2.67033 17.6427 2.31762 17.3698 2.04837C17.0969 1.77912 16.7226 1.61304 16.3212 1.58312L16.1901 1.57812Z"
                                        fill="currentColor" />
                                </svg>
                                <?= esc_html($email); ?></a>
                        <?php endif; ?>
                    </div>

                    <div class="mess">
                        <?= get_template_part('components/social'); ?>
                    </div>
                    <div class="time">
                        <span>Время работы:</span>
                        <p><?= the_field('график', 'option'); ?></p>
                    </div>
                </div>
            </dialog>
        </div>
    </div>
</div>