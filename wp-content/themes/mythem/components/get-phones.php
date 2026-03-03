<?php
$phones = get_field('телефоны', 'option');

if ($phones):
    foreach ($phones as $phone) :
        $number = is_array($phone) ? ($phone['телефон'] ?? '') : $phone;
        $tel = $number ? preg_replace('/[^0-9+]/', '', $number) : ''; ?>
        <?php if ($number) : ?>
            <a class="flex items-center gap-[2px]" href="tel:<?= esc_attr($tel) ?>"><svg width="20" height="21" viewBox="0 0 20 21"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M5.1875 9.5C6.3875 11.8583 8.82917 14.2917 11.1875 15.5L12.1875 13.5C12.4125 13.275 12.8958 13.4 13.1875 13.5C14.1208 13.8083 15.1542 13.5 16.1875 13.5C16.6458 13.5 17.1875 14.0417 17.1875 14.5V17.5C17.1875 17.9583 16.6458 18.5 16.1875 18.5C8.3625 18.5 2.1875 12.325 2.1875 4.5C2.1875 4.04167 2.72917 3.5 3.1875 3.5H6.1875C6.64583 3.5 7.1875 4.04167 7.1875 4.5C7.1875 5.54167 6.87917 6.56667 7.1875 7.5C7.27917 7.79167 7.42083 8.26667 7.1875 8.5L5.1875 9.5Z"
                        fill="#FF7E00" />
                </svg>
                <?= esc_html($number) ?></a>
        <?php endif; ?>
<?php
    endforeach;
endif; ?>