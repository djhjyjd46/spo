<?php
// inc/default-child-template.php
// Автоматически назначает шаблон `pages/page-products.php` дочерним страницам
// родителей из списка. Работает при создании/сохранении страницы.

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Список parent IDs, для которых дочерним страницам по умолчанию назначается шаблон
 */
$caf_parents = [1318, 1316, 1320];

/**
 * Шаблон (относительный путь внутри темы) для назначения
 */
$caf_template = 'pages/page-products.php';

add_action('save_post_page', function ($post_id, $post, $update) use ($caf_parents, $caf_template) {
    // Проверки безопасности
    if (wp_is_post_revision($post_id)) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (get_post_type($post_id) !== 'page') return;

    $parent = (int) $post->post_parent;
    if ($parent && in_array($parent, $caf_parents, true)) {
        $current = get_post_meta($post_id, '_wp_page_template', true);
        // Если шаблон уже установлен и не default — не перезаписываем
        if (empty($current) || $current === 'default') {
            update_post_meta($post_id, '_wp_page_template', $caf_template);
        }
    }
}, 10, 3);

// При создании новой страницы через wp_insert_post тоже save_post вызывается,
// поэтому отдельного хука не требуется.
