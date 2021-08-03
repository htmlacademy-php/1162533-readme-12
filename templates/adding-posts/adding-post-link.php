<div class="adding-post__textarea-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="post-link">Ссылка <span
                class="form__input-required">*</span></label>
    <div class="form__input-section <?= !empty($errors['post-link']) ? 'form__input-section--error' : '' ?>">
        <input
                class="adding-post__input form__input"
                id="post-link"
                type="text"
                name="post-link"
                value="<?= get_post_val('post-link') ?>">
        <?php if (!empty($errors) && !empty($errors['post-link'])): ?>
            <button class="form__error-button button" type="button">!<span
                        class="visually-hidden">Информация об ошибке</span></button>
            <div class="form__error-text">
                <h3 class="form__error-title">Заголовок сообщения</h3>
                <p class="form__error-desc"><?= $errors['post-link']['message'] ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
