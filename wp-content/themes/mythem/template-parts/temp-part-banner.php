 <section class="baner">
     <div class="container">
         <div class="baner__inner flex justify-between rounded-3xl w-full px-6 pt-6  md:px-10 md:pt-10 zoom-in"
             style="background: linear-gradient(96.88deg, #C5EAFF 5.84%, #007DC6 137.08%);">
             <div class="inner grid grid-cols-1 grid-cols-2-auto md:gap-6 items-center">
                 <div class="baner__left mb-6 md:mb-0 md:h-full md:pb-6 md:flex md:flex-col md:justify-between">
                     <div class="">
                         <h2 class="mb-4"><?= the_field('banner-title'); ?></h2>
                         <p class="baner__text mb-6 md:text-xl font-semibold">
                             <?= the_field('banner-text'); ?></p>
                     </div>
                     <a download target="_blank" href="<?= esc_url(get_field('banner-file')); ?>" class="button">Скачать
                         направление</a>
                 </div>
                 <div class="baner__right flex justify-center">
                     <img class="max-w-full h-auto" src="<?= esc_url(get_field('banner-img')); ?>"
                         alt="<?= esc_attr(get_field('banner-img_alt') ?: 'banner'); ?>">
                 </div>
             </div>
         </div>
     </div>
 </section>