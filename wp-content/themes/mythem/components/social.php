 <div class="soc-seti flex items-center gap-2">
     <?php
        $socseti = get_field('соцсети', 'option');
        foreach ($socseti as $item) : ?>
     <a href="<?= $item['ссылка']; ?>" target="_blank" class="text-white">
         <img src="<?= esc_url($item['иконка']); ?>" alt="<?= esc_attr($item['название']); ?>">
     </a>
     <?php endforeach; ?>
 </div>