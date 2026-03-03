<?php
if (!defined('ABSPATH')) exit;

function egro_send_telegram_message($form_data, $cart_data = null)
{
    // Ваш токен по умолчанию
    $default_bot_token = '6971328945:AAEOE6s8ajiKEzUg-G3AAKVP4-uTpgvSL8U';

    $options = get_option('egro_telegram_options', []);

    if (empty($options['enabled']) || $options['enabled'] !== 'yes') {
        return;
    }

    // Если владелец указал свой токен — используем его, иначе ваш
    $bot_token = trim($options['bot_token'] ?? '') ?: $default_bot_token;
    $chat_ids = explode(',', $options['chat_id'] ?? '');
    
    // Убираем дубликаты chat_id
    $chat_ids = array_unique(array_filter(array_map('trim', $chat_ids)));

    if (!$bot_token || empty($chat_ids)) {
        return;
    }

    // Формируем сообщение из данных формы
    $message = "📝 Новая заявка с сайта " . get_bloginfo('name') . "\n\n";
    
    // Добавляем пометку СПАМ если обнаружено
    if (!empty($form_data['is_spam'])) {
        $message = "⚠️ Обнаружен спам\n" . $message;
    }
    
    foreach ($form_data as $key => $value) {
        // Пропускаем служебные поля
        if (in_array($key, ['action', 'egro_form_nonce', 'egro_security_token', 'egro_website', 'egro_field_types', 'egro_data_form_fields', 'form_data_attribute', 'modal_trigger_data', 'is_spam', 'policy', 'conf', 'confidentiality'])) {
            continue;
        }
        
        // Пропускаем пустые значения
        if (empty($value) && $value !== '0' && $value !== 0) {
            continue;
        }
        
        // Преобразуем ключи в русский текст
        $label = ucfirst(str_replace('_', ' ', $key));
        switch ($key) {
            case 'name':
                $label = 'Имя';
                break;
            case 'email':
                $label = 'Email';
                break;
            case 'phone':
            case 'tel':
                $label = 'Телефон';
                break;
            case 'message':
                $label = 'Сообщение';
                break;
            case 'comment':
                $label = 'Комментарий';
                break;
            case 'conf':
            case 'confidentiality':
                $label = 'Конфиденциальность';
                break;
            case 'social':
                $label = 'Соц. сети';
                break;
        }
        
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        
        $message .= $label . ": " . $value . "\n";
    }
    
    // Добавляем данные корзины если есть
    if ($cart_data && !empty($cart_data['items'])) {
        $message .= "\n🛒 Корзина:\n";
        foreach ($cart_data['items'] as $item) {
            $message .= "▫️ " . $item['name'] . " - " . $item['quantity'] . " x " . strip_tags($item['price']) . "\n";
        }
        $message .= "\n💰 Итого: " . strip_tags($cart_data['total']);
    }

    foreach ($chat_ids as $chat_id) {
        $chat_id = trim($chat_id);
        if (!$chat_id) continue;

        $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
        $args = [
            'body' => [
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'HTML'
            ],
            'timeout' => 10
        ];

        $response = wp_remote_post($url, $args);
    }
}
