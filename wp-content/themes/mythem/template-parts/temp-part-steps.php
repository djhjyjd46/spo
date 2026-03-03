<section class="steps">
    <h2>Этапы работы с нами</h2>
    <?php
    $steps = get_field('steps', 'option');
    if ($steps) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6 ">
            <?php
            foreach ($steps as $i => $step) :
                // рассчитываем задержку по индексу (0, 0.1s, 0.2s ...)
                $delay = sprintf('%.2fs', $i * 0.1);
                $step_number = $i + 1;
            ?>
                <div class="step p-6 bg-white shadow-sm size-full rounded-sm zoom-in"
                    style="--anim-delay: <?= esc_attr($delay) ?>;">
                    <div class="mb-5 md:mb-6">
                        <span class="opacity-70  mb-5 md:mb-6">Шаг <?= esc_html($step_number) ?></span>
                    </div>
                    <h3 class="step-title mb-4 font-semibold md:text-xl text-[#007DC6]">
                        <?= esc_html($step['zagolovok']); ?>
                    </h3>
                    <p class="step-text opacity-60"><?= esc_html($step['opisanie']); ?></p>
                </div>
        <?php
            endforeach;
        endif; ?>
        </div>

</section>