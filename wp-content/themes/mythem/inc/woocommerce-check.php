<?php

/**
 * Helper functions to detect WooCommerce presence safely.
 * Use mytheme_is_woocommerce_active() before calling WooCommerce functions.
 */

if (!function_exists('mytheme_is_woocommerce_active')) {
    function mytheme_is_woocommerce_active()
    {
        // Проверяем наличие основного класса плагина или функции WC
        return class_exists('WooCommerce') || function_exists('WC') || function_exists('is_woocommerce');
    }
}

if (!function_exists('mytheme_get_wc_cart_count')) {
    function mytheme_get_wc_cart_count()
    {
        if (!mytheme_is_woocommerce_active()) {
            return 0;
        }

        try {
            return WC()->cart->get_cart_contents_count();
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
