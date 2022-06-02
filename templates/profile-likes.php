<section class="profile__likes tabs__content tabs__content--active">
    <h2 class="visually-hidden">Лайки</h2>
    <ul class="profile__likes-list">
    <?php foreach ($mainContent as $likes): ?>
        <li class="post-mini post-mini--photo post user">
            <div class="post-mini__user-info user__info">
                <div class="post-mini__avatar user__avatar">
                    <a class="user__avatar-link" href="profile.php?profile_id=<?= $likes['like_user_id'] ?? '' ?>">
                        <?php if (!empty($likes['avatar'])): ?>
                            <img class="post-mini__picture user__picture" src="<?= $likes['avatar'] ?>" alt="Аватар пользователя">
                        <?php endif; ?>
                    </a>
                </div>
                <div class="post-mini__name-wrapper user__name-wrapper">
                    <a class="post-mini__name user__name" href="profile.php?profile_id=<?= $likes['like_user_id'] ?? '' ?>">
                        <span><?= htmlspecialchars($likes['login'] ?? '') ?></span>
                    </a>
                    <div class="post-mini__action">
                        <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                        <?php isset($likes['date_add']) ? $likeDate = showDate($likes['date_add']) : ''; ?>
                        <time class="post-mini__time user__additional" datetime="<?= $likeDate['datetime'] ?? '' ?>">
                            <?= isset($likeDate['relative_time']) ? $likeDate['relative_time'] . ' назад' : '' ?>
                        </time>
                    </div>
                </div>
            </div>
            <div class="post-mini__preview">
                <a class="post-mini__link" href="post.php?post_id=<?= $likes['like_post_id'] ?? '' ?>" title="Перейти на публикацию">
                    <span class="visually-hidden"><?= $likes['category_name'] ?? '' ?></span>
                    <?php if (isset($likes['category_class_name']) && $likes['category_class_name'] === 'text'): ?>
                        <svg class="post-mini__preview-icon" width="20" height="21">
                            <use xlink:href="#icon-filter-text"></use>
                        </svg>
                    <?php elseif (isset($likes['category_class_name']) && $likes['category_class_name'] === 'quote'): ?>
                        <svg class="post-mini__preview-icon" width="21" height="20">
                            <use xlink:href="#icon-filter-quote"></use>
                        </svg>
                    <?php elseif (isset($likes['category_class_name']) && $likes['category_class_name'] === 'photo'): ?>
                        <div class="post-mini__image-wrapper"  style="display: flex; height:107px; width:107;">
                            <?php if (!empty($likes['image_path'])): ?>
                                <img class="post-mini__image" src="<?= $likes['image_path'] ?>" width="107" height="107" alt="Превью публикации">
                            <?php endif; ?>
                        </div>
                    <?php elseif (isset($likes['category_class_name']) && $likes['category_class_name'] === 'video'): ?>
                        <?= isset($likes['video_link']) ? embed_youtube_cover($likes['video_link']) : '' ?>
                        <span class="post-mini__play-big">
                            <svg class="post-mini__play-big-icon" width="12" height="13">
                                <use xlink:href="#icon-video-play-big"></use>
                            </svg>
                        </span>
                    <?php elseif (isset($likes['category_class_name']) && $likes['category_class_name'] === 'link'): ?>
                        <svg class="post-mini__preview-icon" width="21" height="18">
                            <use xlink:href="#icon-filter-link"></use>
                        </svg>
                    <?php endif; ?>
                </a>
            </div>
        </li>
    <?php endforeach; ?>
    </ul>
</section>
