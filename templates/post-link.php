<div class="post__main">
    <div class="post-link__wrapper">
        <a class="post-link__external" href="<?= correctSiteUrl(htmlspecialchars($post['website_link'])) ?>" title="Перейти по ссылке">
            <div class="post-link__info-wrapper">
                <div class="post-link__icon-wrapper">
                    <img src="https://www.google.com/s2/favicons?domain=<?= htmlspecialchars($post['website_link']) ?>" alt="Иконка">
                </div>
                <div class="post-link__info">
                    <h3><?= htmlspecialchars($post['title']) ?></h3>
                    <br><span><?= htmlspecialchars($post['website_link']) ?></span></br>
                </div>
            </div>
        </a>
    </div>
</div>
