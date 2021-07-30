<main class="page__main page__main--feed">
    <div class="container">
        <h1 class="page__title page__title--feed">Моя лента</h1>
    </div>
    <div class="page__main-wrapper container">
        <section class="feed">
            <h2 class="visually-hidden">Лента</h2>
            <div class="feed__main-wrapper">
                <div class="feed__wrapper">
                    <?php foreach ($posts as $key => $post): ?>
                        <article class="feed__post post <?= $post['type'] ?>">
                            <header class="post__header post__author">
                                <a class="post__author-link"
                                   href="<?= $to('profile', ['user_id' => $post['user_id']]) ?>"
                                   title="Автор">
                                    <div class="post__avatar-wrapper">
                                        <img class="post__author-avatar" src="<?= htmlspecialchars($post['avatar']) ?>" alt="Аватар пользователя" width="60" height="60">
                                    </div>
                                    <div class="post__info">
                                        <b class="post__author-name"><?= $post['user_name'] ? htmlspecialchars($post['user_name']) : htmlspecialchars($post['login']) ?></b>
                                        <span class="post__time"><?= format_publication_date($post['date_add']) ?></span>
                                    </div>
                                </a>
                            </header>
                            <div class="post__main">
                                <?php if ($post['type'] == 'post-photo'): ?>
                                    <h2>
                                        <a href="<?= $to('post', ['ID' => $post['id']]) ?>"
                                        ><?= htmlspecialchars($post['title']) ?></a>
                                    </h2>
                                    <div class="post-photo__image-wrapper">
                                        <img src="<?= htmlspecialchars($post['content']) ?>" alt="Фото от пользователя" width="760" height="396">
                                    </div>
                                <?php elseif($post['type'] === 'post-text'): ?>
                                    <h2><a href="<?= $to('post', ['ID' => $post['id']]) ?>"><?= $post['title'] ?></a></h2>
                                    <?= cut_text(htmlspecialchars($post['content']), $to('post', ['ID' => $post['id']])) ?>
                                <?php elseif($post['type'] === 'post-video'): ?>
                                    <div class="post-video__block">
                                        <div class="post-video__preview">
                                            <?=embed_youtube_cover(htmlspecialchars($post['content']), 760, 393); ?>
                                        </div>
                                        <div class="post-video__control">
                                            <button class="post-video__play post-video__play--paused button button--video" type="button"><span class="visually-hidden">Запустить видео</span></button>
                                            <div class="post-video__scale-wrapper">
                                                <div class="post-video__scale">
                                                    <div class="post-video__bar">
                                                        <div class="post-video__toggle"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button class="post-video__fullscreen post-video__fullscreen--inactive button button--video" type="button"><span class="visually-hidden">Полноэкранный режим</span></button>
                                        </div>
                                        <button class="post-video__play-big button" type="button">
                                            <svg class="post-video__play-big-icon" width="27" height="28">
                                                <use xlink:href="#icon-video-play-big"></use>
                                            </svg>
                                            <span class="visually-hidden">Запустить проигрыватель</span>
                                        </button>
                                    </div>
                                <?php elseif($post['type'] === 'post-quote'): ?>
                                    <blockquote>
                                        <p>
                                            <?= htmlspecialchars($post['content']) ?>
                                        </p>
                                        <cite><?= htmlspecialchars($post['author']) ?></cite>
                                    </blockquote>
                                <?php elseif($post['type'] === 'post-link'): ?>
                                    <div class="post-link__wrapper">
                                        <a class="post-link__external" href="<?= htmlspecialchars($post['content']) ?>" title="Перейти по ссылке">
                                            <div class="post-link__icon-wrapper">
                                                <img src="https://www.google.com/s2/favicons?domain=<?= get_domain($post['content']) ?>" alt="Иконка">
                                            </div>
                                            <div class="post-link__info">
                                                <h3><?= htmlspecialchars($post['title']) ?></h3>
                                                <span><?= htmlspecialchars($post['content']) ?></span>
                                            </div>
                                            <svg class="post-link__arrow" width="11" height="16">
                                                <use xlink:href="#icon-arrow-right-ad"></use>
                                            </svg>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <footer class="post__footer post__indicators">
                                <div class="post__buttons">
                                    <a
                                        class="post__indicator post__indicator--likes button"
                                        href="<?= $to('likes', [
                                            'post_id' => $post['id'],
                                            'user_id' => $actual_user_id
                                        ]) ?>"
                                        title="Лайк">
                                        <svg class="post__indicator-icon" width="20" height="17">
                                            <use xlink:href="#<?= $check_is_liked_post($post['id']) ?>"></use>
                                        </svg>
                                        <span><?= $post['likes_count'] ?></span>
                                        <span class="visually-hidden">количество лайков</span>
                                    </a>
                                    <a class="post__indicator post__indicator--comments button"
                                       href="<?= $to('post', ['ID' => $post['id']]) ?>"
                                       title="Комментарии">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-comment"></use>
                                        </svg>
                                        <span><?= $post['comments_count'] ?></span>
                                        <span class="visually-hidden">количество комментариев</span>
                                    </a>
                                    <a class="post__indicator post__indicator--repost button" href="<?= $to('repost', ['ID' => $post['id']]) ?>" title="Репост">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-repost"></use>
                                        </svg>
                                        <span><?= $post['reposts_count'] ?></span>
                                        <span class="visually-hidden">количество репостов</span>
                                    </a>
                                </div>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <ul class="feed__filters filters">
                <?php foreach ($content_types as $key => $type): ?>
                    <li
                        class="feed__filters-item filters__item">
                        <a class="button filters__button filters__button--<?= $type['title'] ?>
                        <?= $active_type_content_id == $type['id'] ? 'filters__button--active' : '' ?>"
                        href="<?= $to('feed', ['content_id' =>$type['id']]) ?>">
                            <?php if ($type['title'] !== 'all'): ?>
                                <span class="visually-hidden"><?= $type['label'] ?></span>
                                <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-<?= $type['title'] ?>"></use>
                                </svg>
                            <?php else: ?>
                                <span><?= $type['label'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <aside class="promo">
            <article class="promo__block promo__block--barbershop">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Все еще сидишь на окладе в офисе? Открой свой барбершоп по нашей франшизе!
                </p>
                <a class="promo__link" href="#">
                    Подробнее
                </a>
            </article>
            <article class="promo__block promo__block--technomart">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Товары будущего уже сегодня в онлайн-сторе Техномарт!
                </p>
                <a class="promo__link" href="#">
                    Перейти в магазин
                </a>
            </article>
            <article class="promo__block">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Здесь<br> могла быть<br> ваша реклама
                </p>
                <a class="promo__link" href="#">
                    Разместить
                </a>
            </article>
        </aside>
    </div>
</main>
