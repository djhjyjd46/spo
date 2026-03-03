document.addEventListener("DOMContentLoaded", () => {
    // Проверка на мобильное устройство
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    if (isMobile) {
        // На мобильных не вешаем маску, чтобы не мешать автозаполнению и работе клавиатуры
        return;
    }
    document.querySelectorAll('input[type="tel"]').forEach(input => {
        input.addEventListener('focus', () => {
            if (input.value === '') {
                input.value = '+375 (';
                setTimeout(() => input.setSelectionRange(6, 6), 0);
            }
        });

        input.addEventListener('input', (e) => {
            let start = input.selectionStart; // где был курсор
            let prevValue = input.value;
            let v = input.value.replace(/\D/g, ''); // только цифры
            if (v.startsWith('375')) v = v.slice(3);

            // Если пользователь пытается удалить маску, не даём удалять
            if (e.inputType === 'deleteContentBackward' && (prevValue === '+375 (' || prevValue === '+375 (')) {
                input.value = '+375 (';
                input.setSelectionRange(6, 6);
                return;
            }

            let result = '+375 (';
            if (v.length > 0) result += v.substring(0, 2) + ') ';
            if (v.length > 2) result += v.substring(2, 5);
            if (v.length > 5) result += '-' + v.substring(5, 7);
            if (v.length > 7) result += '-' + v.substring(7, 9);

            // сохраняем позицию курсора как можно ближе к тому, где был
            let oldLength = prevValue.length;
            input.value = result;
            let newLength = result.length;
            let diff = newLength - oldLength;

            // Корректируем позицию курсора, чтобы не перескакивал через скобки и спецсимволы
            let pos = start + diff;
            // Если удаляем и курсор оказался на спецсимволе, двигаем влево
            if (e.inputType === 'deleteContentBackward') {
                while (pos > 0 && /[\s\-\)\(]/.test(result[pos - 1])) {
                    pos--;
                }
                // Не даём удалить маску
                if (pos <= 6) {
                    pos = 6;
                }
            }
            // Если вставляем и курсор оказался на спецсимволе, двигаем вправо
            if (e.inputType === 'insertText') {
                while (pos < result.length && /[\s\-\)\(]/.test(result[pos])) {
                    pos++;
                }
            }
            input.setSelectionRange(pos, pos);
        });

        input.addEventListener('blur', () => {
            // Если поле пустое или только маска, очищаем и возвращаем плейсхолдер
            if (input.value === '+375 (' || input.value.trim() === '') {
                input.value = '';
            }
        });
    });
});