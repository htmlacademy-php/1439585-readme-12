<section class="profile__posts tabs__content tabs__content--active">
    <h2 class="visually-hidden">Публикации</h2>
    <?php foreach ($mainContent as $post): ?>
        <article class="profile__post post post-<?= $post['class_name'] ?? '' ?>">
            <header class="post__header">
                <?php if (isset($post['is_repost']) && $post['is_repost'] === 1): ?>
                    <div class="post__author">
                        <a class="post__author-link" href="profile.php?profile_id=<?= $post['original_author_id'] ?? '' ?>" title="Автор">
                            <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                                <?php if (!empty($post['original_author_avatar'])): ?>
                                    <img class="post__author-avatar" src="<?= $post['original_author_avatar'] ?>" width="60" height="60" alt="Аватар пользователя">
                                <?php endif; ?>
                            </div>
                            <div class="post__info">
                                <b class="post__author-name">Репост: <?= htmlspecialchars($post['original_author_login'] ?? '') ?></b>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
                <h2><a href="post.php?post_id=<?= $post['post_id'] ?? '' ?>"><?= htmlspecialchars($post['title'] ?? '') ?></a></h2>
            </header>
            <div class="post__main">
            <?php if (isset($post['class_name']) && $post['class_name'] === 'quote'): ?>
                <blockquote>
                    <p>
                        <?= htmlspecialchars($post['content'] ?? '') ?>
                    </p>
                    <cite>
                        <?php if (!empty($post['quote_author'])) {
                            echo htmlspecialchars($post['quote_author']);
                        } else {
                            echo 'Неизвестный автор';
                        } ?>
                    </cite>
                </blockquote>
            <?php elseif (isset($post['class_name']) && $post['class_name'] === 'link'): ?>
                <div class="post-link__wrapper">
                    <a class="post-link__external" href="<?= isset($post['website_link']) ? correctSiteUrl(htmlspecialchars($post['website_link'])) : '' ?>" title="Перейти по ссылке">
                        <div class="post-link__icon-wrapper">
                            <img src="https://www.google.com/s2/favicons?domain=<?= $post['website_link'] ?? '' ?>" alt="Иконка">
                        </div>
                        <div class="post-link__info">
                            <h3><?= htmlspecialchars($post['title'] ?? '') ?></h3>
                            <span><?= $post['website_link'] ?? '' ?></span>
                        </div>
                        <svg class="post-link__arrow" width="11" height="16">
                            <use xlink:href="#icon-arrow-right-ad"></use>
                        </svg>
                    </a>
                </div>
            <?php elseif (isset($post['class_name']) && $post['class_name'] === 'photo'): ?>
                <div class="post-photo__image-wrapper">
                    <?php if(isset($post['image_path'])): ?>
                        <img src="<?= $post['image_path'] ?>" alt="Фото от пользователя" width="760" height="396">
                    <?php endif; ?>
                </div>
            <?php elseif (isset($post['class_name']) && $post['class_name'] === 'video'): ?>
                <div class="post-video__block">
                    <div class="post-video__preview">
                        <?php if (isset($post['video_link'])): ?>
                            <?= embed_youtube_video($post['video_link']); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif (isset($post['class_name']) && $post['class_name'] === 'text'): ?>
                <p>
                    <?php if (isset($post['content']) && isset($post['post_id'])): ?>
                        <?= cutCardContent($post['content'], $post['post_id']) ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            </div>
            <footer class="post__footer">
                <div class="post__indicators">
                    <div class="post__buttons">
                        <a class="post__indicator post__indicator--likes button" href="likes.php?post_id=<?= $post['post_id'] ?? '' ?>" title="Лайк">
                            <svg class="post__indicator-icon" width="20" height="17">
                                <use xlink:href="#icon-heart"></use>
                            </svg>
                            <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                <use xlink:href="#icon-heart-active"></use>
                            </svg>
                            <span><?= $post['likes_count'] ?? '0' ?></span>
                            <span class="visually-hidden">количество лайков</span>
                        </a>
                        <a class="post__indicator post__indicator--repost button" href="repost.php?post_id=<?= $post['post_id'] ?? '' ?>" title="Репост">
                            <svg class="post__indicator-icon" width="19" height="17">
                                <use xlink:href="#icon-repost"></use>
                            </svg>
                            <span><?= $post['repost_count'] ?? '0' ?></span>
                            <span class="visually-hidden">количество репостов</span>
                        </a>
                    </div>
                    <?php $postDate = isset($post['date_add']) ? showDate($post['date_add']) : ''; ?>
                    <time class="post__time" datetime="<?= $postDate['datetime'] ?? '' ?>">
                        <?= isset($postDate['relative_time']) ? $postDate['relative_time'] . ' назад' : '' ?>
                    </time>
                </div>
                <ul class="post__tags">
                    <?php foreach ($postHashtags as $postId => $hashtags): ?>
                        <?php if (isset($post['post_id']) && ($post['post_id'] === $postId) && !empty($hashtags)): ?>
                            <?php foreach ($hashtags as $tag): ?>
                                <li><a href="search.php?query=%23<?= ($tag) ?>">#<?= ($tag) ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </footer>
            <div class="comments">
                <a class="comments__button button" href="post.php?post_id=<?= $post['post_id'] ?? '' ?>">Показать комментарии</a>
            </div>
        </article>
    <?php endforeach; ?>
</section>
