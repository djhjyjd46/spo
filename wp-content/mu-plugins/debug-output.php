<?php
/**
 * Диагностика пустых страниц в админке
 * Must-Use плагин для отладки
 */

// Перехватываем ВСЕ попытки запуска output buffering
add_action('plugins_loaded', function() {
    if (is_admin()) {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $caller = isset($trace[1]) ? ($trace[1]['file'] ?? 'unknown') . ':' . ($trace[1]['line'] ?? '0') : 'unknown';
        file_put_contents(
            WP_CONTENT_DIR . '/admin-debug.log', 
            date('[Y-m-d H:i:s] ') . "plugins_loaded in admin, OB Level: " . ob_get_level() . ", Caller: $caller\n", 
            FILE_APPEND
        );
    }
}, 1);

add_action('admin_init', function() {
    $ob_level = ob_get_level();
    $handlers = ob_list_handlers();
    file_put_contents(
        WP_CONTENT_DIR . '/admin-debug.log', 
        date('[Y-m-d H:i:s] ') . "admin_init - OB Level: $ob_level, Handlers: " . json_encode($handlers) . "\n", 
        FILE_APPEND
    );
}, 1);

add_action('shutdown', function() {
    if (is_admin()) {
        $ob_level = ob_get_level();
        $handlers = ob_list_handlers();
        file_put_contents(
            WP_CONTENT_DIR . '/admin-debug.log', 
            date('[Y-m-d H:i:s] ') . "shutdown in admin - OB Level: $ob_level, Handlers: " . json_encode($handlers) . "\n", 
            FILE_APPEND
        );
    }
}, 1);

// Перехватываем преждевременный выход
register_shutdown_function(function() {
    if (is_admin()) {
        $error = error_get_last();
        if ($error) {
            file_put_contents(
                WP_CONTENT_DIR . '/admin-debug.log', 
                date('[Y-m-d H:i:s] ') . "PHP Error: " . json_encode($error) . "\n", 
                FILE_APPEND
            );
        }
    }
});

