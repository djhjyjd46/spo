<?php
/**
 * Plugin Name: My Yandex Reviews
 * Description: Плагин для создания и отображения отзывов Яндекс с помощью шорткода [yandex_reviews]
 * Version: 1.0.0
 * Author: Три Нити
 * Text Domain: my-yandex-reviews
 */

// Предотвращаем прямой доступ
if (!defined('ABSPATH')) {
    exit;
}

class MyYandexReviews {
    
    public function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_fields'));
        add_shortcode('yandex_reviews', array($this, 'shortcode_handler'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Регистрация типа записи yandex_review
     */
    public function register_post_type() {
        $labels = array(
            'name' => 'Отзывы Яндекс',
            'singular_name' => 'Отзыв Яндекс',
            'add_new' => 'Добавить новый',
            'add_new_item' => 'Добавить новый отзыв',
            'edit_item' => 'Редактировать отзыв',
            'new_item' => 'Новый отзыв',
            'view_item' => 'Просмотреть отзыв',
            'search_items' => 'Найти отзывы',
            'not_found' => 'Отзывы не найдены',
            'not_found_in_trash' => 'В корзине отзывы не найдены',
            'menu_name' => 'Отзывы Яндекс'
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-star-filled',
            'menu_position' => 26,
            'supports' => array('title', 'editor', 'thumbnail'),
            'has_archive' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'capability_type' => 'post',
            'rewrite' => false
        );
        
        register_post_type('yandex_review', $args);
    }
    
    /**
     * Добавление метабоксов для произвольных полей
     */
    public function add_meta_boxes() {
        add_meta_box(
            'yandex_review_fields',
            'Данные отзыва',
            array($this, 'meta_box_callback'),
            'yandex_review',
            'normal',
            'high'
        );
    }
    
    /**
     * Отображение метабокса с полями
     */
    public function meta_box_callback($post) {
        // Добавляем nonce поле для безопасности
        wp_nonce_field('yandex_review_meta_box', 'yandex_review_meta_box_nonce');
        
        // Получаем сохранённые значения
        $review_author = get_post_meta($post->ID, 'review_author', true);
        $review_rating = get_post_meta($post->ID, 'review_rating', true);
        $review_date = get_post_meta($post->ID, 'review_date', true);
        $review_link = get_post_meta($post->ID, 'review_link', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="review_author">Имя автора</label></th>
                <td><input type="text" id="review_author" name="review_author" value="<?= esc_attr($review_author) ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="review_rating">Рейтинг (1-5)</label></th>
                <td>
                    <select id="review_rating" name="review_rating">
                        <option value="">Выберите рейтинг</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>" <?php selected($review_rating, $i); ?>><?= $i ?> звезд<?= ($i > 1) ? 'ы' : 'а' ?></option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="review_date">Дата отзыва</label></th>
                <td><input type="date" id="review_date" name="review_date" value="<?= esc_attr($review_date) ?>" /></td>
            </tr>
            <tr>
                <th><label for="review_link">Ссылка на отзыв на Яндексе</label></th>
                <td><input type="url" id="review_link" name="review_link" value="<?= esc_attr($review_link) ?>" class="regular-text" placeholder="https://yandex.ru/..." /></td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Сохранение произвольных полей
     */
    public function save_meta_fields($post_id) {
        // Проверяем nonce
        if (!isset($_POST['yandex_review_meta_box_nonce']) || !wp_verify_nonce($_POST['yandex_review_meta_box_nonce'], 'yandex_review_meta_box')) {
            return;
        }
        
        // Проверяем автосохранение
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Проверяем права доступа
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Сохраняем поля
        $fields = array('review_author', 'review_rating', 'review_date', 'review_link');
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    /**
     * Добавление страницы настроек в админку
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=yandex_review',
            'Настройки отзывов',
            'Настройки',
            'manage_options',
            'yandex-reviews-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Регистрация настроек
     */
    public function register_settings() {
        register_setting('yandex_reviews_settings', 'yandex_reviews_total_count');
        register_setting('yandex_reviews_settings', 'yandex_reviews_average_rating');
    }
    
    /**
     * Страница настроек
     */
    public function settings_page() {
        $total_count = get_option('yandex_reviews_total_count', '');
        $average_rating = get_option('yandex_reviews_average_rating', '');
        ?>
        <div class="wrap">
            <h1>Настройки отзывов Яндекс</h1>
            <form method="post" action="options.php">
                <?php settings_fields('yandex_reviews_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="yandex_reviews_total_count">Общее количество отзывов</label>
                        </th>
                        <td>
                            <input type="number" id="yandex_reviews_total_count" name="yandex_reviews_total_count" value="<?= esc_attr($total_count) ?>" min="0" placeholder="Например: 125" />
                            <p class="description">Укажите общее количество отзывов на Яндексе</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="yandex_reviews_average_rating">Средняя оценка</label>
                        </th>
                        <td>
                            <input type="number" id="yandex_reviews_average_rating" name="yandex_reviews_average_rating" value="<?= esc_attr($average_rating) ?>" step="0.1" min="1" max="5" placeholder="Например: 4.8" />
                            <p class="description">Укажите среднюю оценку от 1 до 5</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Обработчик шорткода [yandex_reviews]
     */
    public function shortcode_handler($atts) {
        $atts = shortcode_atts(array(
            'limit' => -1,
            'order' => 'DESC'
        ), $atts);
        
        // Запрос отзывов
        $reviews = get_posts(array(
            'post_type' => 'yandex_review',
            'post_status' => 'publish',
            'numberposts' => intval($atts['limit']),
            'order' => $atts['order'],
            'orderby' => 'date'
        ));
        
        if (empty($reviews)) {
            return '<p>Отзывы не найдены.</p>';
        }
        
        // Подключаем шаблон
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/reviews-list.php';
        return ob_get_clean();
    }
    
    /**
     * Подключение стилей
     */
    public function enqueue_styles() {
        // Проверяем, есть ли на странице шорткод
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'yandex_reviews')) {
            $css_file = plugin_dir_path(__FILE__) . 'assets/css/styles.css';
            if (file_exists($css_file)) {
                wp_enqueue_style(
                    'my-yandex-reviews-styles',
                    plugin_dir_url(__FILE__) . 'assets/css/styles.css',
                    array(),
                    '1.0.0'
                );
            }
        }
    }
    
    /**
     * Генерация звёздочек по рейтингу
     */
    public static function render_stars($rating) {
        $output = '<div class="review-stars">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $output .= '<span class="star filled">★</span>';
            } else {
                $output .= '<span class="star empty">☆</span>';
            }
        }
        $output .= '</div>';
        return $output;
    }
    
    /**
     * Получить статистику отзывов (общее количество и средняя оценка)
     */
    public static function get_reviews_stats() {
        return array(
            'total_reviews' => get_option('yandex_reviews_total_count', 0),
            'average_rating' => get_option('yandex_reviews_average_rating', 0)
        );
    }
}

// Инициализация плагина
new MyYandexReviews();

/**
 * Активация плагина
 */
register_activation_hook(__FILE__, 'my_yandex_reviews_activate');
function my_yandex_reviews_activate() {
    // Регистрируем тип записи
    $plugin = new MyYandexReviews();
    $plugin->register_post_type();
    
    // Обновляем правила перезаписи
    flush_rewrite_rules();
}

/**
 * Деактивация плагина
 */
register_deactivation_hook(__FILE__, 'my_yandex_reviews_deactivate');
function my_yandex_reviews_deactivate() {
    // Удаляем правила перезаписи
    flush_rewrite_rules();
}
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'my-yandex-reviews-styles',
        plugins_url('assets/css/styles.css', __FILE__),
        [],
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/styles.css')
    );
});