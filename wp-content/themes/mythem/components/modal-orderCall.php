<div class="modal" id="modalCall">
    <div class="modal-content">
        <span class="close">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_72_1491)">
                    <path d="M10 10L19 19M10 10L1 1M10 10L1 19M10 10L19 1" stroke="#777777" stroke-width="2"
                        stroke-linecap="round" />
                </g>
                <defs>
                    <clipPath id="clip0_72_1491">
                        <rect width="20" height="20" fill="white" />
                    </clipPath>
                </defs>
            </svg>
        </span>
        <div class="modal-phone">
            <h2>Заказать звонок</h2>
            <p>Оставьте Ваши контакты и наши специалисты свяжутся с вами</p>
            <form method="post">
                <input name="name" type="text" placeholder="Ваше имя*" required>
                <input name="tel" type="tel" placeholder="+375 (__) ___-__-__" required>
                <input type="hidden" name="action" value="mail_to">
                <input type="hidden" name="modal_trigger_data" id="modal_trigger_data" value="">
                <input type="hidden" name="artcly_website" value="">
                <?php if (function_exists('artcly_smtp_get_form_nonce')): ?>
                    <input type="hidden" name="artcly_form_nonce" value="<?= artcly_smtp_get_form_nonce(); ?>">
                <?php endif; ?>
                <?php if (function_exists('artcly_smtp_get_security_token')): ?>
                    <input type="hidden" name="artcly_security_token" value="<?= artcly_smtp_get_security_token(); ?>">
                <?php endif; ?>
                <label class="custom-checkbox">
                    <input type="checkbox" required>
                    <span></span>
                    <p class="flex flex-wrap gap-[2px] items-center">Подтверждаю ознакомление с <a
                            href="<?= get_permalink(get_page_by_path('policy')); ?>">Политикой обработки персональных
                            данных</a> и даю согласие на обработку моих персональных данных с целью осуществления
                        обратного звонка</p>
                </label>
                <button type="submit" class="button">Заказать звонок</button>
            </form>
        </div>
    </div>
</div>