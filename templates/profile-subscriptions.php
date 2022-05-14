<section class="profile__subscriptions tabs__content tabs__content--active">
    <h2 class="visually-hidden">Подписки</h2>
    <ul class="profile__subscriptions-list">
    <?php foreach ($mainContent as $subscription): ?>
        <li class="post-mini post-mini--photo post user">
            <div class="post-mini__user-info user__info">
                <div class="post-mini__avatar user__avatar">
                    <a class="user__avatar-link" href="profile.php?profile_id=<?= $subscription['user_id'] ?>">
                        <img class="post-mini__picture user__picture" src="<?= $subscription['avatar'] ?>" alt="Аватар пользователя">
                    </a>
                </div>
                <div class="post-mini__name-wrapper user__name-wrapper">
                    <a class="post-mini__name user__name" href="profile.php?profile_id=<?= $subscription['user_id'] ?>">
                        <span><?= htmlspecialchars($subscription['login']) ?></span>
                    </a>
                    <?php $subscriberDateTime = showDate($subscription['date_registration']); ?>
                    <time class="post-mini__time user__additional" datetime="<?= $subscriberDateTime['datetime'] ?>"><?= $subscriberDateTime['relative_time'] . ' на сайте' ?></time>
                </div>
            </div>
            <div class="post-mini__rating user__rating">
                <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
                    <span class="post-mini__rating-amount user__rating-amount"><?= $subscription['subscribers_count'] ?: '0'  ?></span>
                    <span class="post-mini__rating-text user__rating-text"><?= showSubscribersCount($subscription['subscribers_count'] ?: '0') ?></span>
                </p>
                <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
                    <span class="post-mini__rating-amount user__rating-amount"><?= $subscription['count_post'] ?></span>
                    <span class="post-mini__rating-text user__rating-text"><?= showAuthorPostsCount($subscription['count_post']) ?></span>
                </p>
            </div>
            <div class="post-mini__user-buttons user__buttons">
                <?php
                $button = 'button--main';
                $buttonText = 'Подписаться';
                $action = 'subscribe.php';

                if ($subscription['is_subscribe'] === 1) {
                    $button = 'button--quartz';
                    $buttonText = 'Отписаться';
                    $action = 'unsubscribe.php';
                } ?>
                <form action="<?= $action ?>" method="get">
                    <input class="visually-hidden" type="text" name="author_id" value="<?= $subscription['user_id'] ?>">
                    <button class="post-mini__user-button user__button user__button--subscription button <?= $button ?>" type="submit"><?= $buttonText ?></button>
                </form>
            </div>
        </li>
    <?php endforeach; ?>
    </ul>
</section>
