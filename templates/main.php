<section class="page__main page__main--popular">
    <div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    <div class="popular container">
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <li class="sorting__item sorting__item--popular">
                        <a class="sorting__link <?= $sort_type == 'popular'
                            ? 'sorting__link--active'
                            : '' ?>
                            <?= $sort_direction == 'ASC'
                            ? 'sorting__link--reverse'
                            : '' ?>"
                           href="<?= $get_sort_link('popular') ?>">
                            <span>Популярность</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="/img/sprite.svg#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link <?= $sort_type == 'like'
                            ? 'sorting__link--active'
                            : '' ?>
                            <?= $sort_direction == 'ASC'
                            ? 'sorting__link--reverse'
                            : '' ?>"
                           href="<?= $get_sort_link('like') ?>">
                            <span>Лайки</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="/img/sprite.svg#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link <?= $sort_type == 'date'
                            ? 'sorting__link--active'
                            : '' ?>
                            <?= $sort_direction == 'ASC'
                            ? 'sorting__link--reverse'
                            : '' ?>"
                           href="<?= $get_sort_link('date') ?>">
                            <span>Дата</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="/img/sprite.svg#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="popular__filters filters">
                <b class="popular__filters-caption filters__caption">Тип контента:</b>
                <ul class="popular__filters-list filters__list">
                    <?php foreach ($content_types as $key => $type) : ?>
                        <li
                                class="popular__filters-item filters__item
                                   <?= $type['title'] == 'all'
                                    ? 'popular__filters-item--all filters__item--all'
                                    : '' ?>">
                            <a
                                    class="filters__button filters__button--<?= $type['title'] ?>
                                <?= $type['title'] == 'all' ? 'filters__button--ellipse' : '' ?>
                                <?= $active_type_content_id == $type['id'] ||
                                    (!$active_type_content_id && $type['title'] == 'all')
                                        ? 'filters__button--active'
                                        : '' ?>
                                button"
                                    href="<?= utils_url_to('popular', [
                                        'content_id' => $type['id'],
                                        'sort_type' => $sort_type,
                                        'sort_direction' => $sort_direction
                                    ]) ?>">

                                <?php if ($type['title'] !== 'all') : ?>
                                    <span class="visually-hidden"><?= $type['label'] ?></span>
                                    <svg class="filters__icon" width="22" height="18">
                                        <use xlink:href="/img/sprite.svg#icon-filter-<?= $type['title'] ?>"></use>
                                    </svg>
                                <?php else : ?>
                                    <span><?= $type['label'] ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="popular__posts">
            <?php foreach ($popular_posts as $key => $post) : ?>
                <article class="popular__post post <?= $post['type'] ?>">
                    <header class="post__header">
                        <h2>
                            <a href="<?= utils_url_to('post', ['ID' => $post['id']]) ?>"
                            ><?= htmlspecialchars($post['title']) ?></a>
                        </h2>
                    </header>
                    <div class="post__main">
                        <?php if ($post['type'] == 'post-quote') : ?>
                            <blockquote>
                                <p>
                                    <?= htmlspecialchars($post['content']) ?>
                                </p>
                                <cite>Неизвестный Автор</cite>
                            </blockquote>
                        <?php elseif ($post['type'] == 'post-link') : ?>
                            <div class="post-link__wrapper">
                                <a class="post-link__external" href="<?= htmlspecialchars($post['content']) ?>"
                                   title="Перейти по ссылке">
                                    <div class="post-link__info-wrapper">
                                        <div class="post-link__icon-wrapper">
                                            <img src="https://www.google.com/s2/favicons?domain=vitadental.ru"
                                                 alt="Иконка">
                                        </div>
                                        <div class="post-link__info">
                                            <h3><?= htmlspecialchars($post['title']) ?></h3>
                                        </div>
                                    </div>
                                    <span><?= htmlspecialchars($post['content']) ?></span>
                                </a>
                            </div>
                        <?php elseif ($post['type'] == 'post-photo') : ?>
                            <div class="post-photo__image-wrapper">
                                <img src="<?= htmlspecialchars($post['content']) ?>" alt="Фото от пользователя"
                                     width="360" height="240">
                            </div>
                        <?php elseif ($post['type'] == 'post-video') : ?>
                            <div class="post-video__block">
                                <div class="post-video__preview">
                                    <?= embed_youtube_cover(htmlspecialchars($post['content'])); ?>
                                </div>
                                <a href="<?= $post['content'] ?>" class="post-video__play-big button">
                                    <svg class="post-video__play-big-icon" width="14" height="14">
                                        <use xlink:href="/img/sprite.svg#icon-video-play-big"></use>
                                    </svg>
                                    <span class="visually-hidden">Запустить проигрыватель</span>
                                </a>
                            </div>
                        <?php else : ?>
                            <?= cut_text(htmlspecialchars($post['content']), utils_url_to('post', [
                                'ID' => $post['id']
                            ])) ?>
                        <?php endif; ?>

                    </div>
                    <footer class="post__footer">
                        <div class="post__author">
                            <a class="post__author-link"
                               href="<?= utils_url_to('profile', ['user_id' => $post['user_id']]) ?>"
                               title="Автор">
                                <div class="post__avatar-wrapper">
                                    <?php if ($post['avatar']) : ?>
                                        <img class="post__author-avatar" src="<?= htmlspecialchars($post['avatar']) ?>"
                                             alt="Аватар пользователя">
                                    <?php endif; ?>
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name"><?= $post['user_name'] ?? $post['login'] ?></b>
                                    <time class="post__time"
                                          datetime="<?= $post['date_add'] ?>"
                                          title="<?= date_format(date_create($post['date_add']), 'd.m.Y h:i') ?>">
                                        <?= format_publication_date($post['date_add']) ?></time>
                                </div>
                            </a>
                        </div>
                        <div class="post__indicators">
                            <div class="post__buttons">
                                <a class="post__indicator post__indicator--likes button"
                                   href="<?= utils_url_to('likes', [
                                       'post_id' => $post['id'],
                                       'user_id' => $actual_user_id
                                   ]) ?>"
                                   title="Лайк">
                                    <svg class="post__indicator-icon" width="20" height="17">
                                        <use xlink:href="/img/sprite.svg#<?= $check_is_liked_post($post['id']) ?>"></use>
                                    </svg>
                                    <span><?= $post['likes_count'] ?></span>
                                    <span class="visually-hidden">количество лайков</span>
                                </a>
                                <a class="post__indicator post__indicator--comments button"
                                   href="<?= utils_url_to('post', ['ID' => $post['id']]) ?>"
                                   title="Комментарии">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="/img/sprite.svg#icon-comment"></use>
                                    </svg>
                                    <span><?= $post['comments_count'] ?></span>
                                    <span class="visually-hidden">количество комментариев</span>
                                </a>
                            </div>
                        </div>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if ($popular_posts_count > $limit) : ?>
            <div class="popular__page-links">
                <?php if ($page > 1) : ?>
                    <a class="popular__page-link popular__page-link--prev button button--gray"
                       href="<?= utils_url_to('popular', [
                           'content_id' => $active_type_content_id,
                           'page' => $page > 1 ? $page - 1 : 1,
                           'sort_type' => $sort_type,
                           'sort_direction' => $sort_direction
                       ]) ?>"
                    >Предыдущая страница</a>
                <?php else : ?>
                    <span
                        class="popular__page-link
                        popular__page-link--prev
                        button button--gray"
                    >Предыдущая страница</span>
                <?php endif; ?>

                <?php if (($popular_posts_count / $limit) > $page) : ?>
                    <a class="popular__page-link popular__page-link--next button button--gray"
                       href="<?= utils_url_to('popular', [
                           'content_id' => $active_type_content_id,
                           'page' => ($popular_posts_count / $limit) > $page ? $page + 1 : $page,
                           'sort_type' => $sort_type,
                           'sort_direction' => $sort_direction
                       ]) ?>"
                    >Следующая страница</a>
                <?php else : ?>
                    <span
                        class="popular__page-link
                        popular__page-link--next
                        button button--gray"
                    >Следующая страница</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
