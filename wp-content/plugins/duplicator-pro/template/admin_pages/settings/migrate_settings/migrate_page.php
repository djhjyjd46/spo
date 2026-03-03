<?php

/**
 * @package Duplicator
 */

defined("ABSPATH") or die("");

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Utils\Settings\MigrateSettings;
use Duplicator\Addons\ProBase\License\License;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

/* FOR PERSONAL LICENSE JUST SHOW MESSAGE */
if (!License::can(License::CAPABILITY_IMPORT_SETTINGS)) {
    $tplMng->render('admin_pages/settings/migrate_settings/no_capatibily');
    return;
}



/* LET'S PERFORM FREELANCE+ SETTINGS */

$view_state          = DUP_PRO_UI_ViewState::getArray();
$ui_css_export_panel = (isset($view_state['dpro-tools-export-panel']) && $view_state['dpro-tools-export-panel']) ? 'display:block' : 'display:block';
$ui_css_import_panel = (isset($view_state['dpro-tools-import-panel']) && $view_state['dpro-tools-import-panel']) ? 'display:block' : 'display:block';

// POST BACK
$_REQUEST['action'] = !empty($_REQUEST['action']) ? $_REQUEST['action'] : 'display';

$error_message   = null;
$success_message = null;

switch ($_REQUEST['action']) {
    case 'dpro-export':
    case 'dpro-import':
        try {
            if (MigrateSettings::import($_FILES['import-file']['tmp_name'], $_POST['import-opts']) == false) {
                throw new Exception('Import failed.');
            }
            $success_message = 'Successfully imported.';
        } catch (Exception $ex) {
            $error_message = 'Import Error: ' . $ex->getMessage() . "<br>\n" . $ex->getFile() . ':'  . $ex->getLine();
        }
        break;
}

if ($error_message !== null) { ?>
    <div id="message" class="below-h2 error">
        <p><?php echo esc_html($error_message); ?></p>
    </div>
    <?php
} elseif ($success_message !== null) { ?>
    <div id="message" class="below-h2 updated">
        <p><?php echo esc_html($success_message); ?></p>
    </div>
    <?php
}
?>

<?php $tplMng->render('admin_pages/settings/migrate_settings/export'); ?>
<hr size="1" />
<?php $tplMng->render('admin_pages/settings/migrate_settings/import'); ?>

<?php add_thickbox();

