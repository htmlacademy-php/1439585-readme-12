<main class="page__main page__main--messages">
    <h1 class="visually-hidden">Личные сообщения</h1>
    <section class="messages tabs">
        <h2 class="visually-hidden">Сообщения</h2>
        <div class="messages__contacts">
            <ul class="messages__contacts-list tabs__list">
                <?php foreach ($contactsList as $dialog): ?>
                    <?php
                    $messageTabActive = "";
                    $tabItemActive = "";
                    $messageNew = "";

                    if (isset($dialog['dialog_with_id']) && $dialog['dialog_with_id'] === $messagesUserId) {
                        $messageTabActive = "messages__contacts-tab--active";
                        $tabItemActive = "tabs__item--active";
                    }

                    if (isset($dialog['new_message_count'])) {
                        $messageNew = "messages__contacts-item--new";
                    } ?>
                    <li class="messages__contacts-item <?= $messageNew ?>">
                        <a class="messages__contacts-tab <?= $messageTabActive ?> tabs__item <?= $tabItemActive ?>"
                           href="messages.php?user_id=<?= $dialog['dialog_with_id'] ?? '' ?>">
                            <?php if (empty($dialog['sender_id'])): ?>
                                <div class="messages__avatar-wrapper">
                                    <?php if (!empty($dialog['avatar'])): ?>
                                        <img class="messages__avatar" src="<?= $dialog['avatar'] ?>"
                                             alt="Аватар пользователя">
                                    <?php endif; ?>
                                </div>
                                <div class="messages__info">
                                    <span class="messages__contact-name">
                                        <?= htmlspecialchars($dialog['login'] ?? '') ?>
                                     </span>
                                </div>
                            <?php else: ?>
                                <div class="messages__avatar-wrapper">
                                    <?php if (!empty($dialog['avatar'])): ?>
                                        <img class="messages__avatar" src="<?= $dialog['avatar'] ?>"
                                             alt="Аватар пользователя">
                                    <?php endif; ?>
                                    <?php if (!empty($dialog['new_message_count'] !== null)): ?>
                                        <i class="messages__indicator"><?= $dialog['new_message_count'] ?></i>
                                    <?php endif; ?>
                                </div>
                                <div class="messages__info">
                                <span class="messages__contact-name">
                                    <?= htmlspecialchars($dialog['login'] ?? '') ?>
                                 </span>
                                    <div class="messages__preview">
                                        <p class="messages__preview-text">
                                            <?php
                                            if (isset($userData['id']) && $dialog['sender_id'] === $userData['id']) {
                                                echo 'Вы: ';
                                            } ?>
                                            <?= htmlspecialchars($dialog['content'] ?? '') ?>
                                        </p>
                                        <time class="messages__preview-time" datetime="<?= $dialog['date_send'] ?? '' ?>">
                                            <?= isset($dialog['date_send']) ? showMessagePreviewDate($dialog['date_send']) : '' ?>
                                        </time>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        $messageClass = "messages__chat";
        if ($messagesUserId === 0) {
            $messageClass = "messages__no-chat";
        } ?>
        <div class="<?= $messageClass ?>">
            <?php if ($messagesUserId !== 0): ?>
                <div class="messages__chat-wrapper">
                    <ul class="messages__list tabs__content tabs__content--active">
                        <?php foreach ($messagesHistory as $message): ?>
                            <?php
                            $messageItem = "";
                            if (isset($message['sender_id']) && isset($userData['id']) && $message['sender_id'] === $userData['id']) {
                                $messageItem = "messages__item--my";
                            }
                            ?>
                            <li class="messages__item <?= $messageItem ?>">
                                <div class="messages__info-wrapper">
                                    <div class="messages__item-avatar">
                                        <a class="messages__author-link"
                                           href="profile.php?profile_id=<?= $message['sender_id'] ?? '' ?>">
                                            <?php if (!empty($message['avatar'])): ?>
                                                <img class="messages__avatar" src="<?= $message['avatar'] ?>"
                                                     alt="Аватар пользователя">
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="messages__item-info">
                                        <a class="messages__author"
                                           href="profile.php?profile_id=<?= $message['sender_id'] ?? '' ?>">
                                            <?= htmlspecialchars($message['login'] ?? '') ?>
                                        </a>
                                        <?php $messageDate = isset($message['date_send']) ? showDate($message['date_send']) : ''; ?>
                                        <time class="messages__time" datetime="<?= $message['date_send'] ?? '' ?>">
                                            <?= isset($messageDate['relative_time']) ? $messageDate['relative_time'] . ' назад' : '' ?>
                                        </time>
                                    </div>
                                </div>
                                <p class="messages__text">
                                    <?= htmlspecialchars($message['content'] ?? '') ?>
                                </p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="comments">
                    <form class="comments__form form" action="messages.php" method="post">
                        <input class="visually-hidden" type="text" name="recipient_user_id" value="<?= $messagesUserId ?>">
                        <input class="visually-hidden" type="text" name="dialog_id" value="<?= $dialogId ?>">
                        <div class="comments__my-avatar">
                            <?php if (!empty($userData['avatar'])): ?>
                                <img class="comments__picture" src="<?= $userData['avatar'] ?>"
                                     alt="Аватар пользователя">
                            <?php endif; ?>
                        </div>
                        <?php
                        if (!empty($validationError)) {
                            $errorLine = 'form__input-section--error';
                        } else {
                            $errorLine = '';
                        }
                        ?>
                        <div class="form__input-section <?= $errorLine ?>">
                            <textarea class="comments__textarea form__textarea form__input" name="message_content"
                                      placeholder="Ваше сообщение"></textarea>
                            <label class="visually-hidden">Ваше сообщение</label>
                            <?php if (!empty($validationError)): ?>
                                <button class="form__error-button button" type="button">!</button>
                                <div class="form__error-text">
                                    <h3 class="form__error-title">Ошибка валидации</h3>
                                    <p class="form__error-desc"><?= $validationError ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button class="comments__submit button button--green" type="submit">Отправить</button>
                    </form>
                </div>
            <?php elseif (empty($contactsList)): ?>
                <div class="messages__no-chat-area">
                    <p class="messages__no-chat-text">
                        У вас пока нет ни одной переписки =(<br><br>
                        Но не расстраивайтесь! Вы можете зайти на страницу любого пользователя, подписаться на него<br>и отправить ему сообщение.
                    </p>
                </div>
            <?php else: ?>
                <div class="messages__no-active-chat-area">
                    <p class="messages__no-chat-text">
                        Выберите диалог
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
