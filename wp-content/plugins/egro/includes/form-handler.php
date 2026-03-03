<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/telegram.php';

/**
 * Получение данных корзины WooCommerce
 */
function egro_get_cart_data()
{
    if (!function_exists('WC') || !WC()->cart) {
        return null;
    }

    $cart = WC()->cart;

    if ($cart->is_empty()) {
        return null;
    }

    $cart_data = array();
    $cart_data['items'] = array();

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        $product_id = $cart_item['product_id'];
        $variation_id = $cart_item['variation_id'];
        $quantity = $cart_item['quantity'];

        $item_data = array(
            'name' => $product->get_name(),
            'quantity' => $quantity,
            'price' => wc_price($product->get_price()),
            'total' => wc_price($cart_item['line_total'])
        );

        // Добавляем вариации если есть
        if ($variation_id) {
            $variation_attributes = array();
            foreach ($cart_item['variation'] as $key => $value) {
                $taxonomy = str_replace('attribute_', '', $key);
                $term = get_term_by('slug', $value, $taxonomy);
                $variation_attributes[] = $term ? $term->name : $value;
            }
            if (!empty($variation_attributes)) {
                $item_data['variation'] = implode(', ', $variation_attributes);
            }
        }

        $cart_data['items'][] = $item_data;
    }

    $cart_data['subtotal'] = wc_price($cart->get_subtotal());

    // Получаем итоговую сумму - используем numeric значение
    $total_numeric = $cart->get_total('');

    // Если итого равно 0 или пусто, используем подытог
    if (empty($total_numeric) || floatval($total_numeric) == 0) {
        $total_numeric = $cart->get_subtotal();
    }

    $cart_data['total'] = wc_price($total_numeric);

    $cart_data['items_count'] = $cart->get_cart_contents_count();

    return $cart_data;
}

/**
 * Проверка, нужно ли добавлять данные корзины
 */
function egro_should_include_cart_data()
{
    // Проверяем атрибут data у кнопки формы - ТОЛЬКО если он точно равен 'get-cart'
    if (isset($_POST['form_data_attribute']) && $_POST['form_data_attribute'] === 'get-cart') {
        return true;
    }

    // Проверяем скрытое поле modal_trigger_data - ТОЛЬКО если оно точно равно 'get-cart'
    if (isset($_POST['modal_trigger_data']) && $_POST['modal_trigger_data'] === 'get-cart') {
        return true;
    }

    // НЕ используем проверку по referer, так как это вызывает проблемы
    // Данные корзины добавляются ТОЛЬКО при наличии явного триггера

    return false;
}

function egro_init_form()
{
    // Получаем настроенное действие формы
    $form_options = get_option('egro_form_options', array());
    $action = isset($form_options['form_action']) ? $form_options['form_action'] : 'mail_to';

    // Регистрируем хуки с динамическими именами
    add_action('wp_ajax_' . $action, 'egro_form_handler');
    add_action('wp_ajax_nopriv_' . $action, 'egro_form_handler');
}

function egro_form_handler()
{
   
    
    try {
        // Получаем настроенное действие формы
        $form_options = get_option('egro_form_options', array());
        $expected_action = isset($form_options['form_action']) ? $form_options['form_action'] : 'mail_to';

        if (!isset($_POST['action']) || $_POST['action'] !== $expected_action) {
            throw new Exception('Некорректное действие.');
        }

        if (empty($_POST['egro_form_nonce']) || !egro_verify_form_nonce($_POST['egro_form_nonce'])) {
            throw new Exception('Ошибка безопасности формы.');
        }

        $is_spam = !empty($_POST['egro_website']);
        $options = get_option('egro_options');

        if (!empty($options['enable_security']) && $options['enable_security'] === 'yes') {
            if (empty($_POST['egro_security_token']) || !egro_verify_security_token($_POST['egro_security_token'])) {
                throw new Exception('Ошибка проверки безопасности.');
            }
        }

        // Обрабатываем данные формы
        $form_data = array();
        foreach ($_POST as $key => $value) {
            if ($key === 'egro_field_types' || $key === 'egro_data_form_fields') {
                // Сохраняем как есть, без санитизации JSON
                $form_data[$key] = $value;
            } else if (is_array($value)) {
                $form_data[$key] = array_map('sanitize_text_field', $value);
            } else if ($key === 'email') {
                $form_data[$key] = sanitize_email($value);
            } else {
                $form_data[$key] = sanitize_text_field($value);
            }
        }

        if ($is_spam) {
            $form_data['is_spam'] = true;
        }

        if (empty($form_data['name'])) {
            throw new Exception('Пожалуйста, укажите ваше имя');
        }

        // Проверяем, нужно ли добавлять данные корзины
        $cart_data = null;
        if (egro_should_include_cart_data()) {
            $cart_data = egro_get_cart_data();
        }

        // Блокируем спам без отправки
        if ($is_spam && !empty($options['block_spam_emails']) && $options['block_spam_emails'] === 'yes') {
            wp_send_json_success(['message' => 'Сообщение успешно отправлено!', 'success' => true]);
            wp_die();
        }

        // Отправка в Telegram
        egro_send_telegram_message($form_data, $cart_data);
        wp_send_json_success(['message' => 'Заявка успешно отправлена!', 'success' => true]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage(), 'success' => false]);
    }

    wp_die();
}
