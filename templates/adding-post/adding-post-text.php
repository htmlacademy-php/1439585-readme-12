<section class="adding-post__text tabs__content tabs__content--active">
    <h2 class="visually-hidden">Форма добавления текста</h2>
    <form class="adding-post__form form" action="add.php" method="post">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <input class="visually-hidden" type="text" name="post-type" value="text">
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
                <div class="adding-post__textarea-wrapper form__textarea-wrapper <?php if (isset($errorFields['post-text'])) echo 'form__input-section--error' ?>">
                    <label class="adding-post__label form__label" for="post-text">Текст поста <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <textarea class="adding-post__textarea form__textarea form__input" id="post-text" name="post-text"><?php if (!empty($_POST['post-text'])) echo $_POST['post-text'] ?></textarea>
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title">Заголовок сообщения</h3>
                            <p class="form__error-desc"><?php if (isset($errorFields['post-text'])) echo $errorFields['post-text'] ?></p>
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
            <a class="adding-post__close" href="#">Закрыть</a>
        </div>
    </form>
</section>
