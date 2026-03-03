<?php

/**
 * @package Duplicator
 */

defined("ABSPATH") or die("");

/**
 * @todo movie the action in controller
 */
if (!empty($_REQUEST['action'])) {
    $nonce_action = 'duppro-template-list';
    $nonce_val    = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : $_GET['_wpnonce'];
    DUP_PRO_U::verifyNonce($nonce_val, $nonce_action);
    $action = sanitize_text_field($_REQUEST['action']);

    switch ($action) {
        case 'add':
        case 'edit':
            $display_edit = true;
            break;

        case 'bulk-delete':
            if (is_array($_REQUEST['selected_id'])) {
                $package_template_ids = array_map("intval", $_REQUEST['selected_id']);
            } else {
                $package_template_ids = [ ((int) $_REQUEST['selected_id']) ];
            }

            foreach ($package_template_ids as $package_template_id) {
                DUP_PRO_Log::trace("attempting to delete $package_template_id");
                DUP_PRO_Package_Template_Entity::deleteById($package_template_id);
            }

            break;

        case 'delete':
            $package_template_id = (int) $_REQUEST['package_template_id'];

            DUP_PRO_Log::trace("attempting to delete $package_template_id");
            DUP_PRO_Package_Template_Entity::deleteById($package_template_id);
            break;

        default:
            break;
    }
}
