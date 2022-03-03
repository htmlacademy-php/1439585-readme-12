<?php if (!empty($errorFields)) {
    $visualHudden = '';
} else {
    $visualHudden = 'visually-hidden';
} ?>

<div class="form__invalid-block <?= $visualHudden ?>">
    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
    <ul class="form__invalid-list">
        <?php foreach ($errorFields as $key => $error) : ?>
            <li class="form__invalid-item"><?= $errorFields[$key] ?></li>
        <?php endforeach; ?>
    </ul>
</div>
