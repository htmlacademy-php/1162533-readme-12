<!DOCTYPE html>
<html lang="ru">
<?php
print(include_template('head.php', ['title' => $title]));
?>
<body class="page">
<header class="header">
    <div class="header__wrapper container">
        <div class="header__logo-wrapper">
            <a class="header__logo-link" href="/">
                <img class="header__logo" src="img/logo.svg" alt="Логотип readme" width="128" height="24">
            </a>
            <p class="header__topic">
                micro blogging
            </p>
        </div>
        <?php if (!isset($is_registration_page)) : ?>
            <form class="header__search-form form" action="search.php" method="get">
                <div class="header__search">
                    <label for="search-field" class="visually-hidden">Поиск</label>
                    <input
                            id="search-field"
                            class="header__search-input form__input"
                            name="search"
                            type="search"
                            value="<?= $search_query_text ?? null ?>">
                    <button class="header__search-button button" type="submit">
                        <svg class="header__search-icon" width="18" height="18">
                            <use xlink:href="/img/sprite.svg#icon-search"></use>
                        </svg>
                        <span class="visually-hidden">Начать поиск</span>
                    </button>
                </div>
            </form>
        <?php endif; ?>
        <div class="header__nav-wrapper">
            <nav class="header__nav">
                <?php if (!isset($is_registration_page) && $user['is_auth'] === 1) : ?>
                    <ul class="header__my-nav">
                        <li class="header__my-page header__my-page--popular">
                            <a class="header__page-link
                            <?= $page_name && $page_name === 'popular' ? 'header__page-link--active' : '' ?>"
                               href="popular.php" title="Популярный контент">
                                <span class="visually-hidden">Популярный контент</span>
                            </a>
                        </li>
                        <li class="header__my-page header__my-page--feed">
                            <a class="header__page-link
                            <?= $page_name && $page_name === 'feed' ? 'header__page-link--active' : '' ?>"
                               href="feed.php" title="Моя лента">
                                <span class="visually-hidden">Моя лента</span>
                            </a>
                        </li>
                        <li class="header__my-page header__my-page--messages">
                            <a class="header__page-link
                            <?= $page_name && $page_name === 'messages' ? 'header__page-link--active' : '' ?>"
                               href="messages.php" title="Личные сообщения">
                                <span class="visually-hidden">Личные сообщения</span>
                            </a>
                        </li>
                    </ul>
                    <ul class="header__user-nav">
                        <li class="header__profile">
                            <a class="header__profile-link" href="#">
                                <div class="header__avatar-wrapper">
                                    <?php if (!empty($user['avatar'])) : ?>
                                        <img class="header__profile-avatar" src="<?= $user['avatar'] ?>"
                                             alt="Аватар профиля">
                                    <?php endif; ?>
                                </div>
                                <div class="header__profile-name">
                                    <span><?= $user['user_name']; ?></span>
                                    <svg class="header__link-arrow" width="10" height="6">
                                        <use xlink:href="/img/sprite.svg#icon-arrow-right-ad"></use>
                                    </svg>
                                </div>
                            </a>
                            <div class="header__tooltip-wrapper">
                                <div class="header__profile-tooltip">
                                    <ul class="header__profile-nav">
                                        <li class="header__profile-nav-item">
                                            <a class="header__profile-nav-link" href="profile.php">
                          <span class="header__profile-nav-text">
                            Мой профиль
                          </span>
                                            </a>
                                        </li>
                                        <li class="header__profile-nav-item">
                                            <a class="header__profile-nav-link" href="messages.php">
                                              <span class="header__profile-nav-text">
                                                Сообщения
                                                  <?php if ($unreaded_dialogs_count > 0) : ?>
                                                      <i class="header__profile-indicator">
                                                          <?= $unreaded_dialogs_count ?></i>
                                                  <?php endif; ?>
                                              </span>
                                            </a>
                                        </li>

                                        <li class="header__profile-nav-item">
                                            <a class="header__profile-nav-link" href="logout.php">
                          <span class="header__profile-nav-text">
                            Выход
                          </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="header__post-button button button--transparent" href="add.php">Пост</a>
                        </li>
                    </ul>
                <?php else : ?>
                    <ul class="header__user-nav">
                        <li class="header__authorization">
                            <a class="header__user-button header__authorization-button button" href="/">Вход</a>
                        </li>
                        <li>
                            <a href="registration.php"
                               class="header__user-button <?= $is_registration_page
                                   ? 'header__user-button--active'
                                   : '' ?>  header__register-button button">Регистрация</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>


<?= $page_content ?>

<?php
print(include_template('footer.php'));
?>

</body>
</html>
