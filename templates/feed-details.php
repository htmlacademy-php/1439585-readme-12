<main class="page__main page__main--feed">
    <div class="container">
        <h1 class="page__title page__title--feed">Моя лента</h1>
    </div>
    <div class="page__main-wrapper container">
        <section class="feed">
            <h2 class="visually-hidden">Лента</h2>
            <div class="feed__main-wrapper">
                <div class="feed__wrapper">
                    <?php foreach ($posts as $post) : ?>
                        <?php foreach ($categories as $category) {
                            if ($post['category_id'] == $category['id']) {
                                $postType = "post-" . $category['class_name'];
                            }
                        }
                        ?>
                        <article class="feed__post post <?= $postType ?>">
                            <header class="post__header post__author">
                                <a class="post__author-link" href="#" title="Автор">
                                    <div class="post__avatar-wrapper">
                                        <?php if (!empty($post['avatar'])): ?>
                                            <img class="post__author-avatar" src="<?= $post['avatar'] ?>"
                                             alt="Аватар пользователя" width="60" height="60">
                                        <?php endif; ?>
                                    </div>
                                    <div class="post__info">
                                        <b class="post__author-name"><?= $post['login'] ?></b>
                                        <?php $postDate = showDate($post['date_add']); ?>
                                        <time class="post__time" title=" <?= $postDate['title'] ?>"
                                              datetime="<?= $postDate['datetime'] ?>"><?= $postDate['relative_time'] . ' назад' ?></time>
                                    </div>
                                </a>
                            </header>
                            <div class="post__main">
                                <h2><a href="post.php?postId=<?= $post['post_id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                                <?php if ($postType == 'post-quote') : ?>
                                    <blockquote>
                                        <p>
                                            <?= htmlspecialchars($post['content']) ?>
                                        </p>
                                        <cite>
                                            <?php if (!empty($post['quote_author'])) {
                                                echo htmlspecialchars($post['quote_author']);
                                            } else {
                                                echo 'Неизвестный автор';
                                            } ?>
                                        </cite>
                                    </blockquote>
                                <?php elseif ($postType == 'post-link') : ?>
                                    <div class="post-link__wrapper">
                                        <a class="post-link__external"
                                           href="<?= correctSiteUrl(htmlspecialchars($post['website_link'])) ?>"
                                           title="Перейти по ссылке">
                                            <div class="post-link__icon-wrapper">
                                                <img
                                                    src="https://www.google.com/s2/favicons?domain=<?= $post['website_link'] ?>"
                                                    alt="Иконка">
                                            </div>
                                            <div class="post-link__info">
                                                <h3><?= htmlspecialchars($post['title']) ?></h3>
                                                <span><?= $post['website_link'] ?></span>
                                            </div>
                                            <svg class="post-link__arrow" width="11" height="16">
                                                <use xlink:href="#icon-arrow-right-ad"></use>
                                            </svg>
                                        </a>
                                    </div>
                                <?php elseif ($postType == 'post-photo') : ?>
                                    <div class="post-photo__image-wrapper">
                                        <img src="<?= $post['image_path'] ?>" alt="Фото от пользователя" width="760"
                                             height="396">
                                    </div>
                                <?php elseif ($postType == 'post-video') : ?>
                                    <div class="post-video__block">
                                        <div class="post-video__preview">
                                            <?= embed_youtube_video($post['video_link']); ?>
                                        </div>
                                    </div>
                                <?php elseif ($postType == 'post-text') : ?>
                                    <p><?= cutCardContent($post['content']) ?></p>
                                <?php endif; ?>
                            </div>
                            <footer class="post__footer post__indicators">
                                <div class="post__buttons">
                                    <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                        <svg class="post__indicator-icon" width="20" height="17">
                                            <use xlink:href="#icon-heart"></use>
                                        </svg>
                                        <svg class="post__indicator-icon post__indicator-icon--like-active" width="20"
                                             height="17">
                                            <use xlink:href="#icon-heart-active"></use>
                                        </svg>
                                        <span><?= $post['likes_count'] ?></span>
                                        <span class="visually-hidden">количество лайков</span>
                                    </a>
                                    <a class="post__indicator post__indicator--comments button" href="#"
                                       title="Комментарии">
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
                                        <span>0</span>
                                        <span class="visually-hidden">количество репостов</span>
                                    </a>
                                </div>
                            </footer>
                            <ul class="post__tags">
                                <?php foreach ($postHashtags as $postId => $hashtags): ?>
                                    <?php if (($post['post_id'] == $postId) && !empty($hashtags)): ?>
                                        <?php foreach ($hashtags as $tag): ?>
                                            <li><a href="#">#<?= ($tag) ?></a></li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <ul class="feed__filters filters">
                <?php $contentCategory = filter_input(INPUT_GET, 'category_id', FILTER_SANITIZE_NUMBER_INT) ?? 'all';
                ?>
                <li class="feed__filters-item filters__item">
                    <?php if ($contentCategory == 'all') {
                        $buttonActive = "filters__button--active";
                    } ?>
                    <a class="filters__button filters__button--all <?= $buttonActive ?>" href="feed.php">
                        <span>Все</span>
                    </a>
                </li>
                <?php foreach ($categories as $category) : ?>
                    <li class="feed__filters-item filters__item">
                        <?php $buttonActive = "button";
                        if ($contentCategory == $category['id']) {
                            $buttonActive = "filters__button--active";
                        } ?>
                        <a class="filters__button filters__button--<?= $category['class_name'] ?> <?= $buttonActive ?>"
                           href="feed.php?category_id=<?= $category['id'] ?>">
                            <span class="visually-hidden"><?= $category['name'] ?></span>
                            <svg class="filters__icon" width="22" height="18">
                                <use xlink:href="#icon-filter-<?= $category['class_name'] ?>"></use>
                            </svg>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <aside class="promo">
            <article class="promo__block promo__block--barbershop">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Все еще сидишь на окладе в офисе? Открой свой барбершоп по нашей франшизе!
                </p>
                <a class="promo__link" href="#">
                    Подробнее
                </a>
            </article>
            <article class="promo__block promo__block--technomart">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Товары будущего уже сегодня в онлайн-сторе Техномарт!
                </p>
                <a class="promo__link" href="#">
                    Перейти в магазин
                </a>
            </article>
            <article class="promo__block">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Здесь<br> могла быть<br> ваша реклама
                </p>
                <a class="promo__link" href="#">
                    Разместить
                </a>
            </article>
        </aside>
    </div>
</main>
