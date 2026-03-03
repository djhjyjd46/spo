document.addEventListener('DOMContentLoaded', function () {
    const ajaxurl = window.ajax_object?.ajax_url || '/wp-admin/admin-ajax.php';
    const action = window.ajax_object?.action || 'mail_to'; // Получаем настроенное действие
    const useDataForm = window.ajax_object?.use_data_form === 'yes';

    const forms = document.querySelectorAll('form');

    if (forms.length > 0) {
        forms.forEach(form => {
            // Ищем поле с заданным действием формы
            const mailToInput = form.querySelector(`input[value="${action}"]`);
            if (!mailToInput) {
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            let originalBtnText = submitBtn ? submitBtn.textContent : '';

            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Предотвращаем стандартное поведение формы

                // Очищаем предыдущие сообщения
                const prevErrorMsg = form.querySelector('.form-error-message');
                if (prevErrorMsg) {
                    prevErrorMsg.remove();
                }

                const prevSuccessMsg = form.querySelector('.form-success-message');
                if (prevSuccessMsg) {
                    prevSuccessMsg.remove();
                }

                if (submitBtn) {
                    submitBtn.setAttribute('disabled', 'disabled');
                    submitBtn.classList.add('loading');
                    submitBtn.textContent = 'Отправка...';
                }

                const formData = new FormData(form);
                formData.append('action', action); // Добавляем настроенное действие

                // Проверяем, находится ли форма в модальном окне с атрибутом data-trigger-attr
                const modal = form.closest('.modal');
                if (modal && modal.hasAttribute('data-trigger-attr')) {
                    const triggerAttr = modal.getAttribute('data-trigger-attr');
                    formData.append('form_data_attribute', triggerAttr);
                }

                // Проверяем скрытое поле modal_trigger_data
                const modalTriggerField = form.querySelector('#modal_trigger_data');
                if (modalTriggerField && modalTriggerField.value) {
                    formData.append('form_data_attribute', modalTriggerField.value);
                }

                // Проверяем атрибут data у кнопки submit (для обычных форм)
                if (submitBtn && submitBtn.hasAttribute('data')) {
                    const dataAttribute = submitBtn.getAttribute('data');
                    formData.append('form_data_attribute', dataAttribute);
                }

                // Если включена опция использования data-form атрибутов
                if (useDataForm) {
                    // Собираем все типы полей для отправки их на сервер
                    const fieldTypes = {};
                    const dataFormFields = {};

                    // Перебираем все поля формы
                    form.querySelectorAll('input, select, textarea').forEach(field => {
                        const name = field.name;
                        if (!name) return;

                        let fieldType = field.type;

                        if (field.tagName.toLowerCase() === 'input') {
                            if (field.type === 'checkbox' || field.type === 'radio') {
                                // Только отмеченные чекбоксы/радио
                                if (!field.checked) return;
                            }
                        }

                        // Записываем тип поля
                        fieldTypes[name] = fieldType;

                        // Если у поля есть атрибут data-form, сохраняем его
                        const dataFormValue = field.getAttribute('data-form');
                        if (dataFormValue) {
                            dataFormFields[name] = dataFormValue;
                        }
                    });

                    // Добавляем типы полей в formData
                    formData.append('egro_field_types', JSON.stringify(fieldTypes));

                    // Добавляем data-form атрибуты в formData
                    formData.append('egro_data_form_fields', JSON.stringify(dataFormFields));
                }

                fetch(ajaxurl, {
                    method: 'POST',
                    body: formData,
                })
                    .then(response => response.json())
                    .then(response => {

                        const message = response?.data?.message || 'Операция завершена';
                        const success = response?.data?.success || false;

                        if (success) {
                            console.log('Форма успешно отправлена');
                            // Очистить все поля формы
                            form.reset();

                            // Закрываем текущее модальное окно если форма в модальном окне
                            const modal = form.closest('.modal');
                            if (modal) {
                                modal.style.display = 'none';
                            }

                            // Открываем модальное окно благодарности
                            const modalThnx = document.getElementById('modalThnx');
                            if (modalThnx) {
                                modalThnx.style.display = 'block';
                            }

                            // const thankYouPageUrl = window.ajax_object?.thank_you_page || '/spasibo-za-vashu-zayavku';
                            // window.location.href = thankYouPageUrl; // Перенаправляем на страницу благодарности
                        } else {
                            console.log(message);
                        }
                    })
                    .catch(error => {
                        console.error('Произошла ошибка при отправке формы. Пожалуйста, попробуйте позже.', error);
                    })
                    .finally(() => {
                        if (submitBtn) {
                            submitBtn.removeAttribute('disabled');
                            submitBtn.classList.remove('loading');
                            submitBtn.textContent = originalBtnText;
                        }
                    });
            });
        });
    }
});

