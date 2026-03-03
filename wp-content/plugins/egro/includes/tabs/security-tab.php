<?php

/**
 * Отображение вкладки настроек безопасности
 */
function egro_security_settings_tab($options)
{
?>
    <form method="post" action="options.php">
        <?php settings_fields('egro_security_settings_group'); ?>
        <?php do_settings_sections('egro_security_settings_group'); ?>

        <table class="form-table">
            <tr>
                <th scope="row">Включить защиту</th>
                <td>
                    <label>
                        <input type="checkbox" name="egro_security_options[enable_security]" value="yes" <?php checked(isset($options['enable_security']) ? $options['enable_security'] : '', 'yes'); ?>>
                        Включить проверку безопасности
                    </label>
                    <?php if (isset($options['enable_security']) && $options['enable_security'] === 'yes'): ?>
                        <span style="color:green;">(Включено)</span>
                    <?php else: ?>
                        <span style="color:red;">(Отключено)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row">Блокировать письма со спамом</th>
                <td>
                    <label>
                        <input type="checkbox" name="egro_security_options[block_spam_emails]" value="yes" <?php checked(isset($options['block_spam_emails']) ? $options['block_spam_emails'] : '', 'yes'); ?>>
                        Не отправлять письма, если обнаружен спам (honeypot)
                    </label>
                    <?php if (isset($options['block_spam_emails']) && $options['block_spam_emails'] === 'yes'): ?>
                        <span style="color:green;">(Включено)</span>
                    <?php else: ?>
                        <span style="color:red;">(Отключено)</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <div class="submit-buttons-row">
            <?php submit_button('Сохранить настройки', 'primary', 'submit', false); ?>
        </div>
    </form>
<?php
}
