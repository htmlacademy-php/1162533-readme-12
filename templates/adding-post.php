<main class="page__main page__main--adding-post">
    <div class="page__main-section">
        <div class="container">
            <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
        </div>
        <div class="adding-post container">
            <div class="adding-post__tabs-wrapper tabs">
                <div class="adding-post__tabs filters">
                    <ul class="adding-post__tabs-list filters__list tabs__list">
                        <?php foreach ($post_tabs as $key => $tab) : ?>
                            <li class="adding-post__tabs-item filters__item">
                                <a class="adding-post__tabs-link filters__button
                                    filters__button--<?= $key ?>
                                    <?= $active_tab === $key
                                    ? 'filters__button--active tabs__item tabs__item--active'
                                    : '' ?> button"
                                   href="<?= $get_tabs_link($key) ?>">
                                    <svg class="filters__icon" width="22" height="18">
                                        <use xlink:href="/img/sprite.svg#icon-filter-<?= $key ?>"></use>
                                    </svg>
                                    <span><?= $tab ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="adding-post__tab-content"><?= $active_form ?></div>
            </div>
        </div>
    </div>
</main>
<div class="modal modal--adding">
    <div class="modal__wrapper">
        <button class="modal__close-button button" type="button">
            <svg class="modal__close-icon" width="18" height="18">
                <use xlink:href="/img/sprite.svg#icon-close"></use>
            </svg>
            <span class="visually-hidden">Закрыть модальное окно</span></button>
        <div class="modal__content">
            <h1 class="modal__title">Пост добавлен</h1>
            <p class="modal__desc">
                Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается
                самым глубоким озером в мире. Он окружен сефтью пешеходных маршрутов, называемых Большой байкальской
                тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для
                летних экскурсий.
            </p>
            <div class="modal__buttons">
                <a class="modal__button button button--main" href="#">Синяя кнопка</a>
                <a class="modal__button button button--gray" href="#">Серая кнопка</a>
            </div>
        </div>
    </div>
</div>
