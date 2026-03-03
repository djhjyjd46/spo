<?php
if (!defined('ABSPATH')) { exit; }

function egro_form_get_default_settings() {
    return array('use_data_form' => 'yes', 'form_action' => 'mail_to');
}

function egro_security_get_default_settings() {
    return array('security_token' => wp_generate_password(32, false), 'enable_security' => 'yes', 'block_spam_emails' => 'yes');
}

function egro_activate() {
    $upload_dir = wp_upload_dir();
    $logs_dir = $upload_dir['basedir'] . '/egro_logs';
    if (!file_exists($logs_dir)) { wp_mkdir_p($logs_dir); }
    $default_form_options = egro_form_get_default_settings();
    $current_form_options = get_option('egro_form_options', array());
    if (!empty($current_form_options)) {
        update_option('egro_form_options', array_merge($default_form_options, $current_form_options));
    } else {
        add_option('egro_form_options', $default_form_options);
    }
    $default_security_options = egro_security_get_default_settings();
    $current_security_options = get_option('egro_security_options', array());
    if (!empty($current_security_options)) {
        update_option('egro_security_options', array_merge($default_security_options, $current_security_options));
    } else {
        add_option('egro_security_options', $default_security_options);
    }
    $default_telegram_options = array('enabled' => 'yes', 'bot_token' => '', 'chat_id' => '');
    $current_telegram_options = get_option('egro_telegram_options', array());
    if (!empty($current_telegram_options)) {
        update_option('egro_telegram_options', array_merge($default_telegram_options, $current_telegram_options));
    } else {
        add_option('egro_telegram_options', $default_telegram_options);
    }
}

function egro_restore_default_settings() {
    update_option('egro_form_options', egro_form_get_default_settings());
    update_option('egro_security_options', egro_security_get_default_settings());
    return egro_form_get_default_settings();
}

function egro_log($message) {
    $upload_dir = wp_upload_dir();
    $log_file = $upload_dir['basedir'] . '/egro_logs/egro.log';
    $log_entry = "[" . date('Y-m-d H:i:s') . "] {$message}\n";
    @file_put_contents($log_file, $log_entry, FILE_APPEND);
    return true;
}

function egro_get_security_token() {
    return wp_create_nonce('egro_form_security');
}

function egro_verify_security_token($token) {
    return wp_verify_nonce($token, 'egro_form_security');
}

function egro_get_form_nonce() {
    return wp_create_nonce('egro_form_nonce');
}

function egro_verify_form_nonce($nonce) {
    return wp_verify_nonce($nonce, 'egro_form_nonce');
}

function egro_get_email_recipient() {
    if (function_exists('get_field')) {
        $acf_email = get_field('emailTo', 'option');
        if (!empty($acf_email) && is_email($acf_email)) {
            return $acf_email;
        }
    }
    return get_option('admin_email');
}