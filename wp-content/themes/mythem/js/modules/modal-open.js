document.addEventListener("DOMContentLoaded", () => {
  const modalCall = document.getElementById("modalCall");
  const modalPolicy = document.getElementById("modalPolicy");
  const modalThnx = document.getElementById("modalThnx");

  // Кнопки открытия модальных окон
  const openModalPhoneButtons = document.querySelectorAll(".openModalPhone");

  // Кнопки закрытия модальных окон
  const closeButtons = document.querySelectorAll(".modal .close");

  // Открытие модального окна "Заказать звонок" по классу
  if (openModalPhoneButtons.length > 0) {
    openModalPhoneButtons.forEach((btn) => {
      btn.addEventListener("click", () => {
        if (modalCall) {
          modalCall.style.display = "block";
        }
      });
    });
  }
  //открытие модального окна по дата-атрибуту data-modal='id модального окна'


  // Открытие модальных окон по data-modal атрибуту
  document.querySelectorAll("[data-modal]").forEach((btn) => {
    btn.addEventListener("click", (event) => {
      event.preventDefault(); // Предотвращаем стандартное поведение

      const modalAttr = btn.getAttribute("data-modal");
      const dataAttr = btn.getAttribute("data"); // Получаем атрибут data

      let modal = null;
      if (modalAttr === "modalCall") {
        modal = modalCall;
      } else if (modalAttr === "modalPolicy" || modalAttr === "policy") {
        modal = modalPolicy;
      } else {
        modal = document.getElementById(modalAttr);
      }

      if (modal) {
        // Передаем атрибут data в модальное окно
        if (dataAttr) {
          modal.setAttribute("data-trigger-attr", dataAttr);

          // Обновляем скрытое поле в форме, если оно есть
          const hiddenField = modal.querySelector('#modal_trigger_data');
          if (hiddenField) {
            hiddenField.value = dataAttr;
          }
        } else {
          modal.removeAttribute("data-trigger-attr");

          // Очищаем скрытое поле
          const hiddenField = modal.querySelector('#modal_trigger_data');
          if (hiddenField) {
            hiddenField.value = '';
          }
        }

        modal.style.display = "block";
      } else {
      }
    });
  });

  // Функция очистки атрибутов модального окна
  function clearModalAttributes(modal) {
    if (modal) {
      modal.removeAttribute("data-trigger-attr");
      const hiddenField = modal.querySelector('#modal_trigger_data');
      if (hiddenField) {
        hiddenField.value = '';
      }
    }
  }

  // Закрытие модальных окон при нажатии на кнопку "Закрыть"
  if (closeButtons.length > 0) {
    closeButtons.forEach((closeButton) => {
      closeButton.addEventListener("click", () => {
        const modal = closeButton.closest(".modal");
        if (modal) {
          clearModalAttributes(modal); // Очищаем атрибуты
          modal.style.display = "none";
        }
      });
    });
  }

  // Закрытие модальных окон при клике вне их содержимого
  window.addEventListener("click", (event) => {
    if (event.target.classList.contains("modal")) {
      clearModalAttributes(event.target); // Очищаем атрибуты
      event.target.style.display = "none";
    }
  });

  // Добавление стилей прокрутки для модальных окон
  const modals = document.querySelectorAll(".modal");
  if (modals.length > 0) {
    modals.forEach((modal) => {
      const modalContent = modal.querySelector(".modal-content");
      if (modalContent) {
        modalContent.style.overflowY = "auto";
        modalContent.style.maxHeight = "90vh";
      }
    });
  }
  if (modalThnx) {
    const closeThnxButton = document.querySelector('#close-thnx');

    closeThnxButton.addEventListener("click", () => {
      // Очищаем атрибуты из всех модальных окон при закрытии благодарности
      const allModals = document.querySelectorAll('.modal');
      allModals.forEach(clearModalAttributes);

      modalThnx.style.display = "none";
    });
  }

});