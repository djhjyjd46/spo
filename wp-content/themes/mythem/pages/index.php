<?php
add_action('template_redirect', function () {
    if (is_page(42) && !is_admin()) {
        wp_redirect(home_url('/pages/'));
        exit;
    }
});