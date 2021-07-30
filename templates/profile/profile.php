<main class="page__main page__main--profile">
    <h1 class="visually-hidden">Профиль</h1>
    <div class="profile profile--default">
        <div class="profile__user-wrapper">
            <div class="profile__user user container">
                <div class="profile__user-info user__info">
                    <div class="profile__avatar user__avatar">
                        <img class="profile__picture user__picture" src="<?= $user_info['avatar'] ?>" alt="Аватар пользователя">
                    </div>
                    <div class="profile__name-wrapper user__name-wrapper">
                        <span class="profile__name user__name"><?= $user_info['login'] ?></span>
                        <time
                            class="profile__user-time user__time"
                            datetime="<?= $user_info['date_add'] ?>"
                        ><?= format_register_date($user_info['date_add']) ?></time>
                    </div>
                </div>
                <div class="profile__rating user__rating">
                    <p class="profile__rating-item user__rating-item user__rating-item--publications">
                        <span class="user__rating-amount"><?= $user_info['count_posts'] ?></span>
                        <span
                            class="profile__rating-text user__rating-text"
                        ><?= get_noun_plural_form($user_info['count_posts'], 'публикация', 'публикации', 'публикаций') ?></span>
                    </p>
                    <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
                        <span class="user__rating-amount"><?= $user_info['count_followers'] ?></span>
                        <span
                            class="profile__rating-text user__rating-text"
                        ><?= get_noun_plural_form($user_info['count_followers'], 'подписчик', 'подписчика', 'подписчиков') ?></span>
                    </p>
                </div>

                <div class="profile__user-buttons user__buttons">
                    <?php if(!$is_your_profile): ?>
                        <?php if($is_subscription): ?>
                            <a href="<?= $to('subscription', [
                                'user_id' => $user_id,
                                'follower_id' => $actual_user_id,
                                'action' => 'remove'
                            ]) ?>"
                               class="profile__user-button user__button user__button--subscription button button--quartz">Отписаться</a>
                            <a
                                class="profile__user-button user__button user__button--writing button button--green"
                                href="<?= $to('messages', ['user' => $user_id]) ?>"
                            >Сообщение</a>
                        <?php else: ?>
                            <a href="<?= $to('subscription', [
                                'user_id' => $user_id,
                                'follower_id' => $actual_user_id,
                                'action' => 'add'
                            ]) ?>"
                               class="profile__user-button user__button user__button--subscription button button--main">Подписаться</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="profile__tabs-wrapper tabs">
            <div class="container">
                <div class="profile__tabs filters">
                    <b class="profile__tabs-caption filters__caption">Показать:</b>
                    <ul class="profile__tabs-list filters__list tabs__list">
                        <li class="profile__tabs-item filters__item">
                            <a href="<?= $to('profile', [
                                'user_id' => $user_id,
                                'tab' => 'posts'
                            ]) ?>"
                               class="profile__tabs-link filters__button tabs__item <?= $active_tab == 'posts' ? 'filters__button--active tabs__item--active' : '' ?> button">Посты</a>
                        </li>
                        <li class="profile__tabs-item filters__item">
                            <a href="<?= $to('profile', [
                                'user_id' => $user_id,
                                'tab' => 'likes'
                            ]) ?>"
                                class="profile__tabs-link filters__button tabs__item <?= $active_tab == 'likes' ? 'filters__button--active tabs__item--active' : '' ?> button">Лайки</a>
                        </li>
                        <li class="profile__tabs-item filters__item">
                            <a href="<?= $to('profile', [
                                'user_id' => $user_id,
                                'tab' => 'subscriptions'
                            ]) ?>"
                               class="profile__tabs-link filters__button tabs__item <?= $active_tab == 'subscriptions' ? 'filters__button--active tabs__item--active' : '' ?> button">Подписки</a>
                        </li>
                    </ul>
                </div>
                <div class="profile__tab-content">
                    <?php if($active_tab == 'posts'): ?>
                        <section class="profile__posts tabs__content tabs__content--active">
                        <h2 class="visually-hidden">Публикации</h2>
                            <?php if (empty($user_posts)): ?>
                                <p>Пока нет ни одного поста</p>
                            <?php else: ?>
                                <?php foreach ($user_posts as $post): ?>
                                    <article class="profile__post post <?= $post['content_type_title'] ?>">
                                        <header class="post__header">
                                            <?php if($post['repost']): ?>
                                            <div class="post__author">
                                                <a class="post__author-link" href="<?= $to('profile', ['user_id' => $post['origin_author']]) ?>" title="Автор">
                                                    <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                                                        <img class="post__author-avatar" src="<?= $post['origin_author_avatar'] ?>" alt="Аватар пользователя">
                                                    </div>
                                                    <div class="post__info">
                                                        <b class="post__author-name">Репост: <?= $post['origin_author_post'] ?></b>
                                                        <time
                                                            class="post__time"
                                                            datetime="<?= $post['date_add'] ?>"
                                                        ><?= format_publication_date($post['date_add']) ?></time>
                                                    </div>
                                                </a>
                                            </div>
                                            <?php else: ?>
                                            <h2><a href="<?= $to('post', ['ID' => $post['id']]) ?>"><?= $post['title'] ?></a></h2>
                                            <?php endif; ?>
                                        </header>
                                        <div class="post__main">
                                            <?php if($post['content_type_title'] === 'post-photo'): ?>
                                                <div class="post-photo__image-wrapper">
                                                    <img src="<?= $post['content'] ?>" alt="Фото от пользователя" width="760" height="396">
                                                </div>
                                            <?php elseif($post['content_type_title'] === 'post-text'): ?>
                                                <?= cut_text(htmlspecialchars($post['content']), $to('post', ['ID' => $post['id']])) ?>
                                            <?php elseif($post['content_type_title'] === 'post-video'): ?>
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
                                            <?php elseif($post['content_type_title'] === 'post-quote'): ?>
                                                <blockquote>
                                                    <p>
                                                        <?= htmlspecialchars($post['content']) ?>
                                                    </p>
                                                    <cite><?= htmlspecialchars($post['author']) ?></cite>
                                                </blockquote>
                                            <?php elseif($post['content_type_title'] === 'post-link'): ?>
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
                                        <footer class="post__footer">
                                            <div class="post__indicators">
                                                <div class="post__buttons">
                                                    <a href="<?= $to('likes', [
                                                        'post_id' => $post['id'],
                                                        'user_id' => $actual_user_id
                                                    ]) ?>" class="post__indicator post__indicator--likes button" title="Лайк">
                                                        <svg class="post__indicator-icon" width="20" height="17">
                                                            <use xlink:href="#<?= $check_is_liked_post($post['id']) ?>"></use>
                                                        </svg>
                                                        <span><?= $post['likes_count'] ?></span>
                                                        <span class="visually-hidden">количество лайков</span>
                                                    </a>
                                                    <a
                                                        class="post__indicator post__indicator--repost button"
                                                        href="<?= $to('repost', ['ID' => $post['id']]) ?>"
                                                        title="Репост">
                                                        <svg class="post__indicator-icon" width="19" height="17">
                                                            <use xlink:href="#icon-repost"></use>
                                                        </svg>
                                                        <span><?= $post['reposts_count'] ?></span>
                                                        <span class="visually-hidden">количество репостов</span>
                                                    </a>
                                                </div>
                                                <time
                                                    class="post__time"
                                                    datetime="<?= $post['date_add'] ?>"
                                                ><?= format_publication_date($post['date_add']) ?></time>
                                            </div>
                                            <?php
                                            $hashtags = $post_hashtags($post['id']);
                                            ?>
                                            <?php if (!empty($hashtags)): ?>
                                                <ul class="post__tags">
                                                    <?php foreach ($hashtags as $hashtag): ?>
                                                        <li>
                                                            <a href="<?= $to('search', ['search' => '%23' . $hashtag['title']]) ?>">#<?= $hashtag['title'] ?></a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </footer>
                                        <?php
                                        $comments_info = $post_comments($post['id']);
                                        $comments_count = $comments_info['length'];
                                        $comments = $comments_info['comments'];
                                        $is_show_comments = $check_show_comments($post['id']);
                                        ?>
                                        <?php if(!$is_show_comments AND $error_post_comment != $post['id']): ?>
                                            <div class="comments">
                                                <a
                                                    class="comments__button button"
                                                    href="<?= $to('profile', [
                                                        'user_id' => $user_id,
                                                        'post_id' => $post['id'],
                                                        'show_comments' => '',
                                                        'tab' => $active_tab
                                                    ]) ?>"
                                                >Показать комментарии</a>
                                            </div>
                                        <?php else: ?>
                                            <?php if(!empty($comments)): ?>
                                                <div class="comments">
                                                    <div class="comments__list-wrapper">
                                                        <ul class="comments__list">
                                                            <?php foreach ($comments as $comment): ?>
                                                                <li class="comments__item user">
                                                                    <div class="comments__avatar">
                                                                        <a class="user__avatar-link"
                                                                           href="<?= $to('profile', ['user_id' => $comment['user_id']]) ?>">
                                                                            <img class="comments__picture" src="<?= $comment['author_avatar'] ?>" alt="Аватар пользователя">
                                                                        </a>
                                                                    </div>
                                                                    <div class="comments__info">
                                                                        <div class="comments__name-wrapper">
                                                                            <a class="comments__user-name"
                                                                               href="<?= $to('profile', ['user_id' => $comment['user_id']]) ?>">
                                                                                <span><?= $comment['author_name'] ?? $comment['author_login'] ?></span>
                                                                            </a>
                                                                            <time
                                                                                class="comments__time"
                                                                                datetime="<?= $comment['date_add'] ?>"
                                                                            ><?= format_publication_date($comment['date_add']) ?></time>
                                                                        </div>
                                                                        <p class="comments__text">
                                                                            <?= $comment['message'] ?>
                                                                        </p>
                                                                    </div>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>

                                                        <?php if($comments_count > 2 && count($comments) <= 2): ?>
                                                            <a
                                                                class="comments__more-link"
                                                                href="<?= $to('profile', [
                                                                    'user_id' => $user_id,
                                                                    'post_id' => $post['id'],
                                                                    'show_comments' => '',
                                                                    'all_comments' => '',
                                                                    'tab' => $active_tab
                                                                ]) ?>">
                                                                <span>Показать все комментарии</span>
                                                                <sup class="comments__amount"><?= $comments_count - 2 ?></sup>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <form class="comments__form form" action="profile.php?user_id=<?= $user_id ?>" method="post">
                                                <input type="hidden" name="add_comment" value="true" />
                                                <input type="hidden" name="user" value="<?= $actual_user_id ?>" />
                                                <input type="hidden" name="post" value="<?= $post['id'] ?>" />
                                                <div class="comments__my-avatar">
                                                    <img class="comments__picture" src="<?= $actual_user_avatar ?>" alt="Аватар пользователя">
                                                </div>
                                                <div class="form__input-section <?= isset($errors['message']) ? 'form__input-section--error' : '' ?>">
                                            <textarea
                                                name="message"
                                                class="comments__textarea form__textarea form__input"
                                                placeholder="Ваш комментарий"
                                            ></textarea>
                                                    <label class="visually-hidden">Ваш комментарий</label>
                                                    <?php if(isset($errors) && isset($errors['message'])): ?>
                                                        <button class="form__error-button button" type="button">!</button>
                                                        <div class="form__error-text">
                                                            <h3 class="form__error-title">Ошибка валидации</h3>
                                                            <p class="form__error-desc"><?= $errors['message']['message'] ?></p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <button class="comments__submit button button--green" type="submit">Отправить</button>
                                            </form>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            <?php endif; ?>
                    </section>
                    <?php elseif($active_tab == 'likes'): ?>
                        <section class="profile__likes tabs__content tabs__content--active">
                        <h2 class="visually-hidden">Лайки</h2>
                            <?php if (empty($likes_list)): ?>
                                <p>Пока нет ни одного лайка</p>
                            <?php else: ?>
                                <ul class="profile__likes-list">
                                    <?php foreach ($likes_list as $like): ?>
                                        <li class="post-mini post-mini--<?= $like['content_type'] ?> post user">
                                            <div class="post-mini__user-info user__info">
                                                <div class="post-mini__avatar user__avatar">
                                                    <a class="user__avatar-link"
                                                        href="<?= $to('profile', ['user_id' => $like['user_id']]) ?>">
                                                        <img class="post-mini__picture user__picture" src="<?= $like['avatar'] ?>" alt="Аватар пользователя">
                                                    </a>
                                                </div>
                                                <div class="post-mini__name-wrapper user__name-wrapper">
                                                    <a class="post-mini__name user__name"
                                                       href="<?= $to('profile', ['user_id' => $like['user_id']]) ?>">
                                                        <span><?= $like['login'] ?></span>
                                                    </a>
                                                    <div class="post-mini__action">
                                                        <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                                                        <time class="post-mini__time user__additional"
                                                              datetime="<?= $like['date_add'] ?>"
                                                        ><?= format_publication_date($like['date_add']) ?></time>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="post-mini__preview">
                                                <a class="post-mini__link"
                                                   href="<?= $to('post', ['ID' => $like['post_id']]) ?>"
                                                   title="Перейти на публикацию">
                                                    <?php if($like['content_type'] == 'photo'): ?>
                                                        <div class="post-mini__image-wrapper">
                                                            <img class="post-mini__image" src="<?= $like['content'] ?>" width="109" height="109" alt="Превью публикации">
                                                        </div>
                                                        <span class="visually-hidden">Фото</span>
                                                    <?php elseif($like['content_type'] == 'text'): ?>
                                                        <span class="visually-hidden">Текст</span>
                                                        <svg class="post-mini__preview-icon" width="20" height="21">
                                                            <use xlink:href="#icon-filter-text"></use>
                                                        </svg>
                                                    <?php elseif($like['content_type'] == 'video'): ?>
                                                        <div class="post-mini__image-wrapper">
                                                            <img class="post-mini__image" src="<?= get_youtube_video_miniature($like['content']) ?>" width="109" height="109" alt="Превью публикации">
                                                            <span class="post-mini__play-big">
                                                        <svg class="post-mini__play-big-icon" width="12" height="13">
                                                          <use xlink:href="#icon-video-play-big"></use>
                                                        </svg>
                                                      </span>
                                                        </div>
                                                        <span class="visually-hidden">Видео</span>
                                                    <?php elseif($like['content_type'] == 'quote'): ?>
                                                        <span class="visually-hidden">Цитата</span>
                                                        <svg class="post-mini__preview-icon" width="21" height="20">
                                                            <use xlink:href="#icon-filter-quote"></use>
                                                        </svg>
                                                    <?php elseif($like['content_type'] == 'link'): ?>
                                                        <span class="visually-hidden">Ссылка</span>
                                                        <svg class="post-mini__preview-icon" width="21" height="18">
                                                            <use xlink:href="#icon-filter-link"></use>
                                                        </svg>
                                                    <?php endif; ?>
                                                </a>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                    </section>
                    <?php elseif($active_tab == 'subscriptions'): ?>
                        <section class="profile__subscriptions tabs__content tabs__content--active">
                        <h2 class="visually-hidden">Подписки</h2>

                            <?php if(empty($subscriptions_list)): ?>
                                <p>Пока нет ни одной подписки</p>
                            <?php else: ?>
                                <ul class="profile__subscriptions-list">
                                    <?php foreach ($subscriptions_list as $subscription): ?>
                                        <li class="post-mini post-mini--photo post user">
                                            <div class="post-mini__user-info user__info">
                                                <div class="post-mini__avatar user__avatar">
                                                    <a class="user__avatar-link"
                                                       href="<?= $to('profile', ['user_id' => $subscription['user_id']]) ?>">
                                                        <img class="post-mini__picture user__picture" src="<?= $subscription['avatar'] ?>" alt="Аватар пользователя">
                                                    </a>
                                                </div>
                                                <div class="post-mini__name-wrapper user__name-wrapper">
                                                    <a class="post-mini__name user__name"
                                                       href="<?= $to('profile', ['user_id' => $subscription['user_id']]) ?>">
                                                        <span><?= $subscription['login'] ?></span>
                                                    </a>
                                                    <time
                                                        class="post-mini__time user__additional"
                                                        datetime="<?= $subscription['user_date_add'] ?>"
                                                    ><?= format_register_date($subscription['user_date_add']) ?></time>
                                                </div>
                                            </div>
                                            <div class="post-mini__rating user__rating">
                                                <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
                                                    <span class="post-mini__rating-amount user__rating-amount"
                                                    ><?= $subscription['post_count'] ?></span>
                                                    <span class="post-mini__rating-text user__rating-text"
                                                    ><?= get_noun_plural_form($subscription['post_count'], 'публикация', 'публикации', 'публикаций') ?></span>
                                                </p>
                                                <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
                                                    <span class="post-mini__rating-amount user__rating-amount"
                                                    ><?= $subscription['subscription_count'] ?></span>
                                                    <span class="post-mini__rating-text user__rating-text"
                                                    ><?= get_noun_plural_form($subscription['subscription_count'], 'подписчик', 'подписчика', 'подписчиков') ?></span>
                                                </p>
                                            </div>
                                            <div class="post-mini__user-buttons user__buttons">
                                                <?php
                                                    $your_subscribe = $check_subs($subscription['user_id']);
                                                ?>

                                                <?php if($your_subscribe): ?>
                                                    <a
                                                        href="<?= $to('subscription', [
                                                            'user_id' => $subscription['user_id'],
                                                            'follower_id' => $actual_user_id,
                                                            'action' => 'remove'
                                                        ]) ?>"
                                                       class="post-mini__user-button user__button user__button--subscription button button--quartz">Отписаться</a>
                                                <?php else: ?>
                                                    <a
                                                        href="<?= $to('subscription', [
                                                            'user_id' => $subscription['user_id'],
                                                            'follower_id' => $actual_user_id,
                                                            'action' => 'add'
                                                        ]) ?>"
                                                       class="post-mini__user-button user__button user__button--subscription button button--main">Подписаться</a>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                    </section>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
