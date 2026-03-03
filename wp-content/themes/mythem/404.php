<?php

/**
 * Шаблон страницы 404 (не найдено).
 * Отображается, когда запрашиваемая страница не существует.
 * Содержит информативное сообщение и ссылки для навигации
 * (например, возврат на главную или поиск по сайту).
 */

get_header(); ?>
<main class="flex min-h-screen items-center justify-center bg-gray-50 py-20">
    <div class="container text-center flex flex-col items-center gap-6">
        <div class="mb-6 text-7xl font-bold text-orange-500">404</div>
        <h1 class="mb-4 text-2xl font-semibold md:text-3xl">Страница не найдена</h1>
        <p class="mb-8 text-gray-600">
            Извините, такой страницы не существует или она была удалена.<br>Попробуйте воспользоваться поиском или
            вернитесь на главную.
        </p>
        <a href="<?= esc_url(home_url('/')); ?>" class="button mb-6">На главную</a>

    </div>
</main>
<?php get_footer(); ?>