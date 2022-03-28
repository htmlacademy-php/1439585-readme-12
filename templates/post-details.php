<main class="page__main page__main--publication">
    <div class="container">
        <?php foreach ($postContent as $post) : ?>
            <?php foreach ($postAuthorData as $author) : ?>
                <h1 class="page__title page__title--publication"><?= htmlspecialchars($post['title']) ?></h1>
                <section class="post-details">
                    <h2 class="visually-hidden">Публикация</h2>
                    <?php $postType = "post-" . $post['category_name']; ?>
                    <div class="post-details__wrapper <?= $postType ?>">
                        <div class="post-details__main-block post post--details">
                            <?php
                            $filename = $postType . '.php';
                            $filePath = "templates/{$filename}";
                            if (file_exists($filePath)) {
                                require_once($filePath);
                            } else {
                                echo "Sorry, there is not such file :(";
                            } ?>
                            <div class="post__indicators">
                                <div class="post__buttons">
                                    <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                        <svg class="post__indicator-icon" width="20" height="17">
                                            <use xlink:href="#icon-heart"></use>
                                        </svg>
                                        <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                            <use xlink:href="#icon-heart-active"></use>
                                        </svg>
                                        <span><?= $post['likes_count'] ?></span>
                                        <span class="visually-hidden">количество лайков</span>
                                    </a>
                                    <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-comment"></use>
                                        </svg>
                                        <span><?= $post['comment_count'] ?></span>
                                        <span class="visually-hidden">количество комментариев</span>
                                    </a>
                                    <a class="post__indicator post__indicator--repost button" href="#" title="Репост">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-repost"></use>
                                        </svg>
                                        <span>5</span>
                                        <span class="visually-hidden">количество репостов</span>
                                    </a>
                                </div>
                                <span class="post__view"><?= $post['show_count'] ?> просмотров</span>
                            </div>
                            <div class="comments">
                                <form class="comments__form form" action="#" method="post">
                                    <div class="comments__my-avatar">
                                        <?php if (!empty($_SESSION['user']['avatar'])): ?>
                                            <img class="comments__picture" src="<?= $_SESSION['user']['avatar'] ?>" width="40" height="40" alt="Аватар пользователя">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form__input-section form__input-section--error">
                                        <textarea class="comments__textarea form__textarea form__input" placeholder="Ваш комментарий"></textarea>
                                        <label class="visually-hidden">Ваш комментарий</label>
                                        <button class="form__error-button button" type="button">!</button>
                                        <div class="form__error-text">
                                            <h3 class="form__error-title">Ошибка валидации</h3>
                                            <p class="form__error-desc">Это поле обязательно к заполнению</p>
                                        </div>
                                    </div>
                                    <button class="comments__submit button button--green" type="submit">Отправить</button>
                                </form>
                                <div class="comments__list-wrapper">
                                    <?php $i = 0 ?>
                                    <?php foreach ($postsComments as $comment) : ?>
                                        <?php $i++ ?>
                                        <ul class="comments__list">
                                            <li class="comments__item user">
                                                <div class="comments__avatar">
                                                    <a class="user__avatar-link" href="#">
                                                        <?php if (!empty($comment['avatar'])): ?>
                                                            <img class="comments__picture" src="<?= $comment['avatar'] ?>" width="40" height="40" alt="Аватар пользователя">
                                                        <?php endif; ?>
                                                    </a>
                                                </div>
                                                <div class="comments__info">
                                                    <div class="comments__name-wrapper">
                                                        <a class="comments__user-name" href="#">
                                                            <span><?= htmlspecialchars($comment['login']) ?></span>
                                                        </a>
                                                        <?php $commentDate = showDate($comment['comment_date']); ?>
                                                        <time class="comments__time" datetime="<?= $commentDate['datetime'] ?>"><?= $commentDate['relative_time'] . ' назад' ?></time>
                                                    </div>
                                                    <p class="comments__text">
                                                        <?= $comment['comment'] ?>
                                                    </p>
                                                </div>
                                            </li>
                                        </ul>
                                        <?php if ($i == 2) { break; } ?>
                                    <?php endforeach; ?>
                                    <?php if ($post['comment_count'] > 2) : ?>
                                        <a class="comments__more-link" href="#">
                                            <span>Показать все комментарии</span>
                                            <sup class="comments__amount"><?= (int)$post['comment_count'] - 2 ?></sup>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="post-details__user user">
                            <div class="post-details__user-info user__info">
                                <div class="post-details__avatar user__avatar">
                                    <a class="post-details__avatar-link user__avatar-link" href="#">
                                        <?php if (!empty($postAuthorData[0]['avatar'])): ?>
                                            <img class="post-details__picture user__picture" src="<?= $postAuthorData[0]['avatar'] ?>" width="60" height="60" alt="Аватар пользователя">
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div class="post-details__name-wrapper user__name-wrapper">
                                    <a class="post-details__name user__name" href="#">
                                        <span><?= $postAuthorData[0]['login'] ?></span>
                                    </a>
                                    <?php $userDate = showDate($postAuthorData[0]['date_registration']); ?>
                                    <time class="post-details__time user__time" datetime="<?= $userDate['datetime'] ?>"><?= $userDate['relative_time'] . ' на сайте' ?> </time>
                                </div>
                            </div>
                            <div class="post-details__rating user__rating">

                                <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                                    <span class="post-details__rating-amount user__rating-amount"><?= $postAuthorData[0]['subscribers'] ?></span>
                                    <span class="post-details__rating-text user__rating-text"><?= showSubscribersCount($postAuthorData[0]['subscribers']) ?></span>
                                </p>
                                <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                                    <span class="post-details__rating-amount user__rating-amount"><?= $postAuthorData[0]['count_posts'] ?></span>
                                    <span class="post-details__rating-text user__rating-text"><?= showAuthorPostsCount($postAuthorData[0]['count_posts']) ?></span>
                                </p>

                            </div>
                            <div class="post-details__user-buttons user__buttons">
                                <button class="user__button user__button--subscription button button--main" type="button">Подписаться</button>
                                <a class="user__button user__button--writing button button--green" href="#">Сообщение</a>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
</main>
