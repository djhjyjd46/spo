<?php
function egro_telegram_settings_tab($options)
{
?>
    <form method="post" action="options.php">
        <?php settings_fields('egro_telegram_settings_group'); ?>
        <?php do_settings_sections('egro_telegram_settings_group'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">Включить отправку в Telegram</th>
                <td>
                    <input type="checkbox" name="egro_telegram_options[enabled]" value="yes" <?php checked(isset($options['enabled']) ? $options['enabled'] : '', 'yes'); ?>>
                </td>
            </tr>
            <tr>
                <th scope="row">Токен бота</th>
                <td>
                    <input type="text" name="egro_telegram_options[bot_token]" value="<?php echo esc_attr($options['bot_token'] ?? ''); ?>" class="regular-text" placeholder="По умолчанию используется бот разработчика">
                    <p class="description">
                        По умолчанию сообщения будут приходить от бота разработчика.<br>
                        Если хотите использовать своего бота — создайте его через <a href="https://t.me/BotFather" target="_blank">@BotFather</a> и введите токен сюда.<br>
                        Если поле оставить пустым — сообщения будут приходить от бота разработчика.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">ID чата</th>
                <td>
                    <input type="text" name="egro_telegram_options[chat_id]" value="<?php echo esc_attr($options['chat_id'] ?? '-4933571071'); ?>" class="regular-text">
                    <?php
                    $default_bot_token = '6971328945:AAEOE6s8ajiKEzUg-G3AAKVP4-uTpgvSL8U';
                    $bot_token = !empty($options['bot_token']) ? $options['bot_token'] : $default_bot_token;
                    $get_updates_url = 'https://api.telegram.org/bot' . urlencode($bot_token) . '/getUpdates';
                    ?>
                    <p class="description">
                        1. Напишите вашему боту любое сообщение в Telegram.<br>
                        2. Перейдите по <a href="<?php echo esc_url($get_updates_url); ?>" target="_blank">этой ссылке для получения chat_id</a>.<br>
                        3. Найдите свой chat_id в ответе (например, <code>"chat":{"id":123456789,</code>).<br>
                        4. Вставьте этот chat_id сюда.<br>
                        <b>Теперь письма из форм будут приходить вам в личные сообщения от бота.</b>
                    </p>
                </td>
            </tr>
        </table>
        <?php submit_button('Сохранить настройки'); ?>
    </form>
<?php
}
