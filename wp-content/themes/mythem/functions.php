<?php
/**
 * Главный файл функций темы
 * Подключает модули из папки inc/ и components/
 */

if (!defined('ABSPATH')) {
    exit;
}

// Подключение компонентов темы
require_once get_template_directory() . '/components/breadcrumbs.php';

// Подключение основных модулей
require_once get_template_directory() . '/inc/theme-setup.php';
require_once get_template_directory() . '/inc/image-helpers.php';
require_once get_template_directory() . '/inc/enqueue-scripts.php';
// require_once get_template_directory() . '/inc/mega-menu.php';
require_once get_template_directory() . '/inc/woocommerce-check.php';
require_once get_template_directory() . '/inc/woocommerce-functions.php';
require_once get_template_directory() . '/inc/upload-mimes.php';
require_once get_template_directory() . '/inc/default-child-template.php';

// Подключение дополнительных функций
require_once get_template_directory() . '/inc/wp-cleanup.php';
require_once get_template_directory() . '/inc/menu-filter.php';
require_once get_template_directory() . '/inc/profession-csv-import.php';
