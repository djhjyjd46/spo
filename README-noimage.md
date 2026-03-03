# Использование встроенных заглушек WooCommerce

## Доступные размеры placeholder изображений:

### PNG формат:
- `woocommerce-placeholder.png` - основная заглушка (базовый размер)
- `woocommerce-placeholder-100x100.png` - миниатюра 100x100px
- `woocommerce-placeholder-150x150.png` - миниатюра 150x150px  
- `woocommerce-placeholder-300x300.png` - средний размер 300x300px
- `woocommerce-placeholder-600x600.png` - большой размер 600x600px
- `woocommerce-placeholder-768x768.png` - очень большой 768x768px
- `woocommerce-placeholder-1024x1024.png` - максимальный 1024x1024px

### WEBP формат (оптимизированный):
- `woocommerce-placeholder-100x100.webp`
- `woocommerce-placeholder-150x150.webp`
- `woocommerce-placeholder-300x300.webp`

### AVIF формат (современный, сжатый):
- `woocommerce-placeholder.avif`
- `woocommerce-placeholder-600x600.avif`
- `woocommerce-placeholder-768x768.avif`
- `woocommerce-placeholder-1024x1024.avif`

## Как использовать в теме:

### В PHP:
```php
// Получить базовую заглушку
<?= wp_get_attachment_url(get_option('woocommerce_placeholder_image', 0)) ?>

// Или напрямую:
<?= get_site_url() ?>/wp-content/uploads/woocommerce-placeholder.png

// Адаптивное изображение с разными размерами:
<?= get_site_url() ?>/wp-content/uploads/woocommerce-placeholder-300x300.png
```

### В HTML/CSS:
```html
<!-- Базовая заглушка -->
<img src="/wp-content/uploads/woocommerce-placeholder.png" alt="Изображение недоступно">

<!-- Адаптивные изображения -->
<picture>
    <source srcset="/wp-content/uploads/woocommerce-placeholder-300x300.avif" type="image/avif">
    <source srcset="/wp-content/uploads/woocommerce-placeholder-300x300.webp" type="image/webp">
    <img src="/wp-content/uploads/woocommerce-placeholder-300x300.png" alt="Товар">
</picture>
```

### В JavaScript:
```javascript
// Заменить сломанные изображения на заглушку
document.querySelectorAll('img').forEach(img => {
    img.onerror = function() {
        this.src = '/wp-content/uploads/woocommerce-placeholder-300x300.png';
    };
});
```

## Функции WooCommerce:

```php
// Функция WooCommerce для получения placeholder
wc_placeholder_img_src($size = 'woocommerce_thumbnail')

// Получить полный тег img с placeholder
wc_placeholder_img($size = 'woocommerce_thumbnail')
```

## Преимущества использования WooCommerce placeholder:
1. ✅ Автоматически подстраиваются под тему WooCommerce
2. ✅ Есть все популярные форматы (PNG, WEBP, AVIF)
3. ✅ Разные размеры для любых нужд
4. ✅ Соответствуют стандартам WooCommerce
5. ✅ Поддерживают адаптивность