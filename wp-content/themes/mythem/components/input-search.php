<div class="search relative">
    <form role="search" method="get" action="<?= esc_url(home_url('/')) ?>">
        <input type="search" name="s" required value="<?= esc_attr(get_search_query()) ?>" placeholder="Поиск по сайту"
            class="search-input">
        <button type="submit" class="search-button button--transparent absolute right-0 top-0 h-full px-4">
            <img src="<?= get_template_directory_uri(); ?>/images/icons/search.png" alt="Поиск">
        </button>
    </form>
</div>