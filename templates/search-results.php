<main class="page__main page__main--search-results">
    <h1 class="visually-hidden">Страница результатов поиска</h1>
    <section class="search">
        <h2 class="visually-hidden">Результаты поиска</h2>
        <div class="search__query-wrapper">
            <div class="search__query container">
                <span>Вы искали:</span>
                <span class="search__query-text"><?= htmlspecialchars($searchQuery) ?></span>
            </div>
        </div>
        <div class="search__results-wrapper">
            <div class="container">
                <div class="search__content">
                    <?php foreach ($searchContent as $content): ?>
                        <?php $postType = "post-" . $content['category_name']; ?>
                        <article class="search__post post <?= $postType ?>">
                            <header class="post__header post__author">
                                <a class="post__author-link" href="#" title="Автор">
                                    <div class="post__avatar-wrapper">
                                        <?php if (!empty($content['avatar'])): ?>
                                            <img class="post__author-avatar" src="<?= $content['avatar'] ?>" alt="Аватар пользователя" width="60" height="60">
                                        <?php endif; ?>
                                    </div>
                                    <div class="post__info">
                                        <b class="post__author-name"><?= htmlspecialchars($content['login']) ?></b>
                                        <?php $postDate = showDate($content['date_add']); ?>
                                        <span class="post__time" title=" <?= $postDate['title'] ?>" datetime="<?= $postDate['datetime'] ?>">
                                        <?= $postDate['relative_time'] . ' назад' ?>
                                    </span>
                                    </div>
                                </a>
                            </header>
                            <div class="post__main">
                                <h2><a href="post.php?postId=<?= $content['post_id'] ?>"><?= htmlspecialchars($content['title'])?></a></h2>
                                <?php if ($postType == 'post-quote') : ?>
                                    <blockquote>
                                        <p>
                                            <?= htmlspecialchars($content['content']) ?>
                                        </p>
                                        <cite>
                                            <?php if (!empty($content['quote_author'])) {
                                                echo htmlspecialchars($content['quote_author']);
                                            } else echo 'Неизвестный автор'; ?>
                                        </cite>
                                    </blockquote>
                                <?php elseif ($postType == 'post-link') : ?>
                                    <div class="post-link__wrapper">
                                        <a class="post-link__external" href="<?= correctSiteUrl(htmlspecialchars($content['website_link'])) ?>" title="Перейти по ссылке">
                                            <div class="post-link__info-wrapper">
                                                <div class="post-link__icon-wrapper">
                                                    <img src="https://www.google.com/s2/favicons?domain=<?= htmlspecialchars($content['website_link']) ?>" alt="Иконка">
                                                </div>
                                                <div class="post-link__info">
                                                    <h3><?= htmlspecialchars($content['title']) ?></h3>
                                                    <br><span><?= htmlspecialchars($content['website_link']) ?></span></br>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php elseif ($postType == 'post-photo') : ?>
                                    <div class="post-details__image-wrapper post-photo__image-wrapper">
                                        <img src="<?= $content['image_path'] ?>" alt="Фото от пользователя" width="760" height="507">
                                    </div>
                                <?php elseif ($postType == 'post-video') : ?>
                                    <div class="post-video__block">
                                        <div class="post-video__preview">
                                            <?= embed_youtube_video($content['video_link']); ?>
                                        </div>
                                    </div>
                                <?php elseif ($postType == 'post-text') : ?>
                                    <p><?= cutCardContent($content['content']) ?></p>
                                <?php endif; ?>
                            </div>
                            <footer class="post__footer post__indicators">
                                <div class="post__buttons">
                                    <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                        <svg class="post__indicator-icon" width="20" height="17">
                                            <use xlink:href="#icon-heart"></use>
                                        </svg>
                                        <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                            <use xlink:href="#icon-heart-active"></use>
                                        </svg>
                                        <span><?= $content['likes_count'] ?></span>
                                        <span class="visually-hidden">количество лайков</span>
                                    </a>
                                    <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-comment"></use>
                                        </svg>
                                        <span><?= $content['comment_count'] ?></span>
                                        <span class="visually-hidden">количество комментариев</span>
                                    </a>
                                </div>
                            </footer>
                            <ul class="post__tags">
                                <?php foreach ($postHashtags as $postId => $hashtags): ?>
                                    <?php if (($content['post_id'] == $postId) && !empty($hashtags)): ?>
                                        <?php foreach ($hashtags as $tag): ?>
                                            <li><a href="search.php?search=%23<?= ($tag) ?>">#<?= ($tag) ?></a></li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>
