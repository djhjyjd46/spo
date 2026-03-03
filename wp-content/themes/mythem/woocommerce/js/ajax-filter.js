document.addEventListener('DOMContentLoaded', function () {
    const catalogProducts = document.querySelector('.catalog-products');
    if (!catalogProducts) return; // Выходим, если не найден контейнер продуктов

    const sortingOptions = document.querySelectorAll('.sorting-options a');
    const categoryLinks = document.querySelectorAll('.categories-list a');
    const loadMoreBtn = document.querySelector('.load-more-btn');

    let isLoading = false;
    let currentPage = 1;
    let hasMoreItems = true;

    // Кэш для хранения полученных данных
    const productsCache = {};

    // Функция для сохранения состояния фильтрации в URL
    function updateUrl(params) {
        const url = new URL(window.location.href);

        // Удалим существующие параметры, которые мы контролируем
        ['orderby', 'category', 'paged'].forEach(param => url.searchParams.delete(param));

        // Добавим новые параметры
        Object.entries(params).forEach(([key, value]) => {
            if (value) url.searchParams.set(key, value);
        });

        // Обновим URL без перезагрузки страницы
        window.history.pushState({}, '', url);
    }

    // Функция для получения текущих параметров
    function getCurrentParams() {
        const url = new URL(window.location.href);
        return {
            orderby: url.searchParams.get('orderby') || '',
            category: url.searchParams.get('category') || '',
            paged: url.searchParams.get('paged') || 1
        };
    }

    // Функция для генерации уникального ключа кэша
    function getCacheKey(params) {
        return `${params.category || 'all'}_${params.orderby || 'default'}_${params.paged || 1}`;
    }

    // Обновляет активные классы для категорий
    function updateActiveCategories(categorySlug) {
        // Сначала удаляем активный класс у всех категорий
        categoryLinks.forEach(link => {
            link.parentElement.classList.remove('active');
        });

        // Если выбрана опция "Все категории"
        if (!categorySlug) {
            const allCategoriesLink = document.querySelector('.categories-list li:first-child a');
            if (allCategoriesLink) {
                allCategoriesLink.parentElement.classList.add('active');
            }
            return;
        }

        // Находим ссылку с нужным слагом категории и добавляем активный класс
        categoryLinks.forEach(link => {
            const urlParts = link.href.split('/');
            const linkSlug = urlParts[urlParts.length - 2];

            if (linkSlug === categorySlug) {
                link.parentElement.classList.add('active');
            }
        });
    }

    // Обновление активных классов для сортировки
    function updateActiveSorting(orderby) {
        sortingOptions.forEach(link => {
            const linkOrderby = new URL(link.href).searchParams.get('orderby');
            link.parentElement.classList.toggle('active', linkOrderby === orderby);
        });
    }



    // Функция для управления кнопкой "Загрузить еще"
    function manageLoadMoreButton(hasMore) {
        // Удалить существующую кнопку, если она есть
        const existingButton = document.querySelector('.load-more-btn');
        if (existingButton) {
            existingButton.parentElement.remove();
        }

        // Если есть еще товары для загрузки, добавляем кнопку
        if (hasMore) {
            const loadMoreWrapper = document.createElement('div');
            loadMoreWrapper.className = 'load-more-wrapper';

            const newLoadMoreBtn = document.createElement('button');
            newLoadMoreBtn.className = 'load-more-btn';
            newLoadMoreBtn.classList.add('button');
            newLoadMoreBtn.textContent = 'Загрузить еще';

            loadMoreWrapper.appendChild(newLoadMoreBtn);
            catalogProducts.appendChild(loadMoreWrapper);

            // Добавляем обработчик событий для кнопки "Загрузить еще"
            newLoadMoreBtn.addEventListener('click', function () {
                currentPage++;
                loadProducts({
                    ...getCurrentParams(),
                    paged: currentPage
                }, true);
            });
        }
    }

    // Отображение HTML товаров
    function renderProducts(htmlContent, append = false) {
        if (!append) {
            catalogProducts.innerHTML = '';
        }

        if (htmlContent.trim()) {
            let productListContainer;

            if (append) {
                productListContainer = document.querySelector('.catalog-productlist');

                // Если контейнер не найден, создаем новый
                if (!productListContainer) {
                    productListContainer = document.createElement('ul');
                    productListContainer.className = 'catalog-productlist';
                    catalogProducts.appendChild(productListContainer);
                }

                // Создаем временный контейнер для парсинга HTML
                const tempContainer = document.createElement('div');
                tempContainer.innerHTML = htmlContent;

                // Получаем новые элементы для анимации
                const newItems = Array.from(tempContainer.children);

                // Добавляем товары к существующему списку
                newItems.forEach(child => {
                    productListContainer.appendChild(child);
                });

            } else {
                // Создаем новый список товаров
                productListContainer = document.createElement('ul');
                productListContainer.className = 'catalog-productlist';
                productListContainer.innerHTML = htmlContent;
                catalogProducts.appendChild(productListContainer);

            }
        } else if (!append) {
            catalogProducts.innerHTML = '<p class="no-products">Товары не найдены.</p>';
        }
    }



    // Обновление состояния интерфейса
    function updateUIState(queryParams) {
        // Обновляем активные классы для сортировки и категорий
        updateActiveSorting(queryParams.orderby);
        updateActiveCategories(queryParams.category);

        // Обновляем заголовок страницы

        // Проверяем необходимость отображения кнопки "Загрузить еще"
        manageLoadMoreButton(hasMoreItems);
    }

    // Установка состояния загрузки
    function setLoadingState(loading, append = false) {
        if (loading) {
            if (!append) {
                catalogProducts.classList.add('loading');
            } else {
                // При добавлении новых товаров показываем лоадер в кнопке
                const currentLoadMoreBtn = document.querySelector('.load-more-btn');
                if (currentLoadMoreBtn) {
                    currentLoadMoreBtn.disabled = true;
                    currentLoadMoreBtn.classList.add('loading');

                    const btnLoader = document.createElement('div');
                    btnLoader.className = 'btn-loader';
                    currentLoadMoreBtn.prepend(btnLoader);
                    currentLoadMoreBtn.textContent = ' Загрузка...';
                }
            }
        } else {
            catalogProducts.classList.remove('loading');

            // Восстанавливаем состояние кнопки, если она есть
            const currentLoadMoreBtn = document.querySelector('.load-more-btn');
            if (currentLoadMoreBtn && currentLoadMoreBtn.classList.contains('loading')) {
                currentLoadMoreBtn.disabled = false;
                currentLoadMoreBtn.classList.remove('loading');
                const btnLoader = currentLoadMoreBtn.querySelector('.btn-loader');
                if (btnLoader) {
                    btnLoader.remove();
                }
                currentLoadMoreBtn.textContent = 'Загрузить еще';
            }
        }
    }

    // Основная функция для загрузки товаров
    async function loadProducts(params = {}, append = false) {
        if (isLoading) return;
        isLoading = true;

        // Если это не добавление новых товаров, сбрасываем текущую страницу на 1
        if (!append) {
            currentPage = 1;
            params.paged = 1;
            hasMoreItems = true; // При изменении фильтров всегда предполагаем, что есть еще товары
        }

        // Получаем текущие параметры и объединяем с новыми
        const queryParams = append ? params : { ...getCurrentParams(), ...params };

        // Обновляем URL только при первичной загрузке (не при добавлении)
        if (!append) {
            updateUrl(queryParams);
        }

        // Генерируем ключ для кэша
        const cacheKey = getCacheKey(queryParams);

        // Проверяем, есть ли данные в кэше (только для первой загрузки, не для appending)
        if (!append && productsCache[cacheKey]) {
            // Используем кэшированные данные
            const cachedData = productsCache[cacheKey];
            renderProducts(cachedData.html, false);

            // Обновляем состояние hasMoreItems из кэша
            hasMoreItems = cachedData.hasMore;
            currentPage = cachedData.currentPage;

            updateUIState(queryParams);
            isLoading = false;
            return;
        }

        // Устанавливаем состояние загрузки
        setLoadingState(true, append);

        try {
            // Создаем FormData для отправки запроса
            const formData = new FormData();
            formData.append('action', 'ajax_filter_products');
            formData.append('append', append.toString());
            formData.append('nonce', ajax_object.nonce);

            // Добавляем параметры
            Object.entries(queryParams).forEach(([key, value]) => {
                if (value) formData.append(key, value);
            });

            // Отправляем запрос
            const response = await fetch(ajax_object.ajax_url, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Ошибка сети');
            }

            const data = await response.json();

            if (data.success) {
                // Отображаем полученные товары
                renderProducts(data.data.html, append);

                // Если не добавляем товары, сохраняем в кэш вместе с информацией о наличии дополнительных товаров
                if (!append) {
                    productsCache[cacheKey] = {
                        html: data.data.html,
                        hasMore: data.data.has_more,
                        currentPage: parseInt(data.data.current_page, 10)
                    };
                }

                // Обновляем состояние hasMoreItems и текущую страницу
                hasMoreItems = data.data.has_more;
                currentPage = parseInt(data.data.current_page, 10);

                // Обновляем состояние интерфейса
                updateUIState(queryParams);

            } else {
                console.error('Ошибка загрузки товаров:', data.data);
            }
        } catch (error) {
            console.error('Произошла ошибка:', error);

            // В случае ошибки, показываем кнопку "Загрузить еще" если товары есть на странице
            if (document.querySelectorAll('.products__item').length > 0) {
                manageLoadMoreButton(true);
            }
        } finally {
            // Снимаем состояние загрузки
            setLoadingState(false, append);
            isLoading = false;
        }
    }

    // Очистка кэша при определенных условиях
    function setupCacheCleanup() {
        const CACHE_LIFETIME = 5 * 60 * 1000; // 5 минут в миллисекундах

        setInterval(() => {
            console.log('Очистка кэша товаров');
            Object.keys(productsCache).forEach(key => {
                delete productsCache[key];
            });
        }, CACHE_LIFETIME);
    }

    // Добавляем обработчики событий для сортировки
    sortingOptions.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const url = new URL(this.href);
            const orderby = url.searchParams.get('orderby');
            loadProducts({ orderby: orderby });
        });
    });

    // Добавляем обработчики событий для категорий
    categoryLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            // Если это ссылка на страницу магазина (Все категории)
            if (this.href === ajax_object.shop_url) {
                loadProducts({ category: '', paged: 1 });
            } else {
                // Извлекаем слаг категории из URL
                const urlParts = this.href.split('/');
                const categorySlug = urlParts[urlParts.length - 2];
                loadProducts({ category: categorySlug, paged: 1 });
            }
        });
    });

    // Добавляем обработчик для кнопки "Загрузить еще"
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () {
            currentPage++;
            loadProducts({
                ...getCurrentParams(),
                paged: currentPage
            }, true);
        });
    }

    // Инициализация
    function init() {
        // Настраиваем очистку кэша
        setupCacheCleanup();

        // Проверяем параметры URL при загрузке страницы и применяем фильтры если нужно
        const urlParams = getCurrentParams();
        if (urlParams.category || urlParams.orderby || parseInt(urlParams.paged) > 1) {
            loadProducts(urlParams);
        } else {
            // При первой загрузке страницы проверяем, есть ли еще товары для кнопки "Загрузить еще"
            const totalProducts = parseInt(catalogProducts.dataset.totalProducts || '0');
            const existingItems = document.querySelectorAll('.catalog-productlist li, .products__item');
            const initialProductCount = existingItems.length;

            hasMoreItems = totalProducts > initialProductCount;
            manageLoadMoreButton(hasMoreItems);
        }
    }

    // Запуск инициализации
    init();
});