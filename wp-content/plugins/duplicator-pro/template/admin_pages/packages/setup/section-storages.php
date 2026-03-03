<?php

/**
 * @package Duplicator
 */

defined("ABSPATH") or die("");

use Duplicator\Controllers\StoragePageController;
use Duplicator\Core\CapMng;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\StoragesUtil;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$global       = DUP_PRO_Global_Entity::getInstance();
$storage_list = AbstractStorageEntity::getAll(0, 0, [StoragesUtil::class, 'sortByPriority']);

$langLocalDefaultMsg = __('Recovery Point Capable', 'duplicator-pro');
$boxOpened           = DUP_PRO_UI_ViewState::getValue('dup-pack-storage-panel');

$newStorageUrl = StoragePageController::getEditUrl();
?>
<div class="dup-box" id="dup-pack-storage-panel-area">
    <div class="dup-box-title" id="dpro-store-title">
        <i class="fas fa-server fa-sm"></i> 
        <?php esc_html_e('Storage', 'duplicator-pro') ?> <sup id="dpro-storage-title-count" class="dup-box-title-badge"></sup>
        <button class="dup-box-arrow">
            <span class="screen-reader-text">
                <?php esc_html_e('Toggle panel:', 'duplicator-pro') ?> <?php esc_html_e('Storage Options', 'duplicator-pro') ?>
            </span>
        </button>
    </div>          
    <div id="dup-pack-storage-panel" class="dup-box-panel <?php echo ($boxOpened ? '' : 'no-display'); ?>">
        <p>
            <?php esc_html_e('Choose the storage location(s) where the Backup and Installer files will be saved.', 'duplicator-pro') ?>
        </p>
        <table class="widefat pack-store-tbl dup-table-list small">
            <thead>
                <tr>
                    <th style='white-space: nowrap; width:10px;'></th>
                    <th style='width:175px'><?php esc_html_e('Type', 'duplicator-pro') ?></th>
                    <th style='width:275px'><?php esc_html_e('Name', 'duplicator-pro') ?></th>
                    <th style="white-space: nowrap"><?php esc_html_e('Location', 'duplicator-pro') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            $i                  = 0;
            $selectedStorageIds = $global->getManualModeStorageIds();
            foreach ($storage_list as $storage) {
                try {
                    if (!$storage->isSupported()) {
                        continue;
                    }

                    $i++;
                    $is_valid       = $storage->isValid();
                    $is_checked     = in_array($storage->getId(), $selectedStorageIds) && $is_valid;
                    $row_style      = ($i % 2) ? 'alternate' : '';
                    $row_style     .= ($is_valid) ? '' : ' storage-missing';
                    $row_chkid      = "dup-chkbox-{$storage->getId()}";
                    $storageEditUrl = StoragePageController::getEditUrl($storage);
                    ?>
                    <tr class="package-row <?php echo esc_attr($row_style); ?>">
                        <td>
                            <input name="edit_id" type="hidden" value="<?php echo intval($i); ?>" />
                            <input 
                                class="duppro-storage-input margin-bottom-0" 
                                id="<?php echo esc_attr($row_chkid); ?>"
                                name="_storage_ids[]"
                                onclick="DupPro.Pack.UpdateStorageCount(); return true;"
                                data-parsley-errors-container="#storage_error_container"
                                <?php if ($i == 1) { ?>
                                    data-parsley-mincheck="1" 
                                    data-parsley-required="true"
                                <?php } ?>
                                type="checkbox"
                                value="<?php echo (int) $storage->getId(); ?>"
                                <?php disabled($is_valid == false); ?>
                                <?php checked($is_checked); ?> 
                            >
                        </td>
                        <td>
                            <label for="<?php echo esc_attr($row_chkid); ?>" class="dup-store-lbl">
                            <?php
                            echo wp_kses(
                                $storage->getStypeIcon(),
                                [
                                    'i'   => [
                                        'class' => [],
                                    ],
                                    'img' => [
                                        'src'   => [],
                                        'class' => [],
                                        'alt'   => [],
                                    ],
                                ]
                            );
                            echo '&nbsp;' . esc_html($storage->getStypeName());
                            if ($storage->isLocal()) {
                                ?>
                                   <sup title="<?php echo esc_attr($langLocalDefaultMsg); ?>">
                                       <i class="fas fa-house-fire fa-fw fa-sm"></i>
                                    </sup>
                                    <?php
                            }
                            ?>
                            </label>
                        </td>
                        <td>
                            <a href="<?php echo esc_attr($storageEditUrl); ?>" target="_blank">
                                <?php
                                    echo ($is_valid == false)  ? '<i class="fa fa-exclamation-triangle fa-sm"></i> '  : '';
                                    echo esc_html($storage->getName());
                                ?>
                            </a>
                        </td>
                        <td>
                            <?php
                            echo wp_kses(
                                $storage->getHtmlLocationLink(),
                                [
                                    'a' => [
                                        'href'   => [],
                                        'target' => [],
                                    ],
                                ]
                            );
                            ?>
                        </td>
                    </tr>
                    <?php
                } catch (Exception $e) {
                    ?>
                    <tr>
                        <td colspan='5'>
                            <i><?php esc_html_e('Unable to load storage type. Please validate the setup.', 'duplicator-pro'); ?></i>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
        <div id="storage_error_container" class="duplicator-error-container"></div>
        <div class="text-right" >
            <?php if (CapMng::can(CapMng::CAP_STORAGE, false)) { ?>
                <a href="<?php echo esc_url($newStorageUrl); ?>" target="_blank">
                    [<?php esc_html_e('Add Storage', 'duplicator-pro') ?>]
                </a>
            <?php } else { ?>
                &nbsp;
            <?php } ?>
        </div>
    </div>
</div>


<script>
    jQuery(function ($)
    {
        DupPro.Pack.UpdateStorageCount = function ()
        {
            var store_count = $('#dup-pack-storage-panel input[name="_storage_ids[]"]:checked').length;
            $('#dpro-storage-title-count').html('(' + store_count + ')');
            (store_count == 0)
                    ? $('#dpro-storage-title-count').css({'color': 'red', 'font-weight': 'bold'})
                    : $('#dpro-storage-title-count').css({'color': '#444', 'font-weight': 'normal'});
        }
    });

//INIT
    jQuery(document).ready(function ($)
    {
        DupPro.Pack.UpdateStorageCount();
    });
</script>
