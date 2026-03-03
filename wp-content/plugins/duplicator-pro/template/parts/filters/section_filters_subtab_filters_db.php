<?php

/**
 * @package Duplicator
 */

use Duplicator\Controllers\SettingsPageController;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var bool $isTemplateEdit
 */
$isTemplateEdit = $tplData['isTemplateEdit'];
/** @var ?DUP_PRO_Package_Template_Entity */
$template = (isset($tplData['template']) ? $tplData['template'] : null);

$dbbuild_mode       = DUP_PRO_DB::getBuildMode();
$settingsPackageUrl = SettingsPageController::getInstance()->getMenuLink(SettingsPageController::L2_SLUG_PACKAGE);

if ($isTemplateEdit && $template != null) {
    $tableList             = explode(',', $template->database_filter_tables);
    $tableListFilterParams = [
        'dbFilterOn'        => $template->database_filter_on,
        'dbPrefixFilter'    => $template->databasePrefixFilter,
        'dbPrefixSubFilter' => $template->databasePrefixSubFilter,
        'tablesSlected'     => $tableList,
    ];
} else {
    $tableListFilterParams = [
        'dbFilterOn'        => false,
        'dbPrefixFilter'    => '',
        'dbPrefixSubFilter' => '',
        'tablesSlected'     => [],
    ];
}

?>
<div class="filter-db-tab-content">
    <div class="margin-bottom-1" >
        <?php $tplMng->render('parts/filters/tables_list_filter', $tableListFilterParams); ?>
    </div>
    <div class="dup-form-item">
        <label class="lbl-larger" >
            <?php esc_html_e("SQL Mode", 'duplicator-pro') ?>
        </label>
        <span class="input">
            <a href="<?php echo esc_url($settingsPackageUrl); ?>" target="settings">
                <?php echo esc_html($dbbuild_mode); ?>
            </a>
        </span>
    </div>
    <?php $tplMng->render('parts/filters/mysqldump_compatibility_mode'); ?>
</div>

