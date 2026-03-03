<?php
// Запрет прямого доступа к файлу
if (!defined('ABSPATH')) {
    exit;
}

// Добавление пункта меню в админ-панель
function egro_admin_menu()
{
    add_menu_page(
        'EGRO', // Заголовок страницы
        'EGRO', // Название пункта меню
        'manage_options', // Требуемое право пользователя
        'egro-settings', // Идентификатор меню
        'egro_settings_page', // Функция отображения страницы
        'dashicons-email', // Иконка
        80 // Позиция в меню
    );

    // Регистрация настроек
    add_action('admin_init', 'egro_register_settings');
}

// Регистрация настроек в WordPress
function egro_register_settings()
{
    // Регистрация настроек для форм
    register_setting('egro_form_settings_group', 'egro_form_options', 'egro_form_sanitize_options');

    // Регистрация настроек для Telegram
    register_setting('egro_telegram_settings_group', 'egro_telegram_options', 'egro_telegram_sanitize_options');

    // Регистрация настроек для безопасности
    register_setting('egro_security_settings_group', 'egro_security_options', 'egro_security_sanitize_options');
}

// Санитизация данных настроек безопасности перед сохранением
function egro_security_sanitize_options($input)
{
    $new_input = array();

    // Настройки безопасности
    $new_input['enable_security'] = isset($input['enable_security']) && $input['enable_security'] === 'yes' ? 'yes' : 'no';
    $new_input['block_spam_emails'] = isset($input['block_spam_emails']) && $input['block_spam_emails'] === 'yes' ? 'yes' : 'no';
    $new_input['security_token'] = isset($input['security_token']) ? sanitize_text_field($input['security_token']) : wp_generate_password(32, false);

    return $new_input;
}

// Санитизация данных настроек форм перед сохранением
function egro_form_sanitize_options($input)
{
    $new_input = array();

    // Настройки data-form атрибутов
    $new_input['use_data_form'] = isset($input['use_data_form']) ? 'yes' : 'no';

    // Действие формы (селектор)
    $new_input['form_action'] = isset($input['form_action']) ? sanitize_key($input['form_action']) : 'mail_to';

    return $new_input;
}

// Санитизация данных настроек Telegram перед сохранением
function egro_telegram_sanitize_options($input)
{
    $new_input = [];
    $new_input['enabled'] = isset($input['enabled']) && $input['enabled'] === 'yes' ? 'yes' : 'no';
    $new_input['bot_token'] = sanitize_text_field($input['bot_token'] ?? '');
    $new_input['chat_id'] = sanitize_text_field($input['chat_id'] ?? '');
    return $new_input;
}

// Функция для отрисовки страницы настроек
function egro_settings_page()
{
    // Добавляем локализацию для admin-script.js
    wp_localize_script('egro-admin-js', 'egro_admin', array(
        'nonce_restore_defaults' => wp_create_nonce('egro_restore_defaults_nonce'),
        'nonce_updates' => wp_create_nonce('egro_check_updates_nonce'),
        'ajaxurl' => admin_url('admin-ajax.php')
    ));

    wp_enqueue_style('egro-admin-css', EGRO_URL . 'assets/css/admin.css', array(), EGRO_VERSION);

    $security_options = get_option('egro_security_options', array());
    $form_options = get_option('egro_form_options', array());

    // Определение активной вкладки
    $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'telegram';

    // Загружаем файлы с функциями вкладок
    require_once EGRO_PATH . 'includes/tabs/security-tab.php';
    require_once EGRO_PATH . 'includes/tabs/form-settings-tab.php';
    require_once EGRO_PATH . 'includes/tabs/about-tab.php';
    require_once EGRO_PATH . 'includes/tabs/telegram-tab.php';

?>
    <div class="wrap">
        <h1>Настройки EGRO</h1>

        <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) : ?>
            <div class="notice notice-success is-dismissible">
                <p>Настройки успешно сохранены!</p>
            </div>
        <?php endif; ?>

        <!-- Вкладки навигации -->
        <h2 class="nav-tab-wrapper">
            <a href="?page=egro-settings&tab=telegram"
                class="nav-tab <?php echo $active_tab == 'telegram' ? 'nav-tab-active' : ''; ?>">Telegram</a>
            <a href="?page=egro-settings&tab=security"
                class="nav-tab <?php echo $active_tab == 'security' ? 'nav-tab-active' : ''; ?>">Безопасность</a>
            <a href="?page=egro-settings&tab=forms"
                class="nav-tab <?php echo $active_tab == 'forms' ? 'nav-tab-active' : ''; ?>">Настройки форм</a>
            <a href="?page=egro-settings&tab=about"
                class="nav-tab <?php echo $active_tab == 'about' ? 'nav-tab-active' : ''; ?>">О плагине</a>
        </h2>

        <div class="tab-content">
            <?php
            // Отображение содержимого активной вкладки
            switch ($active_tab) {
                case 'telegram':
                    egro_telegram_settings_tab(get_option('egro_telegram_options', []));
                    break;

                case 'security':
                    egro_security_settings_tab($security_options);
                    break;

                case 'forms':
                    egro_form_settings_tab($form_options);
                    break;

                case 'about':
                    egro_about_tab();
                    break;

                default: // 'telegram'
                    egro_telegram_settings_tab(get_option('egro_telegram_options', []));
                    break;
            } ?>
        </div>
    </div>
    <?php
}
