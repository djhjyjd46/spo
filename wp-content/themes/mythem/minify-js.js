const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const jsDir = path.join(__dirname, 'js', 'modules');
const files = fs.readdirSync(jsDir).filter(f => f.endsWith('.js'));

// Исключаем cart-notification.js из объединения, чтобы не было дублирующего класса
// Универсальный список исключаемых файлов (можно добавить любые)
const exclude = [
    'cart-notification.js',
    'all.min.js', // Исключаем уже минифицированный файл
    // Добавьте сюда другие файлы, которые не должны попадать в объединение
];
const filesToConcat = files.filter(f => !exclude.includes(f));

const outputFile = path.join(jsDir, 'all.min.js');
let concat = '';

filesToConcat.forEach(file => {
    const input = path.join(jsDir, file);
    const minified = execSync(`terser "${input}" --compress --mangle`, { encoding: 'utf8' });
    concat += minified + '\n';
    console.log(`Минифицирован: ${file}`);
});

// Добавляем cart-notification.js в конец (только один раз)
const cartFile = path.join(jsDir, 'cart-notification.js');
if (fs.existsSync(cartFile)) {
    const minifiedCart = execSync(`terser "${cartFile}" --compress --mangle`, { encoding: 'utf8' });
    concat += minifiedCart + '\n';
    console.log('Добавлен CartNotification');
}

fs.writeFileSync(outputFile, concat);
console.log(`Все минифицированные файлы объединены в: all.min.js`);
