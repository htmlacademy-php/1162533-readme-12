<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="video-url">Ссылка youtube <span
                class="form__input-required">*</span></label>
    <div class="form__input-section <?= !empty($errors['video-url']) ? 'form__input-section--error' : '' ?>">
        <input
                class="adding-post__input form__input"
                id="video-url"
                type="text"
                name="video-url"
                value="<?= get_post_val('video-url') ?>"
                placeholder="Введите ссылку">
        <?php if (!empty($errors) && !empty($errors['video-url'])) : ?>
            <button class="form__error-button button" type="button">!<span
                        class="visually-hidden">Информация об ошибке</span></button>
            <div class="form__error-text">
                <h3 class="form__error-title">Заголовок сообщения</h3>
                <p class="form__error-desc"><?= $errors['video-url']['message'] ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
