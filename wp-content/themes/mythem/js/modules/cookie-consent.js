document.addEventListener('DOMContentLoaded', function () {
  const KEY = 'site_cookie_consent_v1';
  const container = document.getElementById('cookieConsent');
  const btnAccept = document.getElementById('cookieAccept');
  const btnDecline = document.getElementById('cookieDecline');

  if (!container) return;

  const inner = document.getElementById('cookieConsentInner');

  function hide(animated = true) {
    if (!inner) return;
    inner.classList.add('opacity-0', 'translate-y-3', 'pointer-events-none');
    inner.classList.remove('opacity-100');
    if (animated) setTimeout(() => container.remove(), 220);
    else container.remove();
  }

  // Если пользователь уже сделал выбор — скрываем баннер
  try {
    const stored = localStorage.getItem(KEY);
    if (stored) {
      hide(false);
      return;
    }
  } catch (e) {
    // localStorage может быть недоступен — показываем баннер
  }

  // Показываем баннер (если нет выбора)
  try {
    const innerEl = document.getElementById('cookieConsentInner');
    if (innerEl) {
      // покажем аккуратно
      innerEl.classList.remove('opacity-0', 'translate-y-3', 'pointer-events-none');
      innerEl.classList.add('opacity-100');
    }
  } catch (e) {}

  if (btnAccept) {
    btnAccept.addEventListener('click', function () {
      try { localStorage.setItem(KEY, 'accepted'); } catch (e) {}
      hide(true);
    });
  }

  if (btnDecline) {
    btnDecline.addEventListener('click', function () {
      try { localStorage.setItem(KEY, 'declined'); } catch (e) {}
      hide(true);
    });
  }
});
