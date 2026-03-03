<?php
// set-page-template-for-children.php
// Устанавливает шаблон страницы для всех дочерних страниц указанных parent IDs
// Запуск: php set-page-template-for-children.php --parents=1318,1316,1320 [--dry-run]

$parents_default = [1318,1316,1320];
$template_path = 'pages/page-products.php'; // значение для _wp_page_template

// Найдём wp-load.php
$maybe = __DIR__ . '/../../../wp-load.php';
if (! file_exists($maybe)) {
    $cwd = __DIR__;
    $found = false;
    for ($i = 0; $i < 6; $i++) {
        $try = realpath($cwd . str_repeat('/..', $i) . '/wp-load.php');
        if ($try && file_exists($try)) {
            $maybe = $try;
            $found = true;
            break;
        }
    }
    if (! $found && ! file_exists($maybe)) {
        echo "Не найден wp-load.php. Запустите из корня WP или укажите путь\n";
        exit(1);
    }
}
require_once $maybe;

// Парсим CLI аргументы
$argv_str = implode(' ', $argv);
$dry = in_array('--dry-run', $argv, true) || in_array('-n', $argv, true);
$parents = $parents_default;
foreach ($argv as $arg) {
    if (strpos($arg, '--parents=') === 0) {
        $raw = substr($arg, strlen('--parents='));
        $parts = array_filter(array_map('intval', explode(',', $raw)));
        if (! empty($parts)) $parents = $parts;
    }
}

echo "Parents: " . implode(',', $parents) . "\n";
echo "Template: {$template_path}\n";
echo $dry ? "DRY RUN\n" : "WILL APPLY CHANGES\n";

$total_found = 0;
$to_update = [];
foreach ($parents as $pid) {
    // Найдём прямых детей
    $children = get_posts([
        'post_type' => 'page',
        'posts_per_page' => -1,
        'post_parent' => $pid,
        'fields' => 'ids',
        'post_status' => 'any',
    ]);
    echo "Parent {$pid}: found " . count($children) . " children\n";
    foreach ($children as $c) {
        $current = get_post_meta($c, '_wp_page_template', true);
        $to_update[] = ['id' => $c, 'title' => get_the_title($c), 'current' => $current];
    }
    $total_found += count($children);
}

if ($total_found === 0) {
    echo "No children found for provided parents. Exiting.\n";
    exit(0);
}

echo "\nSample of pages to update (first 50):\n";
foreach (array_slice($to_update,0,50) as $u) {
    echo "ID: {$u['id']} Title: {$u['title']} Current template: " . ($u['current'] ?: '(default)') . "\n";
}

if ($dry) {
    echo "\nDry run complete. No changes applied. Total pages: {$total_found}\n";
    exit(0);
}

// Применяем изменения
$updated = 0;
foreach ($to_update as $u) {
    $id = $u['id'];
    update_post_meta($id, '_wp_page_template', $template_path);
    $updated++;
}

echo "\nApplied template to {$updated} pages.\n";
exit(0);
