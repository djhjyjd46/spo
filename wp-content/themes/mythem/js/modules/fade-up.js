// Упрощённый модуль анимаций: .fade-up, .fade-left, .fade-right, .zoom-in
document.addEventListener('DOMContentLoaded', () => {
  // настройки observer'а
  const OBS_OPTIONS = { root: null, rootMargin: '0px 0px -8% 0px', threshold: 0.08 };

  // учёт предпочтений пользователя (уменьшение анимаций)
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // упрощённый селектор
  const SELECTOR = '.fade-up, .fade-left, .fade-right, .zoom-in';

  // показываем элемент (учёт data-delay)
  const show = el => {
    if (reduceMotion) {
      el.classList.add('visible');
      return;
    }
    if (el.dataset.delay) el.style.transitionDelay = el.dataset.delay;
    el.classList.add('visible');
  };

  const io = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      show(entry.target);
      observer.unobserve(entry.target);
    });
  }, OBS_OPTIONS);

  // начать наблюдение за существующими элементами
  document.querySelectorAll(SELECTOR).forEach(el => {
    if (!el.classList.contains('visible')) io.observe(el);
  });
});
