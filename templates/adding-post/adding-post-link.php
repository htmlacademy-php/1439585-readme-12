<section class="adding-post__link tabs__content tabs__content--active ?>">
    <h2 class="visually-hidden">Форма добавления ссылки</h2>
    <form class="adding-post__form form" action="add.php" method="post">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <input class="visually-hidden" type="text" name="post-type" value="link">
                <div class="adding-post__input-wrapper form__input-wrapper <?php if (isset($errorFields['heading'])) echo 'form__input-section--error'; ?>">
                    <label class="adding-post__label form__label " for="heading">Заголовок <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="heading" type="text" name="heading" placeholder="Введите заголовок" value="<?php if (isset($_POST['heading'])) echo $_POST['heading'] ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text ">
                            <h3 class="form__error-title">Заголовок сообщения</h3>
                            <p class="form__error-desc"><?php if (isset($errorFields['heading'])) echo $errorFields['heading'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__textarea-wrapper form__input-wrapper <?php if (isset($errorFields['post-link'])) echo 'form__input-section--error' ?>">
                    <label class="adding-post__label form__label" for="post-link">Ссылка <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="post-link" type="text" name="post-link" value="<?php if (isset($_POST['post-link'])) echo $_POST['post-link'] ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title">Заголовок сообщения</h3>
                            <p class="form__error-desc"><?php if (isset($errorFields['post-link'])) echo $errorFields['post-link'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="tags">Теги</label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="tags" type="text" name="tags" placeholder="Введите теги" value="<?php if (isset($_POST['tags'])) echo $_POST['tags'] ?>">
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
