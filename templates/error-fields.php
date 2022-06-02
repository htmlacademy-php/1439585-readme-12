<?php
if (!empty($errorFields)) {
    $visualHidden = '';
} else {
    $visualHidden = 'visually-hidden';
} ?>

<div class="form__invalid-block <?= $visualHidden ?>">
    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
    <ul class="form__invalid-list">
        <?php foreach ($errorFields as $error): ?>
            <li class="form__invalid-item"><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
