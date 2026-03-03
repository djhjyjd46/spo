<?php
defined("ABSPATH") or die("");

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapJson;

require_once(DUPLICATOR____PATH . '/classes/package/class.pack.php');

wp_enqueue_script('dup-pro-handlebars');

if (empty($_POST)) {
    // Refresh 'fix'
    $redirect = PackagesPageController::getInstance()->getPackageBuildS1Url();
    ob_start();
    ?>
    <script>
        window.location.href = <?php echo SnapJson::jsonEncode($redirect); ?>;
    </script>
    <?php
    $html = (string) ob_get_clean();
    die($html);
}

$global = DUP_PRO_Global_Entity::getInstance();

//echo '<pre>', var_export($_REQUEST, true), '</pre>';
/**
 * @todo move this in actions controller
 */
if (!empty($_REQUEST['action']) && $_REQUEST['action'] === 'template-create') {
    $storage_ids = isset($_REQUEST['_storage_ids']) ? $_REQUEST['_storage_ids'] : array();
    $template_id = (int) $_REQUEST['template_id'];
    $template    = DUP_PRO_Package_Template_Entity::getById($template_id);

    // always set the manual template since it represents the last thing that was run
    DUP_PRO_Package::set_manual_template_from_post($_REQUEST);
    $global->setManualModeStorageIds($storage_ids);
    $global->save();

    DUP_PRO_Package::set_temporary_package_from_template_and_storages($template_id, $storage_ids);
}

$Package               = DUP_PRO_Package::get_temporary_package();
$package_list_url      = ControllersManager::getMenuLink(ControllersManager::PACKAGES_SUBMENU_SLUG);
$archive_export_onlydb = $Package->isDBOnly();
$messageText           = TplMng::getInstance()->render('admin_pages/packages/scan/error_message', [], false);

/** @var bool */
$blur = TplMng::getInstance()->getGlobalValue('blurCreate');

$validator = $Package->validateInputs();
if (!$validator->isSuccess()) {
    ?>
    <form id="form-duplicator scan-result" method="post" action="<?php echo $package_list_url ?>">
        <!--  ERROR MESSAGE -->
        <div id="dup-msg-error">
            <div class="dup-hdr-error"><i class="fa fa-exclamation-circle"></i> <?php esc_html_e('Input fields not valid', 'duplicator-pro'); ?></div>
            <i><?php esc_html_e('Please try again!', 'duplicator-pro'); ?></i><br/>
            <div style="text-align:left">
                <b><?php esc_html_e("Server Status:", 'duplicator-pro'); ?></b> &nbsp;
                <div id="dup-msg-error-response-status" style="display:inline-block"></div><br/>
                <b><?php esc_html_e("Error Message:", 'duplicator-pro'); ?></b>
                <div id="dup-msg-error-response-text">
                    <ul>
                        <?php
                        $validator->getErrorsFormat("<li>%s</li>");
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <input
            type="button"
            value="&#9664; <?php esc_html_e("Back", 'duplicator-pro') ?>"
            class="button hollow secondary dup-go-back-to-new1"
        >
    </form>
    <?php
    return;
}
?>
<form 
    id="form-duplicator" 
    class="<?php echo ($blur ? 'dup-mock-blur' : ''); ?>  scan-result" 
    method="post" 
    action="<?php echo $package_list_url ?>"
>
    <input type="hidden" name="create_from_temp" value="1" />

    <div id="dup-progress-area">

        <!--  PROGRESS BAR -->
        <div class="dup-progress-bar-area">
            <div class="dup-pro-title" >
                <?php esc_html_e('Scanning Site', 'duplicator-pro'); ?>
            </div>
            <div class="dup-pro-meter-wrapper" >
                <div class="dup-pro-meter green dup-pro-fullsize">
                    <span></span>
                </div>
                <span class="text"></span>
            </div>
            <b><?php esc_html_e('Please Wait...', 'duplicator-pro'); ?></b><br/><br/>
            <i><?php esc_html_e('Keep this window open during the scan process.', 'duplicator-pro'); ?></i><br/>
            <i><?php esc_html_e('This can take several minutes.', 'duplicator-pro'); ?></i><br/>
        </div>

        <!--  SCAN DETAILS REPORT -->
        <div id="dup-msg-success" style="display:none">
            <div style="text-align:center">
                <div class="dup-hdr-success">
                    <i class="far fa-check-square fa-nr"></i> <?php esc_html_e('Scan Complete', 'duplicator-pro'); ?>
                </div>
                <div id="dup-msg-success-subtitle">
                    <?php esc_html_e("Process Time:", 'duplicator-pro'); ?> <span id="data-rpt-scantime"></span>
                </div>
            </div>
            <div class="details">
                <?php
                include(DUPLICATOR____PATH . '/views/packages/main/s2.scan2.server.php');
                echo '<br/>';
                include(DUPLICATOR____PATH . '/views/packages/main/s2.scan3.archive.php');
                ?>
            </div>
        </div>

        <!--  ERROR MESSAGE -->
        <div id="dup-msg-error" style="display:none">
            <div class="dup-hdr-error"><i class="fa fa-exclamation-circle"></i> <?php esc_html_e('Scan Error', 'duplicator-pro'); ?></div>
            <i><?php esc_html_e('Please try again!', 'duplicator-pro'); ?></i><br/>
            <div style="text-align:left">
                <b><?php esc_html_e("Server Status:", 'duplicator-pro'); ?></b> &nbsp;
                <div id="dup-msg-error-response-status" style="display:inline-block"></div><br/>
                <b><?php esc_html_e("Error Message:", 'duplicator-pro'); ?></b>
                <div id="dup-msg-error-response-text"></div>
            </div>
        </div>
    </div>

    <!-- WARNING CONTINUE -->
    <div id="dpro-scan-warning-continue">
        <div class="msg2">
            <?php
            _e("Scan checks are not required to pass, however they could cause issues on some systems.", 'duplicator-pro');
            echo '<br/>';
            _e("Please review the details for each section by clicking on the detail title.", 'duplicator-pro');
            ?>
        </div>
    </div>

    <div id="dpro-confirm-area">
        <?php
        esc_html_e('Do you want to continue?', 'duplicator-pro');
        echo '<br/> ';
        esc_html_e('At least one or more checkboxes were checked in "Quick Filters".', 'duplicator-pro')
        ?>
        <br/>
        <i style="font-weight:normal">
            <?php esc_html_e('To apply a "Quick Filter" click the "Add Filters & Rescan" button', 'duplicator-pro') ?>
        </i><br/>
        <input 
            type="checkbox" 
            id="dpro-confirm-check" 
            onclick="jQuery('#dup-build-button').removeAttr('disabled');"
            class="margin-bottom-0"
        >
        <?php esc_html_e('Yes. Continue without applying any file filters.', 'duplicator-pro') ?>
    </div>

    <div class="dup-button-footer" style="display:none">
        <input
            type="button"
            class="button hollow secondary small dup-go-back-to-new1"
            value="&#9664; <?php esc_html_e("Back", 'duplicator-pro') ?>"
            class="button dup-go-back-to-new1"
        >
        <input 
            type="button" 
            class="button hollow secondary small"
            value="<?php esc_attr_e("Rescan", 'duplicator-pro') ?>" 
            onclick="DupPro.Pack.reRunScanner()"
        >
        <input 
            type="button" 
            onclick="DupPro.Pack.startBuild();" 
            class="button primary small" 
            id="dup-build-button" 
            value='<?php esc_attr_e("Create Backup", 'duplicator-pro') ?> &#9654'
        >
    </div>
</form>

<script>
    jQuery(document).ready(function ($)
    {
        let errorMessage = <?php echo SnapJson::jsonEncode($messageText); ?>;
        DupPro.Pack.WebServiceStatus = {
            Pass: 1,
            Warn: 2,
            Error: 3,
            Incomplete: 4,
            ScheduleRunning: 5
        }

        DupPro.Pack.runScanner = function (callbackOnSuccess) {
            Duplicator.Util.ajaxWrapper(
                {
                    action: 'duplicator_pro_package_scan',
                    nonce: '<?php echo wp_create_nonce('duplicator_pro_package_scan'); ?>'
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    if (funcData.ScanStatus !== undefined && funcData.ScanStatus == 'running') {
                        DupPro.Pack.runScanner();
                    } else {
                        var status = funcData.Status || 3;
                        var message = funcData.Message ||
                            "Unable to read JSON from service. <br/> See: /wp-admin/admin-ajax.php?action=duplicator_pro_package_scan";
                        console.log(funcData);

                        if (status == DupPro.Pack.WebServiceStatus.Pass) {
                            DupPro.Pack.loadScanData(funcData);
                            if (typeof callbackOnSuccess === "function") {
                                callbackOnSuccess(funcData);
                            }
                            $('.dup-button-footer').show();
                        } else if (status == DupPro.Pack.WebServiceStatus.ScheduleRunning) {
                            // as long as its just saying that someone blocked us keep trying
                            console.log('retrying scan in 300 ms...');
                            setTimeout(DupPro.Pack.runScanner, 300);
                        } else {
                            $('.dup-progress-bar-area, #dup-build-button').hide();
                            $('#dup-msg-error-response-status').html(status);
                            $('#dup-msg-error-response-text').html(message + errorMessage);
                            $('#dup-msg-error').show();
                            $('.dup-button-footer').show();
                        }
                    }
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    var status = data.status + ' -' + data.statusText;
                    $('.dup-progress-bar-area, #dup-build-button').hide();
                    $('#dup-msg-error-response-status').html(status)
                    $('#dup-msg-error-response-text').html(data.message + errorMessage);
                    $('#dup-msg-error, .dup-button-footer').show();
                    console.log(data);
                },
                {
                    showProgress: false,
                    timeout: 0
                }
            );
        }

        DupPro.Pack.reRunScanner = function (callbackOnSuccess)
        {
            $('#dup-msg-success,#dup-msg-error,.dup-button-footer,#dpro-confirm-area').hide();
            $('#dpro-confirm-check').prop('checked', false);
            $('.dup-progress-bar-area').show();
            $('#dpro-scan-warning-continue').hide();
            DupPro.Pack.runScanner(callbackOnSuccess);
        }

        DupPro.Pack.loadScanData = function (data)
        {
            try {
                var errMsg = "unable to read";
                $('.dup-progress-bar-area').hide();
                //****************
                // BRAND
                // #data-srv-brand-check
                // #data-srv-brand-name
                // #data-srv-brand-note

                $("#data-srv-brand-name").text(data.SRV.Brand.Name);
                if (data.SRV.Brand.LogoImageExists) {
                    $("#data-srv-brand-note").html(data.SRV.Brand.Notes);
                } else {
                    $("#data-srv-brand-note")
                        .html("<?php
                            esc_html_e(
                                "WARNING! Logo images no longer can be found inside brand. Please edit this brand and place new images. After that you can build your Backup with this brand.",
                                'duplicator-pro'
                            ); ?>"
                        );
                }

                //****************
                //REPORT
                var base = $('#data-rpt-scanfile').attr('href');
                $('#data-rpt-scanfile').attr('href', base + '&scanfile=' + data.RPT.ScanFile);
                $('#data-rpt-scantime').text(data.RPT.ScanTime || 0);

                DupPro.Pack.initArchiveFilesData(data);
                DupPro.Pack.setScanStatus(data);

                //Addon Sites
                if (data.ARC.FilterInfo.Dirs.AddonSites !== undefined && data.ARC.FilterInfo.Dirs.AddonSites.length > 0) {
                    $("#addonsites-block").show();
                }
                $('#dup-msg-success').show();

                //****************
                //DATABASE
                var html = "";
                var DB_TableRowMax = <?php echo DUPLICATOR_PRO_SCAN_DB_TBL_ROWS; ?>;
                var DB_TableSizeMax = <?php echo DUPLICATOR_PRO_SCAN_DB_TBL_SIZE; ?>;
                if (data.DB.DBExcluded && data.DB.Status.Success) {
                    $('#data-db-size1').text(data.DB.Size || errMsg);
                } else if (data.DB.Status.Success) {
                    $('#data-db-size1').text(data.DB.Size || errMsg);
                    $('#data-db-size2').text(data.DB.Size || errMsg);
                    $('#data-db-rows').text(data.DB.Rows || errMsg);
                    $('#data-db-tablecount').text(data.DB.TableCount || errMsg);
                    //Table Details
                    if (data.DB.TableList == undefined || data.DB.TableList.length == 0) {
                        html = '<?php esc_html_e("Unable to report on any tables", 'duplicator-pro') ?>';
                    } else {
                        $.each(data.DB.TableList, function (i) {
                            html += '<b>' + i + '</b><br/>';
                            html += '<table><tr>';
                            $.each(data.DB.TableList[i], function (key, val) {
                                switch (key) {
                                    case 'Case':
                                        color = (val == 1) ? 'maroon' : 'black';
                                        html += '<td style="color:' + color + '"><?php echo esc_js(__('Uppercase:', 'duplicator-pro')) ?> ' + val + '</td>';
                                        break;
                                    case 'Rows':
                                        color = (val > DB_TableRowMax) ? 'red' : 'black';
                                        html += '<td style="color:' + color + '"><?php echo esc_js(__('Rows:', 'duplicator-pro')) ?> ' + val + '</td>';
                                        break;
                                    case 'USize':
                                        color = (parseInt(val) > DB_TableSizeMax) ? 'red' : 'black';
                                        html += '<td style="color:' + color + '">';
                                        html += '<?php echo esc_js(__('Size:', 'duplicator-pro')) ?> ' + data.DB.TableList[i]['Size'];
                                        html += '</td>';
                                        break;
                                }
                            });
                            html += '</tr></table>';
                        });
                    }
                    $('#data-db-tablelist').html(html);
                } else {
                    html = '<?php esc_html_e("Unable to report on database stats", 'duplicator-pro') ?>';
                    $('#dup-scan-db').html(html);
                }

                var isWarn = false;
                for (key in data.ARC.Status) {
                    if (!data.ARC.Status[key]) {
                        isWarn = true;
                    }
                }

                if (!isWarn) {
                    if (!data.DB.Status.Size) {
                        isWarn = true;
                    }
                }

                if (!isWarn && !data.DB.Status.Rows) {
                    isWarn = true;
                }

                if (!isWarn && !data.SRV.PHP.ALL) {
                    isWarn = true;
                }

                if (!isWarn && (data.SRV.WP.version == false || data.SRV.WP.core == false)) {
                    isWarn = true;
                }

                if (isWarn) {
                    $('#dpro-scan-warning-continue').show();
                } else {
                    $('#dpro-scan-warning-continue').hide();
                    $('#dup-build-button').prop("disabled", false);
                }
            } catch (err) {
                err += '<br/> Please try again!'
                $('#dup-msg-error-response-status').html("n/a")
                $('#dup-msg-error-response-text').html(err);
                $('#dup-msg-error, .dup-button-footer').show();
                $('#dup-build-button').hide();
            }
        }

        //Starts the build process
        DupPro.Pack.startBuild = function ()
        {
            // disable to prevent double click
            $('#dup-build-button').prop('disabled', true);

            if ($('#dpro-confirm-check').is(":checked")) {
                $('#form-duplicator').submit();
            }

            var sizeChecks = $('#hb-files-large-jstree').length ? $('#hb-files-large-jstree').jstree(true).get_checked() : 0;
            var addonChecks = $('#hb-addon-sites-result input:checked');
            var utf8Checks = $('#hb-files-utf8-jstree').length ? $('#hb-files-utf8-jstree').jstree(true).get_checked() : 0;
            if (sizeChecks.length > 0 || addonChecks.length > 0 || utf8Checks.length > 0) {
                $('#dpro-confirm-area').show();
            } else {
                $('#form-duplicator').submit();
            }
        }

        //Toggles each scan item to hide/show details
        DupPro.Pack.toggleScanItem = function (item)
        {
            var $info = $(item).parents('div.scan-item').children('div.info');
            var $text = $(item).find('div.text i.fa');
            if ($info.is(":hidden")) {
                $text.addClass('fa-caret-down').removeClass('fa-caret-right');
                $info.show();
            } else {
                $text.addClass('fa-caret-right').removeClass('fa-caret-down');
                $info.hide(250);
            }
        }

        //Set Good/Warn Badges and checkboxes
        DupPro.Pack.setScanStatus = function (data)
        {
            let subTestSelectorMappings = {
                '#data-srv-php-websrv': data.SRV.PHP.websrv,
                '#data-srv-php-openbase': data.SRV.PHP.openbase,
                '#data-srv-php-maxtime': data.SRV.PHP.maxtime,
                '#data-srv-php-minmemory': data.SRV.PHP.minMemory,
                '#data-srv-php-arch64bit': data.SRV.PHP.arch64bit,
                '#data-srv-php-mysqli': data.SRV.PHP.mysqli,
                '#data-srv-php-openssl': data.SRV.PHP.openssl,
                '#data-srv-php-allowurlfopen': data.SRV.PHP.allowurlfopen,
                '#data-srv-php-curlavailable': data.SRV.PHP.curlavailable,
                '#data-srv-php-version': data.SRV.PHP.version,
                '#data-srv-wp-version': data.SRV.WP.version,
                '#data-srv-brand-check': data.SRV.Brand.LogoImageExists,
                '#data-srv-wp-core': data.SRV.WP.core
            };

            for (let selector in subTestSelectorMappings) {
                if (subTestSelectorMappings[selector]) {
                    $(selector).html('<div class="scan-good"><i class="fa fa-check"></i></div>');
                } else {
                    $(selector).html('<div class="scan-warn"><i class="fa fa-exclamation-triangle fa-sm"></i></div>');
                }
            }

            let testSelectorMappings = {
                '#data-srv-php-all': data.SRV.PHP.ALL,
                '#data-srv-wp-all': data.SRV.WP.ALL,
                '#data-arc-status-size': data.ARC.Status.Size,
                '#data-arc-status-unreadablefiles': data.ARC.Status.UnreadableItems,
                '#data-arc-status-showcreatefunc': data.ARC.Status.showCreateFuncStatus,
                '#data-arc-status-network': data.ARC.Status.Network,
                '#data-arc-status-triggers': data.DB.Status.Triggers,
                '#data-arc-status-migratepackage': !data.ARC.Status.PackageIsNotImportable,
                '#data-arc-status-addonsites': data.ARC.Status.AddonSites,
                '#data-db-status-size1': data.DB.DBExcluded && data.DB.Status.Success ? data.DB.Status.Excluded : data.DB.Status.Size,
            }

            const GoodText = "<?php esc_html_e('Good', 'duplicator-pro'); ?>";
            const WarnText = "<?php esc_html_e('Notice', 'duplicator-pro'); ?>";

            for (let selector in testSelectorMappings) {
                if (testSelectorMappings[selector]) {
                    $(selector).html(`<div class="badge badge-pass">${GoodText}</div>`);
                } else {
                    $(selector).html(`<div class="badge badge-warn">${WarnText}</div>`);
                }
            }
        }

        //Allows user to continue with build if warnings found
        DupPro.Pack.warningContinue = function (checkbox)
        {
            ($(checkbox).is(':checked'))
                    ? $('#dup-build-button').prop('disabled', false)
                    : $('#dup-build-button').prop('disabled', true);
        }

        //Page Init:
        DupPro.Pack.runScanner();

        $('.dup-go-back-to-new1').click(function (event) {
            event.preventDefault();
            window.location.href = <?php echo SnapJson::jsonEncode(PackagesPageController::getInstance()->getPackageBuildS1Url()); ?>;
        });
    });
</script>
