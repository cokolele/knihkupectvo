<?php
    require_once("includes/db.php");
    require_once("includes/utils.php");
?>
<!DOCTYPE html>
<html lang="en">
    <?php
        require_once("includes/views/head.php");
    ?>
<body class="page-container">
    <?php
        require_once("includes/views/header.php");
    ?>

    <div class="catalogue-container">
        <aside>
            <div class="catalogue-categories">
                <?php
                    $results = $db->query("SELECT category FROM categories");
                    $results = $results->fetchAll();
                    foreach ($results as $category) {
                        $active = (isset($_GET["category"]) && $_GET["category"] == $category[0]) ? "active" : "";
                        echo <<<EOF
                <a class="catalogue-category a-button title-small text-left {$active}" href="/knihkupectvo/catalogue.php?category={$category[0]}">{$category[0]}</a>
EOF;
                    }
                ?>
            </div>
        </aside>

        <main>
            <?php
                if (isset($_GET["search"]))
                    echo "<div class=\"title-big text-left\">Vyhľadávanie: " . htmlspecialchars($_GET["search"]) . "</div>";
                else if (isset($_GET["category"]))
                    echo "<div class=\"title-big text-left\">Kategória: " . htmlspecialchars($_GET["category"]) . "</div>";
            ?>
            <div class="books-container-smaller">
                <?php
                    $results = $db->query("SELECT id, title, thumbnail_url, cost, categories, authors, available_count FROM view_books_complete");
                    $results = $results->fetchAll();

                    $limit = 15;
                    $count = 0;

                    foreach ($results as $book) {
                        $author = explode(",", $book["authors"])[0];

                        if ((   isset($_GET["search"]) &&
                                (strpos(strip_punctuation($book["title"]), strip_punctuation($_GET["search"])) !== false ||
                                strpos(strip_punctuation($book["authors"]), strip_punctuation($_GET["search"])) !== false ||
                                strpos(strip_punctuation($book["categories"]), strip_punctuation($_GET["search"])) !== false)
                                ) || (
                                isset($_GET["category"]) &&
                                strpos($book["categories"], $_GET["category"]) !== false
                                ) || (
                                !isset($_GET["category"]) &&
                                !isset($_GET["search"])
                            ))
                        {
                            template_book_showcase($book);
                            if (++$count >= 15)
                                break;
                        }
                    }
                ?>
            </div>
        </main>
    </div>
</body>
</html>