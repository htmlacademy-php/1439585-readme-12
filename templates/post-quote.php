<div class="post-details__wrapper post-quote">
    <div class="post__main">
        <blockquote style="width: 757px;">
            <p>
                <?= htmlspecialchars($postData['content']) ?>
            </p>
            <cite>
                <?php if (!empty($postData['quote_author'])) {
                    echo htmlspecialchars($postData['quote_author']);
                } else echo 'Неизвестный автор'; ?>
            </cite>
        </blockquote>
    </div>
</div>
