<form class="contact-form__form" method="post">
    <input name="name" type="text" placeholder="Ваше имя" required>
    <input name="tel" type="tel" placeholder="+375 (__) ___-__-__" required>
    <input type="hidden" name="action" value="mail_to">
    <button class="button" type="submit">Заказать звонок</button>
    <div class="contact-form__conf">
        <label class="custom-checkbox">
            <input type="checkbox" name="policy" required>
            <span></span>
            <p class="flex flex-wrap gap-[2px] items-center">Даю свое согласие на обработку <a class="policy-link-white"
                    href="<?= get_permalink(get_page_by_path('policy')); ?>">персональных данных.</a>
        </label>
    </div>
</form>