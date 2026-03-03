<div class="modal modal-policy" id="modalPolicy">
    <div class="modal-content modal-policy-content">
        <span class="close">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_72_1491)">
                    <path d="M10 10L19 19M10 10L1 1M10 10L1 19M10 10L19 1" stroke="#777777" stroke-width="2" stroke-linecap="round"></path>
                </g>
                <defs>
                    <clipPath id="clip0_72_1491">
                        <rect width="20" height="20" fill="white"></rect>
                    </clipPath>
                </defs>
            </svg>
        </span>
        <h2>Политика конфенденциальности</h2>
        <p class="modal-policy__text">
            <?= the_field('politika', 'option'); ?>            
        </p>
        <p>
            
           Название компании: <?= the_field('name-company', 'option'); ?><br>
          Адрес: <?= the_field('adress', 'option'); ?><br>
       Телефон: <?= the_field('phone-1', 'option'); ?><br>
       Почта: <?= the_field('email', 'option'); ?><br>
      Дата:  <?= the_field('data-policy', 'option'); ?><br>
        </p>
    </div>
</div>