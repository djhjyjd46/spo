<?php
/**
 * Фильтр: не показывать в навигации скрытые страницы/пункты меню.
 * Убирает пункты, если:
 * - у связанной записи статус private/draft/future
 * - у записи есть мета-поле hide_in_menu/hide_from_menu и его значение truthy
 * - у пункта меню есть CSS-класс, содержащий 'hidden' или 'no-menu'
 * Проверка применяется только для location 'header' (можно убрать/изменить).
 */

if (!defined('ABSPATH')) {
	exit;
}

add_filter('wp_get_nav_menu_items', function ($items, $menu, $args) {
	// Применяем фильтр только к главному меню темы (location 'header')
	if (isset($args->theme_location) && $args->theme_location !== 'header') {
		return $items;
	}

	$filtered = [];
	$meta_keys = ['hide_from_menu', 'hide_in_menu', 'menu_hidden', 'exclude_from_menu', 'no_menu', 'show_in_menu'];

	foreach ($items as $item) {
		$skip = false;

		// Проверяем CSS-классы пункта меню
		$classes = (array) $item->classes;
		foreach ($classes as $c) {
			if (!$c) continue;
			if (preg_match('/hidden|hide|no\-?menu|exclude/i', $c)) {
				$skip = true;
				break;
			}
		}
		if ($skip) continue;

		// Если пункт связан с записью/страницей — проверим статус и мета
		$object_id = isset($item->object_id) ? (int) $item->object_id : 0;
		if ($object_id) {
			$status = get_post_status($object_id);
			if (in_array($status, ['private', 'draft', 'future'], true)) {
				continue; // скрываем
			}

			foreach ($meta_keys as $mk) {
				$val = get_post_meta($object_id, $mk, true);
				if ($val === '' || $val === null) continue;
				$v = is_array($val) ? reset($val) : $val;
				$vnorm = mb_strtolower(trim((string) $v));
				if (in_array($vnorm, ['1', 'true', 'yes', 'on'], true)) {
					$skip = true;
					break;
				}
				// специальный ключ show_in_menu: если явно 0/false — скрыть
				if ($mk === 'show_in_menu' && in_array($vnorm, ['0', 'false', 'no', 'off'], true)) {
					$skip = true;
					break;
				}
			}
		}

		if ($skip) continue;
		$filtered[] = $item;
	}

	return $filtered;
}, 10, 3);
