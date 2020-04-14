<?php
    require_once("includes/db.php");
    require_once("includes/utils.php");
    require_once("includes/session.php");
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
                    $results = $db->query("SELECT category FROM view_categories_complete");
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
            <form class="sort-container">
                <?php
                    if (isset($_GET["category"]))
                        echo "<input type=\"hidden\" name=\"category\" value=\"" . $_GET["category"] . "\">";
                    else if (isset($_GET["search"]))
                        echo "<input type=\"hidden\" name=\"search\" value=\"" . $_GET["search"] . "\">";
                ?>
                <input id="sort_popular" type="submit" name="sort" value="popular">
                <input id="sort_cheap" type="submit" name="sort" value="cheap">
                <input id="sort_expensive" type="submit" name="sort" value="expensive">
                <input id="sort_new" type="submit" name="sort" value="new">

                <label <?php if (isset($_GET["sort"]) && $_GET["sort"] == "popular") echo "class=\"active\"" ?> for="sort_popular">Najpredávanejšie</label>
                <label <?php if (isset($_GET["sort"]) && $_GET["sort"] == "cheap") echo "class=\"active\"" ?> for="sort_cheap">Najlacnejšie</label>
                <label <?php if (isset($_GET["sort"]) && $_GET["sort"] == "expensive") echo "class=\"active\"" ?> for="sort_expensive">Najdrahšie</label>
                <label <?php if (isset($_GET["sort"]) && $_GET["sort"] == "new") echo "class=\"active\"" ?> for="sort_new">Najnovšie</label>
            </form>
            <div class="books-container-smaller">
                <?php
                    $order = "";
                    if (isset($_GET["sort"])) {
                        switch ($_GET["sort"]) {
                            case "popular":
                                $order = " ORDER BY sold_count DESC";
                                break;
                            case "cheap":
                                $order = " ORDER BY cost ASC";
                                break;
                            case "expensive":
                                $order = " ORDER BY cost DESC";
                                break;
                            case "new":
                                $order = " ORDER BY publish_date DESC";
                                break;
                        }
                    }
                    $results = $db->query("SELECT id, title, thumbnail_url, cost, categories, authors, available_count FROM view_books_complete" . $order);
                    $results = $results->fetchAll();

                    $limit = 15;
                    $books_count = 0;
                    $page = 1;
                    if (isset($_GET["page"])) {
                        $_page = filter_var($_GET["page"], FILTER_SANITIZE_NUMBER_INT);
                        if ($_page > 1)
                            $page = $_page;
                    }

                    $books_rendered = 0;
                    foreach ($results as $book) {
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
                            if (($page - 1) * $limit <= $books_count && $books_count < $page * $limit && ++$books_rendered <= $limit)
                                template_book_showcase($book);
                            $books_count++;
                        }
                    }

                    $page_last = ceil($books_count / $limit);
                    $pages_left = $page_last - $page;

                ?>
            </div>
            <?php
                if ($books_count > $limit) {
                    echo "<form class=\"sort-container pager-container\">";

                    if (isset($_GET["category"]))
                        echo "<input type=\"hidden\" name=\"category\" value=\"" . $_GET["category"] . "\">";
                    else if (isset($_GET["search"]))
                        echo "<input type=\"hidden\" name=\"search\" value=\"" . $_GET["search"] . "\">";
                    if (isset($_GET["sort"]))
                        echo "<input type=\"hidden\" name=\"sort\" value=\"" . $_GET["sort"] . "\">";

                    if ($page != 1) {
                        echo "<input id=\"page_prev\" type=\"submit\" name=\"page\" value=\"" . ($page - 1) . "\">";
                        echo "<label for=\"page_prev\">❮</label>";
                        echo "<input id=\"page_first\" type=\"submit\" name=\"page\" value=\"1\">";
                        echo "<label for=\"page_first\">1</label>";
                    }
                    if ($page > 4) {
                        echo "<label class=\"blank\">...</label>";
                    }
                    if ($page - 2 > 1) {
                        echo "<input id=\"page_prev_absolute_2\" type=\"submit\" name=\"page\" value=\"" . ($page - 2) . "\">";
                        echo "<label for=\"page_prev_absolute_2\">" . ($page - 2) . "</label>";
                    }
                    if ($page - 1 > 1) {
                        echo "<input id=\"page_prev_absolute\" type=\"submit\" name=\"page\" value=\"" . ($page - 1) ."\">";
                        echo "<label for=\"page_prev_absolute\">" . ($page - 1) ."</label>";
                    }

                    echo "<label class=\"active\">$page</label>";

                    if ($pages_left - 1 > 0) {
                        echo "<input id=\"page_next_absolute\" type=\"submit\" name=\"page\" value=\"" . ($page + 1) . "\">";
                        echo "<label for=\"page_next_absolute\">" . ($page + 1) . "</label>";
                    }
                    if ($pages_left - 2 > 0) {
                        echo "<input id=\"page_next_absolute_2\" type=\"submit\" name=\"page\" value=\"" . ($page + 2) ."\">";
                        echo "<label for=\"page_next_absolute_2\">" . ($page + 2) ."</label>";
                    }
                    if ($pages_left > 3) {
                        echo "<label class=\"blank\">...</label>";
                    }
                    if ($page != $page_last) {
                        echo "<input id=\"page_last\" type=\"submit\" name=\"page\" value=\"" . $page_last . "\">";
                        echo "<label for=\"page_last\">" . $page_last . "</label>";
                        echo "<input id=\"page_next\" type=\"submit\" name=\"page\" value=\"" . ($page + 1) . "\">";
                        echo "<label for=\"page_next\">❯</label>";
                    }

                    echo "</form>";
                }
            ?>
        </main>
    </div>
</body>
</html>