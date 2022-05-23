<main class="page__main page__main--profile">
    <h1 class="visually-hidden">Профиль</h1>
    <div class="profile profile--default">
        <div class="profile__user-wrapper">
            <div class="profile__user user container">
                <div class="profile__user-info user__info">
                    <div class="profile__avatar user__avatar">
                        <?php if (!empty($userProfileData['avatar'])): ?>
                            <img class="profile__picture user__picture" src="<?= $userProfileData['avatar'] ?>" width="100" height="100" alt="Аватар пользователя">
                        <?php endif; ?>
                    </div>
                    <div class="profile__name-wrapper user__name-wrapper">
                        <span class="profile__name user__name"><?= htmlspecialchars($userProfileData['login']) ?></span>
                        <?php $authorDateTime = showDate($userProfileData['date_registration']); ?>
                        <time class="profile__user-time user__time" datetime="<?= $authorDateTime['datetime'] ?>"><?= $authorDateTime['relative_time'] . ' на сайте' ?></time>
                    </div>
                </div>
                <div class="profile__rating user__rating">
                    <p class="profile__rating-item user__rating-item user__rating-item--publications">
                        <span class="user__rating-amount"><?= $userProfileData['count_posts'] ?></span>
                        <span class="profile__rating-text user__rating-text"><?= showAuthorPostsCount($userProfileData['count_posts']) ?></span>
                    </p>
                    <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
                        <span class="user__rating-amount"><?= $userProfileData['subscribers'] ?: '0' ?></span>
                        <span class="profile__rating-text user__rating-text"><?= showSubscribersCount($userProfileData['subscribers'] ?: '0') ?></span>
                    </p>
                </div>
                <div class="profile__user-buttons user__buttons">
                    <?php if ($userProfileData['id'] !== $authorizedUser): ?>
                        <?php
                        $button = 'button--main';
                        $buttonText = 'Подписаться';
                        $action = 'subscribe.php';

                        if ($userProfileData['is_subscribe'] === 1) {
                            $button = 'button--quartz';
                            $buttonText = 'Отписаться';
                            $action = 'unsubscribe.php';
                        } ?>
                        <form action="<?= $action ?>" method="get">
                            <input class="visually-hidden" type="text" name="author_id" value="<?= $userProfileData['id'] ?>">
                            <button style="margin-bottom: 10px; width: 360px;" class="profile__user-button user__button user__button--subscription button <?= $button ?>" type="submit" ><?= $buttonText ?></button>
                        </form>
                        <?php if ($userProfileData['is_subscribe'] === 1): ?>
                            <a class="profile__user-button user__button user__button--writing button button--green" href="messages.php?user_id=<?= $userProfileData['id'] ?>">Сообщение</a>
                        <?php endif;?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="profile__tabs-wrapper tabs">
            <div class="container">
                <div class="profile__tabs filters">
                    <b class="profile__tabs-caption filters__caption">Показать:</b>
                    <ul class="profile__tabs-list filters__list tabs__list">
                        <?php
                        $tabList = [
                            'posts' => 'Посты',
                            'likes' => 'Лайки',
                            'subscriptions' => 'Подписки'
                        ]; ?>
                        <?php foreach ($tabList as $key => $tab): ?>
                            <?php
                            $buttonActive = '';
                            $tabItemActive = '';

                            if ($key === $section) {
                                $buttonActive = 'filters__button--active';
                                $tabItemActive = 'tabs__item--active';
                            } ?>
                            <li class="profile__tabs-item filters__item">
                                <a class="profile__tabs-link filters__button <?= $buttonActive ?> tabs__item <?= $tabItemActive ?> button" href="profile.php?profile_id=<?= $userProfileData['id'] ?>&section=<?= $key ?>"><?= $tab ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="profile__tab-content">
                    <?php
                    $filePath = "templates/profile-" . $section . ".php";
                    if (file_exists($filePath)) {
                        require_once($filePath);
                    } else {
                        echo "Sorry, there is some mistake.  There is nothing you want :(";
                    } ?>
                </div>
            </div>
        </div>
    </div>
</main>
