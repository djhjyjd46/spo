# Три Нити WordPress Theme - Copilot Instructions

## Project Overview
WordPress theme for "Три Нити" - оптово-розничный магазин швейной фурнитуры. Интернет-магазин с каталогом товаров от лучших производителей Беларуси, России, Китая. Сайт ориентирован на продажу швейных принадлежностей с поддержкой русской локализации.

## Architecture & Key Patterns

### Syntax Rules
- **ВСЕГДА используйте короткий синтаксис PHP echo:** `<?= $variable ?>` **вместо** `<?php echo $variable; ?>`**
- **ЗАПРЕЩЕНО:** `<?php echo get_field('name'); ?>` 
- **ПРАВИЛЬНО:** `<?= the_field('name'); ?>`
- **Используйте `<?= esc_url(get_field('name')) ?>` для безопасного вывода URL**
- **Используйте `<?= esc_attr(get_field('name')) ?>` для безопасного вывода атрибутов**
- **Используйте `get_field()` только в PHP:** `<?= get_field('name') ?>` **для получения значения ACF поля**
- **Используйте `the_field()` для вывода значений:** `<?= the_field('name') ?>`**для безопасного вывода значений ACF полей**
- **Не стилизуй элементы если это не указано в задаче** 
### WordPress Template Standards
- **Используйте `<?php foreach ($items as $item) : ?>` вместо `<?php foreach ($items as $item) { ?>` для лучшей читаемости**
 **ПРАВИЛЬНО:** `<?=  ?>`
    **НЕПРАВИЛЬНО:** `<?php echo  ?>`


### Tech Stack
- **WordPress 6.0+** with custom theme
- **ACF Pro** - All dynamic content via Options pages  
- **Tailwind CSS 3.3** - Custom colors: `orange: #FF7E00`, `black: #444444`
- **Vanilla JavaScript** - No jQuery, modular ES6+
- **SCSS** - Component-based with block structure
- **Swiper.js** - Service sliders and galleries

### Build Commands
```bash
npm run dev    # Tailwind watch mode with --minify
npm run build  # Production build
```

## Critical ACF Data Pattern

**All site data stored in ACF Options page:**
```php
get_field('телефон-1', 'options')    // Primary phone
get_field('телефон22', 'options')    // Secondary phone  
get_field('email', 'options')        // Contact email
get_field('адресс', 'options')       // Business address
get_field('график', 'options')       // Operating hours
get_field('hero')                    // Homepage hero content
```

**Phone sanitization is REQUIRED for tel: links:**

- **Используйте preg_replace для очистки номера телефона от лишних символов**
<a href="tel:<?=esc_attr(preg_replace('/[^0-9+]/', '', get_field('телефон_1', 'options')))?>">

## Component Architecture

### File Structure Logic
```
components/     # Reusable UI (mobile-menu, product-card, forms, scroll-top)
elements/       # Page-specific (catalog, product-filter, cart, reviews) 
js/modules/     # Vanilla JS modules (mobile-menu.js, modal-*.js, cart.js)
scss/block/     # Page sections (hero, catalog, products, footer)
```

### Template Part Loading
```php
// Подключение компонентов через WordPress template parts
get_template_part('components/mobile-menu');
get_template_part('elements/catalog');
get_template_part('elements/product-filter');
```

## JavaScript Patterns

### Mobile Menu with History API
```javascript
// Добавляем состояние в историю при открытии меню
window.history.pushState('menu-open', '', window.location.href);

// Обработка кнопки "Назад" браузера
window.addEventListener('popstate', (event) => {
    if (mobileMenu.classList.contains('open')) {
        closeMenus();
    }
});
```

### Performance Optimization
- **Yandex Metrika**: Loads after `window.load` + 100ms delay
- **All scripts**: Footer with dependency management
- **Hero image**: Preloaded with `fetchpriority="high"`

## Form & AJAX Structure

### Contact Forms
```php
<input type="hidden" name="action" value="artcly_mail_to">
```
All forms use custom AJAX handler with privacy policy integration.

## Development Guidelines

### Language & Localization
- **Comments and documentation**: Always in Russian language for local development team
- **Variable names**: Russian transliteration for business logic (телефон_1, адресс, график)
- **User-facing content**: Russian language and Cyrillic characters
- **Code suggestions**: Prefer Russian comments in generated code

### PHP Standards
- Always escape: `esc_html()`, `esc_url()`, `esc_attr()`
- Russian comments and field names for local team
- Check ACF field existence before output

### Styling Approach  
- Tailwind utilities first
- Custom SCSS only for complex interactions
- Component files in `scss/block/` match page sections
- стили scss компилируются с помощью плагина Live Sass Compiler в VSCode. tailwind стили компилируются с помощью npm
проверять скомпилированы ли они не надо, т.к. они компилируются в режиме watch и обновляются автоматически при сохранении файлов
- не меняй стили и верстку без предупреждения или обсуждения с командой, т.к. это может повлиять на работу других компонентов и страниц


### Performance Features
- WordPress bloat removal in `functions.php`
- Async Yandex Metrika loading
- LocalBusiness schema with ACF integration

## Common Deployment Pattern
Uses SFTP deployment to production server via `.vscode/sftp.json` configuration.

