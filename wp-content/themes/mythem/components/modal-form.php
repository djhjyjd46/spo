<div class="modal" id="modalCalForm">
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
            <p>Оставьте ваши контактные данные и мы свяжемся с вами в ближайшее время</p>
            <form class="contact-form__form" method="post">
                <input name="name" type="text" placeholder="Ваше имя" required>
                <input name="tel" type="tel" placeholder="Ваш телефон" required>
                <input type="hidden" name="action" value="mail_to">
                <button type="submit">Отправить</button>
                <div class="contact-form__conf">
                    <label class="custom-checkbox">
                        <input type="checkbox" required>
                        <span></span>
                        <p>Согласен (а) на&nbsp;<a class="policy">обработку персональных данных</a></p>
                    </label>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" id="modalCalForm">
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
            <form action="" onsubmit="return false;">
                <input type="text" placeholder="Ваше имя*" required>
                <input type="tel" placeholder="+375 (__) ___-__-__" required>
                <label class="custom-checkbox">
                    <input type="checkbox" required>
                    <span></span>
                    <p>Подтверждаю ознакомление с <a class="policy">Политикой обработки персональных</a> данных и даю
                        согласие
                        на обработку моих персональных данных с целью осуществления обратного звонка</p>
                </label>
                <button type="submit" class="header__contacts-button">Заказать звонок</button>
            </form>
        </div>
    </div>
</div>