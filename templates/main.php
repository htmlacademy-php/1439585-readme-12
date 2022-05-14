<section class="page__main page__main--popular">
    <div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    <div class="popular container">
        <?php $contentCategory = filter_input(INPUT_GET, 'category_id', FILTER_SANITIZE_NUMBER_INT) ?? 'popular'; ?>
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <?php
                    $sortingList = [
                        'relevance' => 'Популярность',
                        'rating' => 'Лайки',
                        'date_add' => 'Дата'
                    ]; ?>
                    <?php foreach ($sortingList as $keySorting => $sorting): ?>
                        <?php
                        $class = '';
                        $classItem = '';
                        if ($keySorting === 'relevance' & $getSortBy === '') {
                            $class = 'sorting__link--active';
                            $classItem = 'sorting__item--popular';
                        } elseif ($keySorting === $getSortBy) {
                            $class = 'sorting__link--active';
                        }

                        $sortOrder = 'desc';
                        if ($getSortBy === $keySorting) {
                            $sortOrder = ($getSortOrder === 'asc') ? 'desc' : 'asc';
                            $class .= ($sortOrder === 'asc') ? '' : ' sorting__link--reverse';
                        }

                        if ($contentCategory !== 'popular') {
                            $url = "popular.php?" . 'category_id=' . $contentCategory . "&by=" . $keySorting . "&sorting=" . $sortOrder;
                        } else {
                            $url = "popular.php?" . "by=" . $keySorting . "&sorting=" . $sortOrder;
                        }
                        ?>
                        <li class="sorting__item <?= $classItem ?>">
                            <a class="sorting__link <?= $class ?>" href="<?= $url ?>">
                                <span><?= $sorting ?></span>
                                <svg class="sorting__icon" width="10" height="12">
                                    <use xlink:href="#icon-sort"></use>
                                </svg>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="popular__filters filters">
                <b class="popular__filters-caption filters__caption">Тип контента:</b>
                <ul class="popular__filters-list filters__list">
                    <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                        <?php if ($contentCategory == 'popular') {
                            $buttonActive = "filters__button--active";
                        } else {
                            $buttonActive = "";
                        } ?>
                        <a class="filters__button filters__button--ellipse filters__button--all <?= $buttonActive ?>" href="popular.php">
                            <span>Все</span>
                        </a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                        <li class="popular__filters-item filters__item">
                            <?php $buttonActive = "button";
                            if ($contentCategory == $category['id']) {
                                $buttonActive = "filters__button--active";
                            } ?>
                            <a class="filters__button filters__button--<?= $category['class_name'] ?> <?= $buttonActive ?>" href="popular.php?category_id=<?= $category['id'] ?>">
                                <span class="visually-hidden"><?= $category['name'] ?></span>
                                <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-<?= $category['class_name'] ?>"></use>
                                </svg>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="popular__posts">
            <?php foreach ($cards as $card): ?>
                <?php foreach ($categories as $category) {
                    if ($card['category_id'] == $category['id']) {
                        $postType = "post-" . $category['class_name'];
                    }
                }
                ?>
                <article class="popular__post post <?= $postType ?>">
                    <header class="post__header">
                        <h2><a href="post.php?post_id=<?= $card['post_id'] ?>"><?= htmlspecialchars($card['title']) ?></a></h2>
                    </header>
                    <div class="post__main">
                        <?php if ($postType === 'post-quote'): ?>
                            <blockquote>
                                <p>
                                    <?= htmlspecialchars($card['content']) ?>
                                </p>
                                <cite>
                                    <?php if (!empty($card['quote_author'])) {
                                        echo htmlspecialchars($card['quote_author']);
                                    } else echo 'Неизвестный автор'; ?>
                                </cite>
                            </blockquote>
                        <?php elseif ($postType == 'post-link'): ?>
                            <div class="post-link__wrapper">
                                <a class="post-link__external" href="<?= correctSiteUrl(htmlspecialchars($card['website_link'])) ?>" title="Перейти по ссылке">
                                    <div class="post-link__info-wrapper">
                                        <div class="post-link__icon-wrapper">
                                            <img src="https://www.google.com/s2/favicons?domain=<?= $card['website_link'] ?>" alt="Иконка">
                                        </div>
                                        <div class="post-link__info">
                                            <h3><?= htmlspecialchars($card['title']) ?></h3>
                                        </div>
                                    </div>
                                    <span><?= cutPreviewLink($card['website_link']) ?></span>
                                </a>
                            </div>
                        <?php elseif ($postType == 'post-photo'): ?>
                            <div class="post-photo__image-wrapper">
                                <img src="<?= $card['image_path'] ?>" alt="Фото от пользователя" width="360" height="240">
                            </div>
                        <?php elseif ($postType == 'post-video'): ?>
                            <div class="post-video__block">
                                <div class="post-video__preview">
                                    <?= embed_youtube_cover($card['video_link']) ?>
                                </div>
                                <a href="post.php?postId=<?= $card['post_id'] ?>" class="post-video__play-big button">
                                    <svg class="post-video__play-big-icon" width="14" height="14">
                                        <use xlink:href="#icon-video-play-big"></use>
                                    </svg>
                                    <span class="visually-hidden">Запустить проигрыватель</span>
                                </a>
                            </div>
                        <?php elseif ($postType == 'post-text'): ?>
                            <p><?= cutCardContent($card['content'], $card['post_id']) ?></p>
                        <?php endif; ?>
                    </div>
                    <footer class="post__footer">
                        <div class="post__author">
                            <a class="post__author-link" href="profile.php?profile_id=<?= $card['user_id'] ?>" title="Автор">
                                <div class="post__avatar-wrapper">
                                    <?php if (!empty($card['avatar'])): ?>
                                        <img class="post__author-avatar" src="<?= $card['avatar'] ?>" width="40" height="40" alt="Аватар пользователя">
                                    <?php endif; ?>
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name"><?= htmlspecialchars($card['login']) ?></b>
                                    <?php $postDate = showDate($card['date_add']); ?>
                                    <time class="post__time" title="<?= $postDate['title'] ?>" datetime="<?= $postDate['datetime'] ?>">
                                        <?= $postDate['relative_time'] . ' назад' ?>
                                    </time>
                                </div>
                            </a>
                        </div>
                        <div class="post__indicators">
                            <div class="post__buttons">
                                <a class="post__indicator post__indicator--likes button" href="likes.php?post_id=<?= $card['post_id'] ?>" title="Лайк">
                                    <svg class="post__indicator-icon" width="20" height="17">
                                        <use xlink:href="#icon-heart"></use>
                                    </svg>
                                    <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                        <use xlink:href="#icon-heart-active"></use>
                                    </svg>
                                    <span><?= $card['likes_count'] ?></span>
                                    <span class="visually-hidden">количество лайков</span>
                                </a>
                                <a class="post__indicator post__indicator--comments button" href="post.php?post_id=<?= $card['post_id'] ?>" title="Комментарии">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="#icon-comment"></use>
                                    </svg>
                                    <span><?= $card['comment_count'] ?></span>
                                    <span class="visually-hidden">количество комментариев</span>
                                </a>
                            </div>
                        </div>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if ($countPosts > 6 ): ?>
            <div class="popular__page-links">
                <a class="popular__page-link popular__page-link--prev button button--gray" href="popular.php?page=<?= $previousPage .  $currentPageParams ?>">Предыдущая страница</a>
                <a class="popular__page-link popular__page-link--next button button--gray" href="popular.php?page=<?= $nextPage .  $currentPageParams ?>">Следующая страница</a>
            </div>
        <?php endif; ?>
    </div>
</section>
