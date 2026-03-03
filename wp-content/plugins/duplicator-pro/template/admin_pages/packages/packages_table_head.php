<?php

/**
 * Duplicator Backup row in table Backups list
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$tooltipContent = $tplMng->render('admin_pages/packages/packages_table_head_status_icons', [], false);
?>
<h2 class="screen-reader-text"><?php esc_html_e('Backups list', 'duplicator-pro') ?></h2>
<thead>
    <tr>
        <th class="dup-check-column" style="width:10px;">
            <input 
                type="checkbox" 
                id="dup-chk-all" 
                title="<?php esc_attr_e("Select all Backups", 'duplicator-pro') ?>" 
                onclick="DupPro.Pack.SetDeleteAll()" 
            >
        </th>
        <th class="dup-name-column" >
            <?php esc_html_e("Name", 'duplicator-pro') ?>
        </th>
        <th class="dup-note-column">
            <?php esc_html_e("Note", 'duplicator-pro') ?>
        </th>
        <th class="dup-storages-column">
            <?php esc_html_e("Storages", 'duplicator-pro') ?>
        </th>
        <th class="dup-flags-column">
            <?php esc_html_e("Status", 'duplicator-pro') ?>&nbsp;
            <i 
                class="fa-solid fa-circle-info"
                data-tooltip-title="<?php esc_attr_e("Status Icons", 'duplicator-pro'); ?>"
                data-tooltip="<?php echo esc_attr($tooltipContent); ?>"
            ></i>
        </th>
        <th class="dup-size-column">
            <?php esc_html_e("Size", 'duplicator-pro') ?>
        </th>
        <th class="dup-created-column">
            <?php esc_html_e("Created", 'duplicator-pro') ?>
        </th>
        <th class="dup-age-column">
            <?php esc_html_e("Age", 'duplicator-pro') ?>
        </th>
        <th class="dup-download-column" style="width:75px;"></th>
        <th class="dup-restore-column" style="width:25px;"></th>
        <th id="dup-header-chkall" class="dup-details-column" >
        <?php if ($tplData['totalElements'] > 0) { ?>
                <span class="link-style" title="<?php esc_attr_e('Expand/Collapse All', 'duplicator-pro'); ?>" >
                    <i class="fa-solid fa-plus"></i>
                </span>
        <?php } ?>
        </th>
    </tr>
</thead>
