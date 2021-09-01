<main class="page__main page__main--publication">
    <div class="container">
        <?php foreach ($post as $post) : ?>
            <h1 class="page__title page__title--publication"><?= htmlspecialchars($post['title']) ?></h1>
            <section class="post-details">
                <h2 class="visually-hidden">Публикация</h2>
                <?php foreach ($categories as $category) {
                    if ($post['category_id'] == $category['id']) {
                        $postType = "post-" . $category['class_name'];
                    }
                }
                ?>
                <div class="post-details__wrapper <?= $postType ?>">
                    <div class="post-details__main-block post post--details">
                        <?php if ($postType == 'post-quote') {
                            require_once('templates/post-quote.php');
                        } elseif ($postType == 'post-text') {
                            require_once('templates/post-text.php');
                        } elseif ($postType == 'post-photo') {
                            require_once('templates/post-photo.php');
                        } elseif ($postType == 'post-video') {
                            require_once('templates/post-video.php');
                        } elseif ($postType == 'post-link') {
                            require_once('templates/post-link.php');
                        }
                        ?>
                        <div class="post__indicators">
                            <div class="post__buttons">
                                <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                    <svg class="post__indicator-icon" width="20" height="17">
                                        <use xlink:href="#icon-heart"></use>
                                    </svg>
                                    <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                        <use xlink:href="#icon-heart-active"></use>
                                    </svg>
                                    <span><?= $likesCount ?></span>
                                    <span class="visually-hidden">количество лайков</span>
                                </a>
                                <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="#icon-comment"></use>
                                    </svg>
                                    <span><?= $commentsCount ?></span>
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
                                    <img class="comments__picture" src="img/userpic-medium.jpg" alt="Аватар пользователя">
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
                                <ul class="comments__list">
                                    <li class="comments__item user">
                                        <div class="comments__avatar">
                                            <a class="user__avatar-link" href="#">
                                                <img class="comments__picture" src="img/userpic-larisa.jpg" alt="Аватар пользователя">
                                            </a>
                                        </div>
                                        <div class="comments__info">
                                            <div class="comments__name-wrapper">
                                                <a class="comments__user-name" href="#">
                                                    <span>Лариса Роговая</span>
                                                </a>
                                                <time class="comments__time" datetime="2019-03-20">1 ч назад</time>
                                            </div>
                                            <p class="comments__text">
                                                Красота!!!1!
                                            </p>
                                        </div>
                                    </li>
                                    <li class="comments__item user">
                                        <div class="comments__avatar">
                                            <a class="user__avatar-link" href="#">
                                                <img class="comments__picture" src="img/userpic-larisa.jpg" alt="Аватар пользователя">
                                            </a>
                                        </div>
                                        <div class="comments__info">
                                            <div class="comments__name-wrapper">
                                                <a class="comments__user-name" href="#">
                                                    <span>Лариса Роговая</span>
                                                </a>
                                                <time class="comments__time" datetime="2019-03-18">2 дня назад</time>
                                            </div>
                                            <p class="comments__text">
                                                Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                                <a class="comments__more-link" href="#">
                                    <span>Показать все комментарии</span>
                                    <sup class="comments__amount">45</sup>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php foreach ($authorData as $authorData) : ?>
                        <div class="post-details__user user">
                            <div class="post-details__user-info user__info">
                                <div class="post-details__avatar user__avatar">
                                    <a class="post-details__avatar-link user__avatar-link" href="#">
                                        <img class="post-details__picture user__picture" src="img/<?= $authorData['avatar'] ?>" alt="Аватар пользователя">
                                    </a>
                                </div>
                                <div class="post-details__name-wrapper user__name-wrapper">
                                    <a class="post-details__name user__name" href="#">
                                        <span><?= $authorData['full_name'] ?></span>
                                    </a>
                                    <time class="post-details__time user__time" datetime="<?= $authorData['date_registration'] ?>">5 лет на сайте</time>
                                </div>
                            </div>
                            <div class="post-details__rating user__rating">
                                <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                                    <span class="post-details__rating-amount user__rating-amount"><?= $subscribersCount ?></span>
                                    <span class="post-details__rating-text user__rating-text">подписчиков</span>
                                </p>
                                <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                                    <span class="post-details__rating-amount user__rating-amount"><?= $postsCount ?></span>
                                    <span class="post-details__rating-text user__rating-text">публикаций</span>
                                </p>
                            </div>
                            <div class="post-details__user-buttons user__buttons">
                                <button class="user__button user__button--subscription button button--main" type="button">Подписаться</button>
                                <a class="user__button user__button--writing button button--green" href="#">Сообщение</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
</main>
