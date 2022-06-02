<main class="page__main page__main--adding-post">
    <div class="page__main-section">
        <div class="container">
            <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
        </div>
        <div class="adding-post container">
            <div class="adding-post__tabs-wrapper tabs">
                <div class="adding-post__tabs filters">
                    <?php foreach ($categories as $category): ?>
                        <ul class="adding-post__tabs-list filters__list tabs__list">
                            <li class="adding-post__tabs-item filters__item">
                                <?php
                                $contentCategory = filter_input(INPUT_GET, 'category_name', FILTER_SANITIZE_SPECIAL_CHARS);

                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post-type'])) {
                                    //условие на активность таба после сабмита
                                    $compareType = $_POST['post-type'];
                                } elseif ((!empty($contentCategory))) {
                                    $compareType = $contentCategory;
                                } else {
                                    $compareType = 'text';
                                }

                                if ($category['class_name'] === $compareType) {
                                    $activeButton = 'filters__button--active';
                                    $activeTab = 'tabs__item--active';
                                } else {
                                    $activeButton = '';
                                    $activeTab = '';
                                }
                                ?>
                                <a class="adding-post__tabs-link filters__button filters__button--<?= $category['class_name'] ?? '' ?> <?= $activeButton ?> tabs__item <?= $activeTab ?> button" href="add.php?category_name=<?= $category['class_name'] ?? '' ?>">
                                    <svg class="filters__icon" width="22" height="18">
                                        <use xlink:href="#icon-filter-<?= $category['class_name'] ?? '' ?>"></use>
                                    </svg>
                                    <span><?= $category['name'] ?? '' ?></span>
                                </a>
                            </li>
                        </ul>
                    <?php endforeach; ?>
                </div>
                <div class="adding-post__tab-content">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post-type'])) {
                        $contentCategory = $_POST['post-type'];
                        $filePath = "templates/adding-post/adding-post-" . $contentCategory . ".php";
                    } else {
                        if (empty($contentCategory)) {
                            $filePath = "templates/adding-post/adding-post-text.php";
                        } else {
                            $filePath = "templates/adding-post/adding-post-" . $contentCategory . ".php";
                        }
                    }

                    if (file_exists($filePath)) {
                        require_once($filePath);
                    } else {
                        echo "Sorry, there is some mistake. There is nothing you want :(";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>
