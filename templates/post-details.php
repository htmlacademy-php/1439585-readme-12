<main class="page__main page__main--publication">
    <div class="container">
        <h1 class="page__title page__title--publication"><?= htmlspecialchars($postData['title'] ?? '') ?></h1>
        <section class="post-details">
            <h2 class="visually-hidden">Публикация</h2>
            <?php $postType = isset($postData['category_name']) ? "post-" . $postData['category_name'] : '' ?>
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
                            <a class="post__indicator post__indicator--likes button" href="likes.php?post_id=<?= $postData['post_id'] ?? '' ?>" title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span><?= $postData['likes_count'] ?? '0' ?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span><?= $postData['comment_count'] ?? '0' ?></span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                            <a class="post__indicator post__indicator--repost button" href="repost.php?post_id=<?= $postData['post_id'] ?? '' ?>" title="Репост">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-repost"></use>
                                </svg>
                                <span><?= $postData['repost_count'] ?? '0' ?></span>
                                <span class="visually-hidden">количество репостов</span>
                            </a>
                        </div>
                        <span class="post__view"><?= $postData['show_count'] ?? '0' ?> просмотров</span>
                    </div>
                    <div class="comments">
                        <form class="comments__form form" action="post.php?post_id=<?= $postData['post_id'] ?? '' ?>" method="post">
                            <input class="visually-hidden" type="text" name="post-id" value="<?= $postData['post_id'] ?? '' ?>">
                            <div class="comments__my-avatar">
                                <?php if (!empty($userData['avatar'])): ?>
                                    <img class="comments__picture" src="<?= $userData['avatar'] ?>" width="40" height="40" alt="Аватар пользователя">
                                <?php endif; ?>
                            </div>
                            <?php
                            if (!empty($validationError))
                            {
                                $errorLine = 'form__input-section--error';
                            } else {
                                $errorLine = '';
                            }
                            ?>
                            <div class="form__input-section <?= $errorLine ?>">
                                <textarea class="comments__textarea form__textarea form__input" name="comment_content" placeholder="Ваш комментарий"></textarea>
                                <label class="visually-hidden">Ваш комментарий</label>
                                <?php if (!empty($validationError)): ?>
                                    <button class="form__error-button button" type="button">!</button>
                                    <div class="form__error-text">
                                        <h3 class="form__error-title">Ошибка валидации</h3>
                                        <p class="form__error-desc">Это поле обязательно к заполнению</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button class="comments__submit button button--green" type="submit">Отправить</button>
                        </form>
                        <div class="comments__list-wrapper">
                            <?php foreach ($postsComments as $comment): ?>
                                <ul class="comments__list">
                                    <li class="comments__item user">
                                        <div class="comments__avatar">
                                            <a class="user__avatar-link" href="profile.php?profile_id=<?= $comment['comment_author_id'] ?? '' ?>">
                                                <?php if (!empty($comment['avatar'])): ?>
                                                    <img class="comments__picture" src="<?= $comment['avatar'] ?>" width="40" height="40" alt="Аватар пользователя">
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="comments__info">
                                            <div class="comments__name-wrapper">
                                                <a class="comments__user-name" href="profile.php?profile_id=<?= $comment['comment_author_id'] ?? '' ?>">
                                                    <span><?= htmlspecialchars($comment['login'] ?? '' ) ?></span>
                                                </a>
                                                <?php $commentDate = isset($comment['comment_date']) ? showDate($comment['comment_date']) : '' ?>
                                                <time class="comments__time" datetime="<?= $commentDate['datetime'] ?? '' ?>">
                                                    <?= isset($commentDate['relative_time']) ?$commentDate['relative_time'] . ' назад' : '' ?></time>
                                            </div>
                                            <p class="comments__text">
                                                <?= htmlspecialchars($comment['comment'] ?? '') ?>
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="post-details__user user">
                    <div class="post-details__user-info user__info">
                        <div class="post-details__avatar user__avatar">
                            <a class="post-details__avatar-link user__avatar-link" href="profile.php?profile_id=<?= $postData['user_id'] ?? '' ?>">
                                <?php if (!empty($postData['avatar'])): ?>
                                    <img class="post-details__picture user__picture" src="<?= $postData['avatar'] ?>" width="60" height="60" alt="Аватар пользователя">
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="post-details__name-wrapper user__name-wrapper">
                            <a class="post-details__name user__name" href="profile.php?profile_id=<?= $postData['user_id'] ?? '' ?>">
                                <span><?= htmlspecialchars($postData['login'] ?? '') ?></span>
                            </a>
                            <?php $authorDateTime = isset($postData['date_registration']) ? showDate($postData['date_registration']) : '' ?>
                            <time class="post-details__time user__time" datetime="<?= $authorDateTime['datetime'] ?? '' ?>">
                                <?= isset($authorDateTime['relative_time']) ? $authorDateTime['relative_time'] . ' на сайте' : '' ?>
                            </time>
                        </div>
                    </div>
                    <div class="post-details__rating user__rating">
                        <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                            <span class="post-details__rating-amount user__rating-amount"><?= $postData['author_subscribers'] ?? '0' ?></span>
                            <span class="post-details__rating-text user__rating-text">
                                <?= isset($postData['author_subscribers']) ? showSubscribersCount($postData['author_subscribers']) : 'подписчиков' ?>
                            </span>
                        </p>
                        <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                            <span class="post-details__rating-amount user__rating-amount"><?= $postData['author_count_post'] ?? '0' ?></span>
                            <span class="post-details__rating-text user__rating-text">
                                <?= isset($postData['author_count_post']) ? showAuthorPostsCount($postData['author_count_post']) : 'постов' ?>
                            </span>
                        </p>
                    </div>
                    <?php
                    $button = 'button--main';
                    $buttonText = 'Подписаться';
                    $action = 'subscribe.php';

                    if ($postData['is_subscribe'] === 1) {
                        $button = 'button--quartz';
                        $buttonText = 'Отписаться';
                        $action = 'unsubscribe.php';
                    } ?>
                    <div class="post-details__user-buttons user__buttons">
                        <?php if ($postData['user_id'] !== $userData['id']): ?>
                            <form action="<?= $action ?>" method="get">
                                <input class="visually-hidden" type="text" name="author_id" value="<?= $postData['user_id'] ?? '' ?>">
                                <button style="margin-bottom: 10px; width: 319px;" class="user__button user__button--subscription button <?= $button ?>" type="submit"><?= $buttonText ?></button>
                            </form>
                            <?php if (isset($postData['is_subscribe']) && $postData['is_subscribe'] === 1): ?>
                                <a class="user__button user__button--writing button button--green" href="messages.php?user_id=<?= $postData['user_id'] ?? '' ?>">Сообщение</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
