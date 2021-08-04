<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="photo-url">Ссылка из интернета</label>
    <div class="form__input-section <?= !empty($errors) && $errors['photo-url'] ? 'form__input-section--error' : '' ?>">
        <input
                class="adding-post__input form__input"
                id="photo-url"
                type="text"
                name="photo-url"
                value="<?= get_post_val('photo-url') ?>"
                placeholder="Введите ссылку">
        <?php if (!empty($errors) && $errors['photo-url']) : ?>
            <button class="form__error-button button" type="button">!<span
                        class="visually-hidden">Информация об ошибке</span></button>
            <div class="form__error-text">
                <h3 class="form__error-title">Заголовок сообщения</h3>
                <p class="form__error-desc"><?= $errors['photo-url']['message'] ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
