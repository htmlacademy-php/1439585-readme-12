<section class="adding-post__quote tabs__content tabs__content--active">
    <h2 class="visually-hidden">Форма добавления цитаты</h2>
    <form class="adding-post__form form" action="add.php" method="post">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <input class="visually-hidden" type="text" name="post-type" value="quote">
                <div class="adding-post__input-wrapper form__input-wrapper <?= isset($errorFields['heading']) ? 'form__input-section--error' : '' ?>">
                    <label class="adding-post__label form__label " for="heading">Заголовок <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="heading" type="text" name="heading" placeholder="Введите заголовок"
                               value="<?= $_POST['heading'] ?? '' ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text ">
                            <h3 class="form__error-title">Заголовок сообщения</h3>
                            <p class="form__error-desc"><?= $errorFields['heading'] ?? '' ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__textarea-wrapper <?= isset($errorFields['cite-text']) ? 'form__input-section--error' : '' ?> ">
                    <label class="adding-post__label form__label" for="cite-text">Текст цитаты <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <textarea class="adding-post__textarea adding-post__textarea--quote form__textarea form__input" id="cite-text" name="cite-text"
                                  placeholder="Текст цитаты"><?= $_POST['cite-text'] ?? '' ?></textarea>
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title">Заголовок сообщения</h3>
                            <p class="form__error-desc"><?= $errorFields['cite-text'] ?? '' ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__textarea-wrapper form__input-wrapper <?= isset($errorFields['quote-author']) ? 'form__input-section--error' : '' ?>">
                    <label class="adding-post__label form__label" for="quote-author">Автор <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="quote-author" type="text" name="quote-author"
                               value="<?= $_POST['quote-author'] ?? '' ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title">Заголовок сообщения</h3>
                            <p class="form__error-desc"><?= $errorFields['quote-author'] ?? '' ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="tags">Теги</label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="tags" type="text" name="tags" placeholder="Введите теги"
                               value="<?= $_POST['tags'] ?? '' ?>">
                    </div>
                </div>
            </div>
            <?= $redErrorBanner ?>
        </div>
        <div class="adding-post__buttons">
            <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
            <a class="adding-post__close" href="index.php">Закрыть</a>
        </div>
    </form>
</section>
