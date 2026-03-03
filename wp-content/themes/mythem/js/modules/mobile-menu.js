document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("mobileOverlay");
    const links = modal.querySelectorAll("a");
    const body = document.body;

    // Блокировка скролла при открытии модального окна
    document.querySelector(".burger-button").addEventListener("click", function () {
        modal.showModal();
        body.style.overflow = "hidden";
    });

    // Закрытие модального окна при клике по ссылке
    links.forEach(link => {
        link.addEventListener("click", function () {
            modal.close();
            body.style.overflow = ""; // Возвращаем скролл
        });
    });

    // Закрытие модального окна и разблокировка скролла при клике на крестик
    modal.addEventListener("close", function () {
        body.style.overflow = "";
    });
});
