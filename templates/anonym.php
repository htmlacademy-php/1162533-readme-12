<!DOCTYPE html>
<html lang="ru">
<?php
print(include_template('head.php', ['title' => $title]));
?>

<body class="page page--main">
<header class="header page__header">
    <div class="header__wrapper page__header-wrapper container">
        <div class="header__logo-wrapper page__logo-wrapper">
            <a class="header__logo-link header__logo-link--active">
                <img class="header__logo" src="img/logo.svg" alt="Логотип readme" width="172" height="32">
            </a>
            <p class="header__topic page__header-topic">
                micro blogging
            </p>
        </div>
        <div class="header__nav-wrapper">
            <nav class="header__nav">
                <p class="header__register-slogan">
                    Еще нет аккаунта?
                </p>
                <ul class="header__user-nav">
                    <li>
                        <a class="header__user-button header__register-button button button--transparent"
                           href="registration.php">Регистрация</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>

<main>
    <h1 class="visually-hidden">Главная страница сайта по созданию микроблога readme</h1>
    <div class="page__main-wrapper page__main-wrapper--intro container">
        <section class="intro">
            <h2 class="visually-hidden">Наши преимущества</h2>
            <b class="intro__slogan">Блог, каким<br> он должен быть</b>
            <ul class="intro__advantages-list">
                <li class="intro__advantage intro__advantage--ease">
                    <p class="intro__advantage-text">
                        Есть все необходимое для&nbsp;простоты публикации
                    </p>
                </li>
                <li class="intro__advantage intro__advantage--no-excess">
                    <p class="intro__advantage-text">
                        Нет ничего лишнего, отвлекающего от сути
                    </p>
                </li>
            </ul>
        </section>
        <section class="authorization">
            <h2 class="visually-hidden">Авторизация</h2>
            <form class="authorization__form form" action="index.php" method="post">
                <div class="authorization__input-wrapper form__input-wrapper">
                    <div class="form__input-section <?= !empty($errors) && !empty($errors['login'])
                        ? 'form__input-section--error'
                        : '' ?>">
                        <input
                                class="authorization__input authorization__input--login form__input"
                                type="text"
                                name="login"
                                value="<?= get_post_val('login') ?>"
                                placeholder="Логин">
                        <svg class="form__input-icon" width="19" height="18">
                            <use xlink:href="/img/sprite.svg#icon-input-user"></use>
                        </svg>
                        <label class="visually-hidden">Логин</label>
                    </div>
                    <?php if (!empty($errors) && !empty($errors['login'])) : ?>
                        <span class="form__error-label form__error-label--login">
                            <?= $errors['login']['message'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="authorization__input-wrapper form__input-wrapper">
                    <div class="form__input-section <?= !empty($errors) && !empty($errors['password'])
                        ? 'form__input-section--error'
                        : '' ?>">
                        <input
                                class="authorization__input authorization__input--password form__input"
                                type="password"
                                name="password"
                                value="<?= get_post_val('password') ?>"
                                placeholder="Пароль">
                        <svg class="form__input-icon" width="16" height="20">
                            <use xlink:href="/img/sprite.svg#icon-input-password"></use>
                        </svg>
                        <label class="visually-hidden <?= !empty($errors) && !empty($errors['password'])
                            ? 'form__input-section--error'
                            : '' ?>">Пароль</label>
                    </div>
                    <?php if (!empty($errors) && !empty($errors['password'])) : ?>
                        <span class="form__error-label"><?= $errors['password']['message'] ?></span>
                    <?php endif; ?>

                </div>
                <a class="authorization__recovery" href="#">Восстановить пароль</a>
                <button class="authorization__submit button button--main" type="submit">Войти</button>
            </form>
        </section>
    </div>
</main>

<?php
print(include_template('footer.php', ['class' => 'footer--main']))
?>

</body>
</html>
