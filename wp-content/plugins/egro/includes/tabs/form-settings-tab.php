<?php
// Запрет прямого доступа к файлу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Отображает содержимое вкладки настроек форм
 *
 * @param array $options Текущие настройки форм
 */
function egro_form_settings_tab($options)
{
?>
    <form method="post" action="options.php">
        <?php settings_fields('egro_form_settings_group'); ?>

        <h2>Настройки форм</h2>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="use_data_form">Использовать атрибуты data-form</label>
                </th>
                <td>
                    <label for="use_data_form">
                        <input type="checkbox" name="egro_form_options[use_data_form]" id="use_data_form" value="yes"
                            <?php checked(isset($options['use_data_form']) && $options['use_data_form'] === 'yes'); ?>>
                        Использовать в письме атрибуты data-form из полей формы
                    </label>
                    <p class="description">
                        Если включено, то в письме в качестве названия поля будет использоваться значение атрибута
                        data-form.
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="form_action">Селектор действия формы</label>
                </th>
                <td>
                    <input type="text" name="egro_form_options[form_action]" id="form_action"
                        value="<?php echo esc_attr(isset($options['form_action']) ? $options['form_action'] : 'mail_to'); ?>"
                        class="regular-text">
                    <p class="description">
                        Задает значение поля action для обработчика формы (по умолчанию: mail_to).
                        <br>Вам нужно добавить скрытое поле в форму:
                        <code>&lt;input type="hidden" name="action" value="<b>mail_to</b>"&gt;</code>
                    </p>
                </td>
            </tr>
        </table>

        <h2>Пример использования в коде формы</h2>
        <div class="code-example">
            <pre><code>&lt;form method="post"&gt;
  &lt;input type="hidden" name="action" value="<?php echo esc_attr(isset($options['form_action']) ? $options['form_action'] : 'mail_to'); ?>"&gt;
  &lt;input type="text" name="name" data-form="Ваше имя" placeholder="Имя"&gt;
  &lt;input type="tel" name="phone" data-form="Ваш телефон" placeholder="Телефон"&gt;
  &lt;input type="checkbox" name="policy" data-form="Согласен с политикой конфиденциальности"&gt;
  &lt;button type="submit"&gt;Отправить&lt;/button&gt;
&lt;/form&gt;</code></pre>
        </div>

        <p class="submit">
            <input type="submit" class="button-primary" value="Сохранить изменения">
        </p>
    </form>
<?php
}
