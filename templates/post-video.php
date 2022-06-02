<div class="post-video__block">
    <div class="post-video__preview">
        <?php if (isset($postData['video_link'])): ?>
            <?= embed_youtube_video($postData['video_link']); ?>
        <?php endif;?>
    </div>
</div>
