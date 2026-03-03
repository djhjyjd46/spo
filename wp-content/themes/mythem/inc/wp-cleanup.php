<?php
/**
 * Отключение jQuery и дефолтных стилей/скриптов WordPress на фронтенде
 * Сохраняем загрузку скриптов и стилей плагинов
 *
 * Комментарии и имена — на русском в соответствии с инструкциями проекта.
 */

if (!defined('ABSPATH')) {
	exit; // защита от прямого вызова
}

// Убираем добавление emoji, которые WP добавляет в <head>
function mythem_disable_wp_emoji() {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action('init', 'mythem_disable_wp_emoji');

// Отключаем только core WordPress стили/скрипты на фронтенде
function mythem_dequeue_wp_core_assets() {
	if (is_admin()) {
		return; // не трогаем админку
	}

	// Убираем только core WordPress скрипты
	wp_dequeue_script('wp-embed');
	
	// Убираем только core WordPress стили (блоки Gutenberg и системные)
	wp_dequeue_style('wp-block-library'); // основные стили блоков Gutenberg
	wp_dequeue_style('wp-block-library-theme'); // тематические стили блоков
	wp_dequeue_style('global-styles'); // глобальные стили (WP 5.9+)
	wp_dequeue_style('classic-theme-styles'); // стили классических тем
	
	// НЕ убираем стили плагинов типа wc-block-style, dashicons и др.
}
add_action('wp_enqueue_scripts', 'mythem_dequeue_wp_core_assets', 100);

// Простое отключение jQuery на фронтенде (теперь плагин не зависит от него)
function mythem_remove_jquery() {
	if (is_admin()) {
		return; // не трогаем админку
	}
	
	// Убираем jQuery на фронтенде
	wp_dequeue_script('jquery');
	wp_deregister_script('jquery');
	wp_dequeue_script('jquery-core');  
	wp_deregister_script('jquery-core');
	wp_dequeue_script('jquery-migrate');
	wp_deregister_script('jquery-migrate');
}
add_action('wp_enqueue_scripts', 'mythem_remove_jquery', 999);

// Дополнительная попытка убрать поздно подключаемые core-стили
function mythem_remove_late_core_styles() {
	if (is_admin()) {
		return;
	}
	
	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wp-block-library-theme');
	wp_dequeue_style('global-styles');
}
add_action('wp_print_footer_scripts', 'mythem_remove_late_core_styles', 1);
