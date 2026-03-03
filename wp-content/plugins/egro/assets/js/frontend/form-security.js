/**
 * Скрипт для добавления полей безопасности во все почтовые формы
 */
document.addEventListener('DOMContentLoaded', function () {
    // Проверяем доступность данных безопасности
    const securityToken = window.egro_security?.token || '';
    const nonceToken = window.egro_security?.nonce || '';

    if (!securityToken) {
        console.error('Токен безопасности ArtCly SMTP не доступен');
        return;
    }

    // Находим все формы с полем mail_to
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const actionInput = form.querySelector('input[type="hidden"][name="action"][value="mail_to"]');


        // Проверяем, существуют ли уже поля безопасности
        const existingSecurityField = form.querySelector('input[name="egro_security_token"]');
        const existingNonceField = form.querySelector('input[name="egro_form_nonce"]');
        const existingHoneypotField = form.querySelector('input[name="egro_website"]');

        // 1. Добавляем токен безопасности
        if (!existingSecurityField) {
            const securityField = document.createElement('input');
            securityField.type = 'hidden';
            securityField.name = 'egro_security_token';
            securityField.value = securityToken;
            form.appendChild(securityField);
        }

        // 2. Добавляем nonce-поле
        if (!existingNonceField && nonceToken) {
            const nonceField = document.createElement('input');
            nonceField.type = 'hidden';
            nonceField.name = 'egro_form_nonce';
            nonceField.value = nonceToken;
            form.appendChild(nonceField);
        }

        // 3. Добавляем honeypot поле
        if (!existingHoneypotField) {
            const honeypotField = document.createElement('input');
            honeypotField.type = 'text';
            honeypotField.name = 'egro_website';
            honeypotField.value = '';
            honeypotField.style.display = 'none';
            form.appendChild(honeypotField);
        }
    });
});
