<?php
/**
 * CSV импортёр для CPT `profession` — добавляет подстраницу в админке рядом с CPT
 * Столбцы CSV: код,название,срок,цена
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('admin_menu', function () {
	$parent = 'edit.php?post_type=profession';
	// Добавляем страницу: 'Импорт CSV' под меню CPT 'profession'
	add_submenu_page(
		$parent,
		'Импорт профессий (CSV)',
		'Импорт CSV',
		'edit_posts',
		'profession_csv_import',
		'mythem_render_profession_csv_import_page'
	);
});

function mythem_render_profession_csv_import_page()
{
	if (!current_user_can('edit_posts')) {
		wp_die('Доступ запрещён');
	}

	$status = [];

	// Обработка загрузки
	if (!empty($_POST) && check_admin_referer('profession_csv_import_action', 'profession_csv_import_nonce')) {
		if (!empty($_FILES['profession_csv_file']) && is_uploaded_file($_FILES['profession_csv_file']['tmp_name'])) {
			$tmp = $_FILES['profession_csv_file']['tmp_name'];
			$handle = fopen($tmp, 'r');
			if ($handle !== false) {
				$row = 0;
				$imported = 0;
				while (($data = fgetcsv($handle, 0, ',')) !== false) {
					$row++;
					// Пропускаем пустые строки
					$allEmpty = true;
					foreach ($data as $c) {
						if (trim($c) !== '') { $allEmpty = false; break; }
					}
					if ($allEmpty) continue;

					// Ожидаем минимум 4 колонки: код, название, срок, цена
					if (count($data) < 4) {
						$status[] = "Строка {$row}: недостаточно колонок";
						continue;
					}

					// В первой строке может быть заголовок — если в первой колонке есть не цифры (код) и равен 'код' или 'Код' — пропускаем
					if ($row === 1) {
						$h0 = mb_strtolower(trim($data[0]));
						if (in_array($h0, ['код', 'code', 'id', 'код профессии'])) {
							continue; // это header
						}
					}

					$code = trim($data[0]);
					$title = trim($data[1]);
					$duration = trim($data[2]);
					$price = trim($data[3]);

					if ($title === '') {
						$status[] = "Строка {$row}: пустое название — пропущено";
						continue;
					}

					// Создаём запись CPT 'profession'
					$postarr = [
						'post_title' => wp_strip_all_tags($title),
						'post_status' => 'publish',
						'post_type' => 'profession',
					];
					$post_id = wp_insert_post($postarr);
					if (is_wp_error($post_id) || $post_id == 0) {
						$status[] = "Строка {$row}: не удалось создать запись";
						continue;
					}

					// Подготовим массив для ACF-поля 'professiya' (если используется группа)
					$prof_val = [
						'kod_professii' => $code,
						'srok_obucheniya' => $duration,
						'stoimost' => $price,
					];

					// Если ACF активен — используем update_field, иначе — записываем meta
					if (function_exists('update_field')) {
						// запись в поле-группу 'professiya'
						update_field('professiya', $prof_val, $post_id);
					} else {
						update_post_meta($post_id, 'professiya', $prof_val);
					}

					$imported++;
				}
				fclose($handle);
				$status[] = "Импорт завершён: импортировано {$imported} записей";
			} else {
				$status[] = 'Не удалось открыть загруженный файл';
			}
		} else {
			$status[] = 'Файл не загружен';
		}
	}

	// Рендер страницы
	?>
	<div class="wrap">
		<h1>Импорт профессий (CSV)</h1>
		<?php if (!empty($status)) : ?>
			<div style="margin:10px 0;">
				<?php foreach ($status as $s) : ?>
					<div style="padding:6px 10px;background:#f7f7f7;border-left:4px solid #007DC6;margin-bottom:6px;"><?= esc_html($s) ?></div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<form method="post" enctype="multipart/form-data">
			<?php wp_nonce_field('profession_csv_import_action', 'profession_csv_import_nonce'); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="profession_csv_file">CSV файл</label></th>
					<td><input type="file" id="profession_csv_file" name="profession_csv_file" accept=".csv,text/csv" required></td>
				</tr>
			</table>
			<p class="submit"><button class="button button-primary" type="submit">Импортировать</button></p>
		</form>
		<p>Формат CSV: код,название,срок,цена — разделитель запятая. Первая строка может быть заголовком.</p>
	</div>
	<?php
}
