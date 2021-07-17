<main class="page__main page__main--registration">
    <div class="container">
        <h1 class="page__title page__title--registration">Регистрация</h1>
    </div>
    <section class="registration container">
        <h2 class="visually-hidden">Форма регистрации</h2>
        <form class="registration__form form" action="registration.php" method="post" enctype="multipart/form-data">
            <div class="form__text-inputs-wrapper">
                <div class="form__text-inputs">
                    <div class="registration__input-wrapper form__input-wrapper">
                        <label class="registration__label form__label" for="registration-email">Электронная почта <span class="form__input-required">*</span></label>
                        <div class="form__input-section <?= !empty($errors['registration-email']) ? 'form__input-section--error' : '' ?>">
                            <input
                                class="registration__input form__input"
                                id="registration-email"
                                type="text"
                                name="registration-email"
                                value="<?= get_post_val('registration-email') ?>"
                                placeholder="Укажите эл.почту">
                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                            <?php if(!empty($errors) && !empty($errors['registration-email'])): ?>
                                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                <div class="form__error-text">
                                    <h3 class="form__error-title">Заголовок сообщения</h3>
                                    <p class="form__error-desc"><?= $errors['registration-email']['message'] ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="registration__input-wrapper form__input-wrapper">
                        <label class="registration__label form__label" for="registration-login">Логин <span class="form__input-required">*</span></label>
                        <div class="form__input-section <?= !empty($errors['registration-login']) ? 'form__input-section--error' : '' ?>">
                            <input
                                class="registration__input form__input"
                                id="registration-login"
                                type="text"
                                name="registration-login"
                                value="<?= get_post_val('registration-login') ?>"
                                placeholder="Укажите логин">
                            <?php if(!empty($errors) && !empty($errors['registration-login'])): ?>
                                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                <div class="form__error-text">
                                    <h3 class="form__error-title">Заголовок сообщения</h3>
                                    <p class="form__error-desc"><?= $errors['registration-login']['message'] ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="registration__input-wrapper form__input-wrapper">
                        <label class="registration__label form__label" for="registration-password">Пароль<span class="form__input-required">*</span></label>
                        <div class="form__input-section <?= !empty($errors['registration-password']) ? 'form__input-section--error' : '' ?>">
                            <input
                                class="registration__input form__input"
                                id="registration-password"
                                type="password"
                                name="registration-password"
                                value="<?= get_post_val('registration-password') ?>"
                                placeholder="Придумайте пароль">
                            <?php if(!empty($errors) && !empty($errors['registration-password'])): ?>
                                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                <div class="form__error-text">
                                    <h3 class="form__error-title">Заголовок сообщения</h3>
                                    <p class="form__error-desc"><?= $errors['registration-password']['message'] ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="registration__input-wrapper form__input-wrapper">
                        <label class="registration__label form__label" for="registration-password-repeat">Повтор пароля<span class="form__input-required">*</span></label>
                        <div class="form__input-section <?= !empty($errors['registration-password-repeat']) ? 'form__input-section--error' : '' ?>">
                            <input
                                class="registration__input form__input"
                                id="registration-password-repeat"
                                type="password"
                                name="registration-password-repeat"
                                value="<?= get_post_val('registration-password-repeat') ?>"
                                placeholder="Повторите пароль">
                            <?php if(!empty($errors) && !empty($errors['registration-password-repeat'])): ?>
                                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                <div class="form__error-text">
                                    <h3 class="form__error-title">Заголовок сообщения</h3>
                                    <p class="form__error-desc"><?= $errors['registration-password-repeat']['message'] ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if(!empty($errors)): ?>
                    <div class="form__invalid-block">
                        <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                        <ul class="form__invalid-list">
                            <?php foreach ($errors as $error): ?>
                                <li class="form__invalid-item"><?= $error['title'] ?>. <?= $error['message'] ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="registration__input-file-container form__input-container form__input-container--file">
                <div class="registration__input-file-wrapper form__input-file-wrapper">
                    <div class="registration__file-zone form__file-zone dropzone">
                        <input class="registration__input-file form__input-file" id="userpic-file" type="file" name="userpic-file" title=" ">
                        <div class="form__file-zone-text">
                            <span>Перетащите фото сюда</span>
                        </div>
                    </div>
                </div>
            </div>
            <button class="registration__submit button button--main" type="submit">Отправить</button>
        </form>
    </section>
</main>
