<?php

/**
 * upload-mimes.php
 * Фильтры для разрешения загрузки дополнительных типов файлов
 * Размещено в inc/ для чистоты структуры темы
 */

// Разрешаем дополнительные mime-типы (pdf для всех, остальные для админов)
add_filter('upload_mimes', function ($mimes) {
    // PDF разрешаем всем (обычно безопасный формат). Другие типы ограничим ниже.
    $mimes['pdf']  = 'application/pdf';

    if (!current_user_can('manage_options')) {
        return $mimes;
    }

    // Документы
    $mimes['doc']  = 'application/msword';
    $mimes['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    $mimes['xls']  = 'application/vnd.ms-excel';
    $mimes['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    $mimes['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';

    // Архивы и изображения
    $mimes['zip']  = 'application/zip';
    $mimes['webp'] = 'image/webp';

    // SVG — только для пользователей с правами на unfiltered_html
    if (current_user_can('unfiltered_html')) {
        $mimes['svg']  = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';
    }

    return $mimes;
});
