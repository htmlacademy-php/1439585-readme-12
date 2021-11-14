<div class="post-details__wrapper post-quote">
    <div class="post__main">
        <blockquote style="width: 757px;">
            <p>
                <?= htmlspecialchars($post['content']) ?>
            </p>
            <cite>
                <?php if (!empty($post['quote_author'])) {
                    echo htmlspecialchars($post['quote_author']);
                } else echo 'Неизвестный автор'; ?>
            </cite>
        </blockquote>
    </div>
</div>
