<section class="professii">
       <div class="container">
           <h2 class="section-title">Профессии</h2>
           <div class="search relative mb-10">
               <div class="relative">
                   <input id="profession-search" type="search" class="search-input h-10 !w-full rounded-lg"
                       placeholder="Поиск по коду профессии/названию" aria-label="Поиск по профессиям">
                   <button type="button" id="profession-clear"
                       class="search-button button--transparent absolute right-0 top-0 h-full px-4"
                       aria-label="Очистить поиск">
                       <img src="<?= get_template_directory_uri(); ?>/images/icons/search.png" alt="Поиск">
                   </button>
               </div>
               <span class="search-invalid text-red-500 mt-2 hidden">Ничего не найдено</span>
           </div>
           <div class="result-table">
               <table>
                   <thead>
                       <tr>
                           <th>Код профессии
                               ОКРБ 014-2017</th>
                           <th>Наименование профессии</th>
                           <th>Срок обучения</th>
                           <th>Стоимость</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php
                        // Выводим данные из записей типа 'profession'
                        // в любой таксономии для CPT 'profession' есть термин,
                        // название которого совпадает с заголовком текущей страницы.
                        $current_page_title = trim(get_the_title());

                        $args = [
                            'post_type' => 'profession',
                            'posts_per_page' => -1,
                            'post_status' => 'publish',
                            'orderby' => 'title',
                            'order' => 'ASC',
                        ];

                        $taxonomies = get_object_taxonomies('profession', 'names');
                        if (!empty($taxonomies)) {
                            $args['tax_query'] = ['relation' => 'OR'];
                            foreach ($taxonomies as $tax) {
                                $args['tax_query'][] = [
                                    'taxonomy' => $tax,
                                    'field' => 'name',
                                    'terms' => $current_page_title,
                                    'operator' => 'IN',
                                ];
                            }
                        } else {
                            // Если у CPT нет таксономий — не показываем записи
                            $args['post__in'] = [0];
                        }

                        $prof_q = new WP_Query($args);
                        if ($prof_q->have_posts()) :
                            while ($prof_q->have_posts()) : $prof_q->the_post();
                                $pid = get_the_ID();
                                $prof = get_field('professiya', $pid);
                                $code = is_array($prof) && isset($prof['kod_professii']) ? $prof['kod_professii'] : '';
                                $duration = is_array($prof) && isset($prof['srok_obucheniya']) ? $prof['srok_obucheniya'] : '';
                                $cost = is_array($prof) && isset($prof['stoimost']) ? $prof['stoimost'] : '';

                                // Проверяем, есть ли у записи термин в любой таксономии,
                                // имя которого совпадает с заголовком текущей страницы.
                                $include_row = false;
                                if (!empty($taxonomies)) {
                                    foreach ($taxonomies as $tax_check) {
                                        $terms = get_the_terms($pid, $tax_check);
                                        if ($terms && !is_wp_error($terms)) {
                                            foreach ($terms as $t) {
                                                if (mb_strtolower(trim($t->name)) === mb_strtolower($current_page_title)) {
                                                    $include_row = true;
                                                    break 2;
                                                }
                                            }
                                        }
                                    }
                                }

                                if (! $include_row) {
                                    // пропускаем запись, если ни один термин не совпал
                                    continue;
                                }
                        ?>
                               <tr>
                                   <td><?= esc_html($code); ?></td>
                                   <td><a href="<?= esc_url(get_permalink($pid)) ?>"><?= esc_html(get_the_title()); ?></a></td>
                                   <td><?= esc_html($duration); ?></td>
                                   <td><?= esc_html($cost); ?></td>
                               </tr>
                       <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            echo '<tr><td colspan="4">Нет данных для отображения</td></tr>';
                        endif;
                        ?>
               </table>
               <div class="flex items-center justify-between mt-6">
                <div class="add-rows">
                    <button id="load-more-rows" class="px-4 py-2 bg-blue-600 rounded-lg hover:bg-blue-700">
                        Показать больше
                    </button>
                </div>
                   <div id="profession-meta" class="text-sm text-gray-600">Показано 0 из 0</div>
               </div>
           </div>
           <style>
               /* enable horizontal scrolling for wide tables */
               .result-table {
                   overflow-x: auto;
                   -webkit-overflow-scrolling: touch;
               }

               .result-table table {
                   min-width: 900px;
               }

               table {
                   width: 100%;
                   border-collapse: collapse;
                   text-align: center;
                   border-radius: 24px;
                   border: 2px solid #e9e9e9;
                   overflow: hidden;
                   /* важно для корректного отображения */
                   border-collapse: separate;
                   /* по умолчанию */
                   border-spacing: 0;
                   /* убираем промежутки между ячейками */
               }

               thead {
                   background: #007DC633;
                   white-space: pre-line;

               }

               th,
               td {
                   border: 1px solid #ddd;
                   padding: 8px;
               }
           </style>
   </section>

   <script>
       (() => {
           const input = document.getElementById('profession-search');
           const tbody = document.querySelector('.result-table table tbody');
           const meta = document.getElementById('profession-meta');
           const invalid = document.querySelector('.search-invalid');
           const loadMoreBtn = document.getElementById('load-more-rows');

           const clearBtn = document.getElementById('profession-clear');
           if (!input || !tbody || !meta) return;

           const ROWS_PER_PAGE = 20;
           const allRows = Array.from(tbody.querySelectorAll('tr')).map(row => ({
               el: row,
               text: (row.textContent || row.innerText || '').toLowerCase()
           }));

           let filtered = allRows.slice();
           let shownCount = ROWS_PER_PAGE;

           const render = () => {
               // hide all first
               allRows.forEach(r => r.el.style.display = 'none');
               // show slice
               const toShow = filtered.slice(0, shownCount);
               toShow.forEach(r => r.el.style.display = '');
               meta.textContent = `Показано ${Math.min(shownCount, filtered.length)} из ${filtered.length}`;
               // показываем/скрываем сообщение "ничего не найдено"
               if (allRows.length === 0 || toShow.length === 0) {
                   invalid.classList.remove('hidden');
               } else {
                   invalid.classList.add('hidden');
               }
               // показываем или скрываем кнопку "Показать больше"
               if (loadMoreBtn) {
                   loadMoreBtn.style.display = (filtered.length > shownCount) ? '' : 'none';
               }
           };

           const doFilter = () => {
               const q = input.value.trim().toLowerCase();
               if (!q) {
                   filtered = allRows.slice();
               } else {
                   filtered = allRows.filter(r => r.text.indexOf(q) !== -1);
               }
               // сбрасываем количество показанных строк при новом фильтре
               shownCount = ROWS_PER_PAGE;
               render();
           };

           let debounceId = null;
           input.addEventListener('input', () => {
               if (debounceId) cancelAnimationFrame(debounceId);
               debounceId = requestAnimationFrame(() => {
                   doFilter();
               });
           });

           if (clearBtn) clearBtn.addEventListener('click', () => {
               input.value = '';
               doFilter();
               input.focus();
           });

           // Обработчик "Показать больше"
           if (loadMoreBtn) {
               loadMoreBtn.addEventListener('click', () => {
                   shownCount += ROWS_PER_PAGE;
                   render();
                   // после загрузки дополнительных строк можно плавно проскроллить к кнопке
                   // loadMoreBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
               });
           }

           doFilter();
       })();
   </script>