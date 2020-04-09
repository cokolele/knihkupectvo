<?php
    require_once("includes/utils.php");
    require_once("includes/db.php");
    require_once("includes/session.php");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
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
    <div class="landingbg">
        <span></span>
        <span class="no-select">Najdôležitejší je základ</span>
        <span class="no-select">Vybudujte ho <span class="color-accent">s nami.</span> IT Kníhkupectvo chars</span>
    </div>
    <div class="text-center">
        <div class="title-medium">Kategórie</div>
        <div class="categories-carousel">
            <div class="categories-carousel-controller no-select">❯</div>
            <?php
                $results = $db->query("SELECT * FROM view_categories_complete");
                $results = $results->fetchAll();
                foreach ($results as $category) {
                    echo <<<EOF
            <div class="categories-carousel-category">
                <a href="/knihkupectvo/catalogue.php?category={$category[0]}">{$category[0]}</a>
            </div>
EOF;
                }
            ?>
            <div class="categories-carousel-controller no-select">❯</div>
            <script>
                const carouselEl = document.querySelector(".categories-carousel");
                const carousel = {
                    offset: 0,
                    offsetMax: carouselEl.scrollWidth - carouselEl.offsetWidth,
                    offsetEl: carouselEl.children[1],
                    left: document.querySelectorAll(".categories-carousel-controller")[0],
                    right: document.querySelectorAll(".categories-carousel-controller")[1]
                };

                carousel.right.addEventListener("click", () => {
                    carousel.left.style.visibility = "visible";
                    carousel.offsetEl.style.marginLeft = carousel.offset + 400 > carousel.offsetMax ? `-${carousel.offsetMax}px` : `-${carousel.offset + 400}px`;
                    carousel.offset = carousel.offset + 400 > carousel.offsetMax ? carousel.offsetMax : carousel.offset + 400;
                });

                carousel.left.addEventListener("click", () => {
                    carousel.offsetEl.style.marginLeft = carousel.offset - 400 < 0 ? "0" : `-${carousel.offset - 400}px`;
                    carousel.offset = carousel.offset - 400 < 0 ? 0 : carousel.offset - 400;
                });
            </script>
        </div>
        <a class="a-button title-small text-right" href="/knihkupectvo/catalogue.php">Zobraziť všetky</a>
    </div>
    <div class="title-big text-left">Najpredávanejšie</div>
    <div class="books-container">
        <?php
            $results = $db->query("SELECT id, title, thumbnail_url, cost, authors, available_count FROM view_books_complete ORDER BY sold_count LIMIT 9");
            $results = $results->fetchAll();

            foreach ($results as $book)
                template_book_showcase($book);
        ?>
    </div>
    <a class="a-button title-small text-center" href="/knihkupectvo/catalogue.php">Zobraziť všetky</a>
    <div class="title-big text-left">Novinky</div>
    <div class="books-container">
        <?php
            $results = $db->query("SELECT id, title, thumbnail_url, cost, authors, available_count FROM view_books_complete ORDER BY publish_date DESC LIMIT 6");
            $results = $results->fetchAll();

            foreach ($results as $book)
                template_book_showcase($book);
        ?>
    </div>
    <a class="a-button title-small text-center" href="/knihkupectvo/catalogue.php">Zobraziť všetky</a>
</body>
</html>