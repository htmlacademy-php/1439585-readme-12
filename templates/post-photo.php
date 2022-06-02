<div class="post-details__image-wrapper post-photo__image-wrapper">
    <?php if (isset($postData['image_path'])): ?>
        <img src="<?= $postData['image_path'] ?>" alt="Фото от пользователя" width="760" height="507">
    <?php endif;?>
</div>
