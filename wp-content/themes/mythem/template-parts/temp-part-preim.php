<section class="preimuschestva container">
    <?php
    $preimuschestva = get_field('преимущества', 'option');
    ?>
    <h2 class="section-title uppercase"><?= esc_html($preimuschestva['заголовок']); ?></h2>
    <?php if ($banner_text): ?>
        <p class="baner__text mb-6 md:text-xl font-semibold"><?= esc_html($banner_text) ?></p>
    <?php endif; ?>
    <div class="preimuschestva__grid flex flex-wrap w-full justify-between gap-8 md:gap-6">
        <?php foreach ($preimuschestva['список'] as $item): ?>

            <div
                class="preimuschestva__item w-full  h-auto flex flex-col shadow-sm rounded p-4 md:shadow-none md:rounded-none md:p-0 fade-up ">
                <div class="img mb-4 w-14 h-10">
                    <img class="" src="<?= esc_url($item['изображение']['url']); ?>"
                        alt="<?= (isset($item['изображение']['alt']) && $item['изображение']['alt']) ? esc_attr($item['изображение']['alt']) : 'иконка' ?>">
                </div>
                <p class="preimuschestva__item-text font-semibold text-lg whitespace-pre-line"><?= $item['текст']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>