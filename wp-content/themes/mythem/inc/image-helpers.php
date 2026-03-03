<?php
/**
 * Server-side image helpers
 * - Генерация WebP при загрузке/регерации изображений
 * - Отдача уже сконвертированных изображений
 * - Замена <img> на <picture> в HTML-выводе (без изменения шаблонов)
 *
 * Комментарии на русском для команды.
 */

// Безопасные проверки: если файл вызывается напрямую — выйти
if (!defined('ABSPATH')) {
	exit;
}


/**
 * Конвертация изображения в WebP (Imagick при наличии, fallback GD + imagewebp)
 * @param string $src_path
 * @param string $dest_path
 * @param int $quality
 * @return bool
 */
function mytheme_convert_to_webp($src_path, $dest_path, $quality = 80)
{
	if (!file_exists($src_path)) {
		return false;
	}

	$ext = strtolower(pathinfo($src_path, PATHINFO_EXTENSION));
	if (!in_array($ext, array('jpg', 'jpeg', 'png'))) {
		return false;
	}

	$dir = dirname($dest_path);
	if (!file_exists($dir)) {
		wp_mkdir_p($dir);
	}

	// Imagick
	if (class_exists('Imagick')) {
		try {
			$img = new \Imagick($src_path);
			// Для корректной поддержки альфа-канала
			if ($img->getImageAlphaChannel()) {
				$img = $img->coalesceImages();
			}
			$img->setImageFormat('webp');
			$img->setImageCompressionQuality((int)$quality);
			$ok = $img->writeImage($dest_path);
			$img->clear();
			$img->destroy();
			if ($ok && file_exists($dest_path)) {
				return true;
			}
		} catch (Exception $e) {
			// fallthrough to GD
		}
	}

	// GD fallback
	if (function_exists('imagecreatefromjpeg') || function_exists('imagecreatefrompng')) {
		if ($ext === 'jpg' || $ext === 'jpeg') {
			$im = @imagecreatefromjpeg($src_path);
		} else {
			$im = @imagecreatefrompng($src_path);
			// optionally preserve alpha for png
			if ($im && imageistruecolor($im)) {
				// noop
			}
		}

		if (!$im) return false;

		if (function_exists('imagewebp')) {
			$ok = imagewebp($im, $dest_path, (int)$quality);
			imagedestroy($im);
			if ($ok && file_exists($dest_path)) return true;
		} else {
			imagedestroy($im);
		}
	}

	return false;
}


/**
 * Проверяет и возвращает webp-URL для локального изображения в uploads (конвертирует при необходимости)
 * @param string $url
 * @return string|false
 */
function mytheme_ensure_webp($url)
{
	if (empty($url)) return false;
	$uploads = wp_get_upload_dir();
	// Только локальные файлы в uploads
	if (strpos($url, $uploads['baseurl']) !== 0) return false;

	$rel = substr($url, strlen($uploads['baseurl']));
	$src_path = $uploads['basedir'] . $rel;
	if (!file_exists($src_path)) return false;

	$ext = strtolower(pathinfo($src_path, PATHINFO_EXTENSION));
	if (!in_array($ext, array('jpg','jpeg','png'))) return false;

	$webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $src_path);
	$webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $url);

	if (file_exists($webp_path)) return $webp_url;

	// Попытка создать
	$ok = mytheme_convert_to_webp($src_path, $webp_path, 80);
	if ($ok && file_exists($webp_path)) return $webp_url;

	return false;
}


/**
 * При генерации метаданных attachment'а создаём webp-версии (оригинал и размеры)
 */
function mytheme_generate_webp_for_attachment($metadata, $attachment_id)
{
	$file = get_attached_file($attachment_id);
	if (!$file || !file_exists($file)) return $metadata;

	$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
	if (in_array($ext, array('jpg','jpeg','png'))) {
		$webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file);
		mytheme_convert_to_webp($file, $webp, 80);
	}

	if (!empty($metadata['sizes']) && is_array($metadata['sizes'])) {
		foreach ($metadata['sizes'] as $size => $info) {
			if (empty($info['file'])) continue;
			$size_path = path_join(dirname($file), $info['file']);
			$ext = strtolower(pathinfo($size_path, PATHINFO_EXTENSION));
			if (in_array($ext, array('jpg','jpeg','png'))) {
				$webp_size = preg_replace('/\.(jpe?g|png)$/i', '.webp', $size_path);
				mytheme_convert_to_webp($size_path, $webp_size, 80);
			}
		}
	}

	return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'mytheme_generate_webp_for_attachment', 10, 2);


/**
 * Разбор атрибутов тега img (упрощённый) в ассоциативный массив
 */
function mytheme_parse_img_attrs($attr_str)
{
	$attrs = array();
	$pattern = '/([a-zA-Z0-9-_:]+)\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/';
	if (preg_match_all($pattern, $attr_str, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $m) {
			$key = $m[1];
			$val = isset($m[3]) && $m[3] !== '' ? $m[3] : (isset($m[4]) && $m[4] !== '' ? $m[4] : (isset($m[5]) ? $m[5] : ''));
			$attrs[strtolower($key)] = $val;
		}
	}
	return $attrs;
}


/**
 * Обработчик буфера: заменяет <img ...> на <picture> с webp-источником, если локальный webp доступен/создан
 * НЕ обрабатывает img теги, которые уже находятся внутри picture
 */
function mytheme_replace_img_with_picture($html)
{
    if (stripos($html, '<img') === false) return $html;

    $uploads = wp_get_upload_dir();

    // Сначала найдём все существующие <picture> блоки и запомним позиции img внутри них
    $picture_ranges = array();
    $picture_pattern = '/<picture[^>]*>(.*?)<\/picture>/is';
    if (preg_match_all($picture_pattern, $html, $picture_matches, PREG_OFFSET_CAPTURE)) {
        foreach ($picture_matches[0] as $match) {
            $start = $match[1];
            $end = $start + strlen($match[0]);
            $picture_ranges[] = array('start' => $start, 'end' => $end);
        }
    }

    $pattern = '/<img\s+([^>]*?)\s*\/?>/i';
    $result = preg_replace_callback($pattern, function($m) use ($uploads, $picture_ranges, $html) {
        $full = $m[0];
        $attr_str = $m[1];
        
        // Проверяем, находится ли этот img внутри существующего picture
        $img_position = strpos($html, $full);
        if ($img_position !== false) {
            foreach ($picture_ranges as $range) {
                if ($img_position >= $range['start'] && $img_position < $range['end']) {
                    // img уже внутри picture - не обрабатываем
                    return $full;
                }
            }
        }
        
        $attrs = mytheme_parse_img_attrs($attr_str);

        if (empty($attrs['src'])) return $full;
        $src = $attrs['src'];

        // Обрабатываем только локальные uploads (или файлы из темы)
        $is_local = (strpos($src, $uploads['baseurl']) === 0) || (strpos($src, get_template_directory_uri()) === 0);
        if (!$is_local) return $full;

        $alt = isset($attrs['alt']) ? $attrs['alt'] : '';

        // Получаем (или создаём) webp
        $webp = mytheme_ensure_webp($src);
        if (!$webp) return $full;

        // Разделяем атрибуты: что идёт в picture, что остаётся у img
        $picture_attrs = '';
        $img_attrs = '';
        
        // Атрибуты производительности остаются у img
        $img_only_attrs = array('loading', 'decoding', 'fetchpriority', 'sizes');
        
        foreach ($attrs as $k => $v) {
            if ($k === 'src' || $k === 'alt') {
                // src и alt всегда остаются у img
                continue;
            } elseif (in_array($k, $img_only_attrs)) {
                // Атрибуты производительности остаются у img
                $img_attrs .= ' ' . esc_attr($k) . '="' . esc_attr($v) . '"';
            } else {
                // Все остальные атрибуты (class, style, data-*, id и т.д.) переносятся в picture
                $picture_attrs .= ' ' . esc_attr($k) . '="' . esc_attr($v) . '"';
            }
        }

        // Добавляем дефолтные атрибуты производительности если их нет
        if (!isset($attrs['loading'])) {
            $img_attrs .= ' loading="lazy"';
        }
        if (!isset($attrs['decoding'])) {
            $img_attrs .= ' decoding="async"';
        }

        $html_out = '<picture' . $picture_attrs . '>';
        $html_out .= '<source type="image/webp" srcset="' . esc_url($webp) . '">';
        $html_out .= '<img src="' . esc_url($src) . '" alt="' . esc_attr($alt) . '"' . $img_attrs . '>';
        $html_out .= '</picture>';

        return $html_out;
    }, $html);

    return $result;
}


/**
 * Запуск буферизации вывода на фронтенде (исключаем REST/AJAX/админку)
 */
function mytheme_start_output_buffer()
{
	if (is_admin()) return;
	if (defined('REST_REQUEST') && REST_REQUEST) return;
	if (defined('DOING_AJAX') && DOING_AJAX) return;

	ob_start('mytheme_replace_img_with_picture');
}
add_action('template_redirect', 'mytheme_start_output_buffer', 0);

