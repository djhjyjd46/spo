<?php
// Запрет прямого доступа к файлу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Отображает содержимое вкладки "О плагине"
 */
function egro_about_tab() {
    ?>
    <div class="about-wrap">
        <h2>О плагине EGRO</h2>
        
        <div class="about-description">
            <p>
                EGRO - это плагин для WordPress, разработанный для обработки форм обратной связи и интеграции с Telegram.
            </p>
        </div>
        
        <div class="about-section">
            <h3>Основные функции</h3>
            <ul class="ul-disc">
                <li>Обработка форм обратной связи через AJAX</li>
                <li>Отправка уведомлений в Telegram</li>
                <li>Интеграция с корзиной WooCommerce</li>
                <li>Использование атрибутов data-form для настройки меток полей</li>
                <li>Защита от спама (honeypot + security token)</li>
            </ul>
        </div>
        
        <div class="about-section">
            <h3>Версия</h3>
            <p>Текущая версия: <strong><?= EGRO_VERSION ?></strong></p>
            <p>Дата обновления: 05.10.2025</p>
        </div>
        
        <div class="about-section">
            <h3>Автор</h3>
            <p>Разработано компанией <a href="https://fastup.by/" target="_blank">FastUp</a></p>
            <p>При возникновении вопросов или проблем с плагином, пожалуйста, обращайтесь по адресу: <a href="mailto:web@fastup.by">web@fastup.by</a></p>
        </div>
    </div>
    <?php
}
