<section class="page__main page__main--publication">
    <div class="container">
        <h1 class="page__title page__title--publication"><?= $post['title'] ?></h1>
        <section class="post-details">
            <h2 class="visually-hidden">Публикация</h2>
            <div class="post-details__wrapper post-photo">
                <div class="post-details__main-block post post--details">
                    <?php if($post['content_type_name'] == 'post-text'): ?>
                        <?php
                            print(include_template('posts/post-text.php', [
                                'text' => $post['content']
                            ]))
                        ?>

                    <?php elseif ($post['content_type_name'] == 'post-quote'): ?>
                        <?php
                            print(include_template('posts/post-quote.php', [
                                'text' => $post['content'],
                                'author' => $post['author'],
                            ]))
                        ?>

                    <?php elseif ($post['content_type_name'] == 'post-link'): ?>
                        <?php
                            print(include_template('posts/post-link.php', [
                                'title' => $post['title'],
                                'url' => $post['content'],
                            ]))
                        ?>

                    <?php elseif ($post['content_type_name'] == 'post-photo'): ?>
                        <?php
                        print(include_template('posts/post-image.php', [
                            'img_url' => $post['content'],
                        ]))
                        ?>

                    <?php elseif ($post['content_type_name'] == 'post-video'): ?>
                        <?php
                        print(include_template('posts/post-video.php', [
                            'youtube_url' => $post['content'],
                        ]))
                        ?>
                    <? endif; ?>

                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button"
                               href="<?= $to('likes', [
                                   'post_id' => $post['id'],
                                   'user_id' => $user['id']
                               ]) ?>"
                               title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#<?= $check_is_liked_post($post['id']) ?>"></use>
                                </svg>
                                <span><?= $post['likes_count'] ?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
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
                        <span class="post__view"><?= get_text_count_shown($post['shown_count']) ?></span>
                    </div>
                    <?php if (!empty($hashtags)): ?>
                        <ul class="post__tags">
                            <?php foreach ($hashtags as $key => $hashtag): ?>
                                <li>
                                    <a
                                        href="<?= $to('search', ['search' => '%23' . $hashtag['title']]) ?>"
                                    >#<?= $hashtag['title'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="comments">
                        <form class="comments__form form" action="<?= $to('post', ['ID' => $post_id]) ?>" method="post">
                            <div class="comments__my-avatar">
                                <img class="comments__picture" src="<?= $user['avatar'] ?>" alt="Аватар пользователя">
                            </div>
                            <div class="form__input-section <?= !empty($errors) && $errors['message'] ? 'form__input-section--error' : '' ?>">
                                <input type="hidden" name="user" value="<?= $user['id'] ?>" />
                                <input type="hidden" name="post" value="<?= $post_id ?>" />
                                <textarea class="comments__textarea form__textarea form__input" name="message" placeholder="Ваш комментарий"></textarea>
                                <label class="visually-hidden">Ваш комментарий</label>

                                <?php if(!empty($errors) && $errors['message']): ?>
                                    <button class="form__error-button button" type="button">!</button>
                                    <div class="form__error-text">
                                        <h3 class="form__error-title">Ошибка валидации</h3>
                                        <p class="form__error-desc"><?= $errors['message']['message'] ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button class="comments__submit button button--green" type="submit">Отправить</button>
                        </form>
                        <?php if (!empty($comments)): ?>
                            <div class="comments__list-wrapper">
                                <ul class="comments__list">
                                    <?php foreach ($comments as $key => $comment): ?>
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
                                                    <time class="comments__time" datetime="<?= $comment['date_add'] ?>"><?= format_publication_date($comment['date_add']) ?></time>
                                                </div>
                                                <p class="comments__text">
                                                    <?= $comment['message'] ?>
                                                </p>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if($comments_length > 2 && count($comments) <= 2): ?>
                                    <a
                                        class="comments__more-link"
                                        href="<?= $to('post', [
                                            'ID' => $post_id,
                                            'show_comments' => ''
                                        ]) ?>" >
                                        <span>Показать все комментарии</span>
                                        <sup class="comments__amount"><?= $comments_length - 2 ?></sup>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="post-details__user user">
                    <div class="post-details__user-info user__info">
                        <div class="post-details__avatar user__avatar">
                            <a class="post-details__avatar-link user__avatar-link" href="<?= $to('profile', ['user_id' => $author_info['id']]) ?>">
                                <img class="post-details__picture user__picture" src="<?= $author_info['avatar'] ?>" alt="Аватар пользователя">
                            </a>
                        </div>
                        <div class="post-details__name-wrapper user__name-wrapper">
                            <a class="post-details__name user__name" href="<?= $to('profile', ['user_id' => $author_info['id']]) ?>">
                                <span><?= $author_info['user_name'] ?? $author_info['login'] ?></span>
                            </a>
                            <time class="post-details__time user__time" datetime="2014-03-20"><?= format_register_date($author_info['date_add']) ?></time>
                        </div>
                    </div>
                    <div class="post-details__rating user__rating">
                        <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                            <span class="post-details__rating-amount user__rating-amount"><?= $author_info['count_followers'] ?></span>
                            <span class="post-details__rating-text user__rating-text"><?= get_text_count_followers($author_info['count_followers']) ?></span>
                        </p>
                        <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                            <span class="post-details__rating-amount user__rating-amount"><?= $author_info['count_posts'] ?></span>
                            <span class="post-details__rating-text user__rating-text"><?= get_text_count_publications($author_info['count_posts']) ?></span>
                        </p>
                    </div>
                    <?php if($author_info['id'] != $user['id']): ?>
                        <div class="post-details__user-buttons user__buttons">
                            <?php
                            $your_subscribe = $check_subs($author_info['id']);
                            ?>

                            <?php if($your_subscribe): ?>
                                <a
                                    href="<?= $to('subscription', [
                                        'user_id' => $author_info['id'],
                                        'follower_id' => $user['id'],
                                        'action' => 'remove'
                                    ]) ?>"
                                    class="user__button user__button--subscription button button--quartz">Отписаться</a>
                            <?php else: ?>
                                <a
                                    href="<?= $to('subscription', [
                                        'user_id' => $author_info['id'],
                                        'follower_id' => $user['id'],
                                        'action' => 'add'
                                    ]) ?>"
                                    class="user__button user__button--subscription button button--main">Подписаться</a>
                            <?php endif; ?>
                            <a class="user__button user__button--writing button button--green" href="<?= $to('messages', ['user' => $author_info['id']]) ?>">Сообщение</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</section>
