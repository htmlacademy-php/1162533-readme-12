<main class="page__main page__main--messages">
    <h1 class="visually-hidden">Личные сообщения</h1>
    <section class="messages tabs">
        <h2 class="visually-hidden">Сообщения</h2>
        <div class="messages__contacts">
            <ul class="messages__contacts-list tabs__list">
                <?php if(!empty($message_users)): ?>
                    <?php foreach ($message_users as $message_user): ?>
                        <li class="messages__contacts-item <?= !$message_user['was_read'] && $message_user['sender_id'] !== $user['id'] ? 'messages__contacts-item--new' : '' ?>">
                            <a
                                class="messages__contacts-tab tabs__item
                                <?= $active_dialog === $message_user['user_id'] ? 'messages__contacts-tab--active tabs__item--active' : '' ?>"
                                href="/messages.php?user=<?= $message_user['user_id'] ?>">
                                <div class="messages__avatar-wrapper">
                                    <img class="messages__avatar" src="<?= $message_user['avatar'] ?>" alt="Аватар пользователя">
                                    <?php if($message_user['unreaded_messages_count']): ?>
                                        <i class="messages__indicator"><?= $message_user['unreaded_messages_count'] ?></i>
                                    <?php endif; ?>
                                </div>
                                <div class="messages__info">
                                     <span class="messages__contact-name">
                                        <?= $message_user['login'] ?>
                                     </span>
                                    <div class="messages__preview">
                                        <p class="messages__preview-text">
                                            <?= $message_user['content'] ?>
                                        </p>
                                        <time class="messages__preview-time" datetime="<?= $message_user['last_message'] ?>">
                                            <?= get_message_sent_time($message_user['last_message']); ?>
                                        </time>
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="messages__chat">
            <?php if($active_dialog): ?>
                <div class="messages__chat-wrapper">
                    <?php if(!empty($message_list)): ?>
                        <ul class="messages__list tabs__content tabs__content--active">
                            <?php foreach ($message_list as $message): ?>
                                <li class="messages__item <?= $message['sender_id'] == $user['id'] ? 'messages__item--my' : '' ?>">
                                    <div class="messages__info-wrapper">
                                        <div class="messages__item-avatar">
                                            <a class="messages__author-link" href="profile.php?user_id=<?= $message['sender_id'] ?>">
                                                <img class="messages__avatar" src="<?= $message['avatar'] ?>" alt="Аватар пользователя">
                                            </a>
                                        </div>
                                        <div class="messages__item-info">
                                            <a class="messages__author" href="#">
                                                <?= $message['login'] ?>
                                            </a>
                                            <time class="messages__time" datetime="<?= $message['date_add'] ?>">
                                                <?= format_publication_date($message['date_add']) ?>
                                            </time>
                                        </div>
                                    </div>

                                    <p class="messages__text">
                                        <?= $message['content'] ?>
                                    </p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Пока нет ни одного сообщения. Начните переписку первым!</p>
                    <?php endif; ?>
                </div>
                <div class="comments">
                    <form class="comments__form form" action="messages.php?user=<?= $active_dialog ?>" method="post">
                        <input type="hidden" name="recipient_id" value="<?= $active_dialog ?>" />
                        <div class="comments__my-avatar">
                            <img class="comments__picture" src="<?= $user['avatar'] ?>" alt="Аватар пользователя">
                        </div>
                        <div class="form__input-section <?= !empty($errors) && $errors['message'] ? 'form__input-section--error' : '' ?>">
                    <textarea class="comments__textarea form__textarea form__input"
                              name="message"
                              placeholder="Ваше сообщение"><?= get_post_val('message') ?></textarea>
                            <label class="visually-hidden">Ваше сообщение</label>

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
                </div>
            <?php else: ?>
            <p>Выберите диалог, чтобы начать переписку.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
