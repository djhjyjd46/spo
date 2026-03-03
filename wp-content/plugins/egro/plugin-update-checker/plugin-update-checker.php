<?php
/**
 * Plugin Update Checker Library 5.0
 * Простая реализация системы обновлений для Egro Egro
 */
namespace YahnisElsts\PluginUpdateChecker\v5;

if ( !class_exists(PucFactory::class, false) ):

class PucFactory {
    /**
     * Create a new instance of the update checker.
     *
     * @param string $metadataUrl  URL файла с данными об обновлении
     * @param string $pluginFile   Полный путь к основному файлу плагина
     * @param string $slug         Уникальный слаг плагина
     * @param int    $checkPeriod  Как часто проверять обновления (в часах)
     * @param string $optionName   Имя параметра для сохранения в БД WordPress
     * @return UpdateChecker
     */
    public static function buildUpdateChecker($metadataUrl, $pluginFile, $slug = '', $checkPeriod = 12, $optionName = '') {
        return new UpdateChecker($metadataUrl, $pluginFile, $slug, $checkPeriod, $optionName);
    }
}

class UpdateChecker {
    protected $metadataUrl;
    protected $pluginFile;
    protected $slug;
    protected $checkPeriod;
    protected $optionName;
    protected $lastCheck = 0;
    protected $cachedUpdateData = null;

    public function __construct($metadataUrl, $pluginFile, $slug = '', $checkPeriod = 12, $optionName = '') {
        $this->metadataUrl = $metadataUrl;
        $this->pluginFile = $pluginFile;
        $this->slug = !empty($slug) ? $slug : basename($pluginFile, '.php');
        $this->checkPeriod = $checkPeriod * 3600;
        $this->optionName = !empty($optionName) ? $optionName : 'external_updates-' . $this->slug;

        $this->installHooks();
        $this->loadUpdateData();
    }

    /**
     * Инсталляция хуков WordPress для проверки обновлений
     */
    protected function installHooks() {
        add_filter('pre_set_site_transient_update_plugins', array($this, 'injectUpdate'));
        add_filter('plugins_api', array($this, 'getPluginInfo'), 10, 3);
        add_action('admin_init', array($this, 'maybeCheckForUpdates'));
    }

    /**
     * Загрузка сохраненных данных об обновлении
     */
    protected function loadUpdateData() {
        $this->cachedUpdateData = get_option($this->optionName);
        if ($this->cachedUpdateData === false) {
            $this->cachedUpdateData = new \stdClass();
            $this->cachedUpdateData->lastCheck = 0;
            $this->cachedUpdateData->update = null;
            update_option($this->optionName, $this->cachedUpdateData);
        }
        $this->lastCheck = $this->cachedUpdateData->lastCheck;
    }

    /**
     * Проверка наличия обновлений (с учетом периода проверок)
     */
    public function maybeCheckForUpdates() {
        if ( empty($this->lastCheck) || ((time() - $this->lastCheck) > $this->checkPeriod) ) {
            $this->checkForUpdates();
        }
    }

    /**
     * Принудительная проверка обновлений
     *
     * @return \stdClass|null Данные об обновлении или null
     */
    public function checkForUpdates() {
        $state = $this->requestUpdate();
        $this->lastCheck = time();
        $this->cachedUpdateData->lastCheck = $this->lastCheck;
        $this->cachedUpdateData->update = $state;
        update_option($this->optionName, $this->cachedUpdateData);

        return $this->cachedUpdateData->update;
    }

    /**
     * Запрос данных об обновлении с сервера
     *
     * @return \stdClass|null
     */
    protected function requestUpdate() {
        $response = wp_remote_get(
            $this->metadataUrl,
            array('timeout' => 10)
        );

        if ( is_wp_error($response) || !isset($response['response']['code']) || $response['response']['code'] != 200 ) {
            return null;
        }

        $metadata = json_decode($response['body']);
        if ( !is_object($metadata) || !isset($metadata->name, $metadata->version) ) {
            return null;
        }

        $updateData = new \stdClass();
        $updateData->slug = $this->slug;
        $updateData->plugin = plugin_basename($this->pluginFile);
        $updateData->new_version = $metadata->version;
        $updateData->url = isset($metadata->homepage) ? $metadata->homepage : '';
        $updateData->package = isset($metadata->download_url) ? $metadata->download_url : '';

        if ( isset($metadata->sections) ) {
            $updateData->sections = $metadata->sections;
        }

        if ( isset($metadata->banners) ) {
            $updateData->banners = $metadata->banners;
        }

        if ( isset($metadata->icons) ) {
            $updateData->icons = $metadata->icons;
        }

        return $updateData;
    }

    /**
     * Вставка информации об обновлении в список обновлений WordPress
     *
     * @param \stdClass $updates Список обновлений WordPress
     * @return \stdClass Обновленный список с нашим плагином
     */
    public function injectUpdate($updates) {
        if ( !is_object($updates) ) {
            $updates = new \stdClass();
        }

        if ( !isset($updates->response) ) {
            $updates->response = array();
        }

        $state = $this->getUpdate();
        if ( !empty($state) && version_compare(plugin_get_version($this->pluginFile), $state->new_version, '<') ) {
            $updates->response[$state->plugin] = $state;
        }

        return $updates;
    }

    /**
     * Получение сохраненных данных об обновлении
     *
     * @return \stdClass|null
     */
    public function getUpdate() {
        return $this->cachedUpdateData->update;
    }

    /**
     * Предоставление информации о плагине для WordPress API
     *
     * @param mixed $result
     * @param string $action
     * @param object $args
     * @return object|mixed
     */
    public function getPluginInfo($result, $action, $args) {
        if ( $action !== 'plugin_information' || !isset($args->slug) || ($args->slug !== $this->slug) ) {
            return $result;
        }

        $state = $this->getUpdate();
        if ( empty($state) ) {
            return $result;
        }

        $pluginInfo = new \stdClass();
        $pluginInfo->name = isset($state->name) ? $state->name : 'Egro Egro';
        $pluginInfo->slug = $this->slug;
        $pluginInfo->version = $state->new_version;
        $pluginInfo->author = isset($state->author) ? $state->author : 'Egro';
        $pluginInfo->homepage = isset($state->url) ? $state->url : '';
        $pluginInfo->download_link = isset($state->package) ? $state->package : '';

        if ( isset($state->sections) ) {
            $pluginInfo->sections = (array)$state->sections;
        } else {
            $pluginInfo->sections = array(
                'description' => 'Плагин Egro Egro - Отправка писем через SMTP',
            );
        }

        if ( isset($state->banners) ) {
            $pluginInfo->banners = (array)$state->banners;
        }

        if ( isset($state->icons) ) {
            $pluginInfo->icons = (array)$state->icons;
        }

        return $pluginInfo;
    }
}

// Вспомогательная функция для получения версии плагина
if (!function_exists('plugin_get_version')) {
    function plugin_get_version($pluginFile) {
        if (!function_exists('get_plugin_data')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $pluginData = get_plugin_data($pluginFile);
        return isset($pluginData['Version']) ? $pluginData['Version'] : '0';
    }
}

endif;
