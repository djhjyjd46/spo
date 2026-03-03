<?php
/*
Plugin Name: EGRO
Plugin URI: https://fastup.by/
Description: Плагин для работы с формами обратной связи и интеграцией с Telegram.
Version: 2.0.0
Author: FastUp Team
Author URI: https://fastup.by/
Text Domain: egro
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit;
}

define('EGRO_PATH', plugin_dir_path(__FILE__));
define('EGRO_URL', plugin_dir_url(__FILE__));
define('EGRO_VERSION', '2.0.0');

// Загружаем основные файлы
require_once EGRO_PATH . 'includes/plugin-activation.php';
require_once EGRO_PATH . 'includes/form-handler.php';
require_once EGRO_PATH . 'includes/admin-page.php';
require_once EGRO_PATH . 'includes/telegram.php';

// Инициализация системы обновлений
global $egro_update_checker;

function egro_init_update_checker()
{
    global $egro_update_checker;

    $update_checker_path = EGRO_PATH . 'plugin-update-checker/plugin-update-checker.php';

    if (file_exists($update_checker_path)) {
        require_once $update_checker_path;

        if (class_exists('YahnisElsts\PluginUpdateChecker\v5\PucFactory')) {
            $egro_update_checker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
                'https://fastup.by/plugin-update.json',
                __FILE__,
                'egro'
            );
        }
    }
}

// Хуки активации/деактивации/удаления
register_activation_hook(__FILE__, 'egro_activate');
register_deactivation_hook(__FILE__, 'egro_deactivate');
register_uninstall_hook(__FILE__, 'egro_uninstall');

function egro_deactivate()
{
    // Сохраняем настройки при деактивации
}

function egro_uninstall()
{
    // При удалении плагина удаляем его настройки
    delete_option('egro_form_options');
    delete_option('egro_telegram_options');
    delete_option('egro_security_options');
}

// Инициализация плагина
add_action('plugins_loaded', 'egro_init');
function egro_init()
{
    // Инициализация системы обновлений
    egro_init_update_checker();

    // Регистрация компонентов плагина
    add_action('admin_menu', 'egro_admin_menu');

    add_action('wp_ajax_egro_check_for_updates', 'egro_ajax_check_for_updates');

    add_action('wp_enqueue_scripts', 'egro_form_styles');
    add_action('wp_enqueue_scripts', 'egro_security_scripts');

    if (function_exists('egro_init_form')) {
        egro_init_form();
    }

    // Загрузка текстового домена для переводов
    load_plugin_textdomain('egro', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

function egro_form_styles()
{
    // Проверка, что мы на странице с формой
    if (!is_admin()) {
        wp_enqueue_style(
            'egro-form-css',
            EGRO_URL . 'assets/css/frontend/form.css',
            array(),
            EGRO_VERSION
        );
    }
}

function egro_security_scripts()
{
    // Проверка, что мы на странице с формой
    if (!is_admin() && (is_page() || is_single())) {
        // Подключаем основной скрипт формы (минифицированная версия)
        wp_enqueue_script(
            'egro-form-js',
            EGRO_URL . 'assets/js/frontend/form.min.js',
            array(),
            EGRO_VERSION,
            array(
                'in_footer' => true,
                'strategy' => 'defer'
            )
        );
        
        // Подключаем скрипт безопасности (минифицированная версия)
        wp_enqueue_script(
            'egro-security-js',
            EGRO_URL . 'assets/js/frontend/form-security.min.js',
            array(), 
            EGRO_VERSION,
            array(
                'in_footer' => true,
                'strategy' => 'defer'
            )
        );
        
        // Локализация для скрипта безопасности
        wp_localize_script(
            'egro-security-js',
            'egro_security',
            array(
                'token' => egro_get_security_token(),
                'nonce' => egro_get_form_nonce()
            )
        );
        
        // Локализация для основного скрипта формы
        $form_options = get_option('egro_form_options', array());
        $form_action = isset($form_options['form_action']) ? $form_options['form_action'] : 'mail_to';
        
        wp_localize_script(
            'egro-form-js',
            'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'action' => $form_action,
                'use_data_form' => isset($form_options['use_data_form']) ? $form_options['use_data_form'] : 'no'
            )
        );
    }
}

// Добавляем defer к скриптам плагина для улучшения производительности
function egro_add_defer_to_scripts($tag, $handle, $src)
{
    // Список скриптов плагина
    $defer_scripts = array('egro-form-js', 'egro-security-js', 'Egro-form-js');
    
    if (in_array($handle, $defer_scripts)) {
        // Добавляем defer если его еще нет
        if (strpos($tag, 'defer') === false) {
            $tag = str_replace(' src', ' defer src', $tag);
        }
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'egro_add_defer_to_scripts', 10, 3);

// Добавляем preload для критически важных ресурсов
function egro_add_resource_hints($urls, $relation_type)
{
    if ($relation_type === 'preload') {
        if (!is_admin() && (is_page() || is_single())) {
            $urls[] = array(
                'href' => EGRO_URL . 'assets/js/frontend/form.min.js',
                'as' => 'script'
            );
            $urls[] = array(
                'href' => EGRO_URL . 'assets/js/frontend/form-security.min.js',
                'as' => 'script'
            );
        }
    }
    
    return $urls;
}
add_filter('wp_resource_hints', 'egro_add_resource_hints', 10, 2);

// Только для админки
function egro_admin_enqueue_scripts($hook)
{
    if (strpos($hook, 'egro-settings') !== false) {
        // Загружаем стили для админки
        wp_enqueue_style(
            'egro-admin-css',
            EGRO_URL . 'assets/css/admin/admin-style.css',
            array(),
            EGRO_VERSION
        );
    }
}

add_action('admin_enqueue_scripts', 'egro_admin_enqueue_scripts');

// AJAX-обработчик для проверки обновлений
function egro_ajax_check_for_updates()
{
    global $egro_update_checker;

    if (!current_user_can('manage_options')) {
        wp_send_json_error('У вас нет прав для выполнения этого действия.');
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'egro_check_updates_nonce')) {
        wp_send_json_error('Ошибка безопасности. Обновите страницу и попробуйте снова.');
    }

    if (!isset($egro_update_checker)) {
        wp_send_json_error('Система обновлений не инициализирована.');
    }

    // Принудительно проверяем обновления
    $egro_update_checker->checkForUpdates();

    wp_send_json_success('Проверка обновлений выполнена успешно.');
}

// При обновлении плагина сохраняем настройки пользователя
function egro_handle_update()
{
    $current_version = get_option('egro_version', '0');

    // Если версия изменилась, обновляем настройки
    if (version_compare($current_version, EGRO_VERSION, '<')) {
        // Добавляем новые настройки форм при обновлении
        if (!get_option('egro_form_options')) {
            add_option('egro_form_options', egro_form_get_default_settings());
        }

        // Обновляем версию в базе данных
        update_option('egro_version', EGRO_VERSION);

        // Логируем обновление
        egro_log('Плагин обновлен с версии ' . $current_version . ' до версии ' . EGRO_VERSION);
    }
}
add_action('plugins_loaded', 'egro_handle_update', 5); // Запускаем до основной инициализации
