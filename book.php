<?php
    if (!isset($_GET["name"])) {
        header("Location: /knihkupectvo/catalogue.php");
        exit;
    }

    require_once("includes/session.php");
    require_once("includes/utils.php");
    require_once("includes/db.php");

    $params = explode("_", $_GET["name"]);
    $id = end($params);

    $stmt = $db->prepare("SELECT * FROM view_books_complete WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetchAll();

    if (empty($book)) {
        header("Location: /knihkupectvo/catalogue.php");
        exit;
    }

    $book = $book[0];
    $authors = explode("|", $book["authors"]);
    $categories = explode("|", $book["categories"]);
    $unavailable = $book["available_count"] == 0;
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

    <?php
        if (isset($_SESSION["logged"]) && $_SESSION["logged"]["admin"]) {
            echo <<<EOF
    <fieldset>
        <legend>admin menu</legend>
        <a href="/knihkupectvo/admin/add_book.php?id={$book["id"]}" class="a-button">Upraviť knihu</a>
        <a href="/knihkupectvo/admin/remove_book.php?id={$book["id"]}" class="a-button">Odstrániť knihu</a>
    </fieldset>
EOF;
        }
    ?>

    <span class="title-medium">
        <a href="/knihkupectvo/catalogue.php" class="a-inline">Katalóg</a>
        🠆
        <?php
            $i = 0;
            foreach ($categories as $category) {
                if ($i++)
                    echo " | ";
                echo "<a href=\"/knihkupectvo/catalogue.php?category=$category\" class=\"a-inline\">$category</a>";
            }
        ?>
    </span>
    <div class="bookpage-container">
        <aside>
            <img src="<?php echo $book["thumbnail_url"] ?>" alt="">
        </aside>
        <main>
            <div>
                <div class="bookpage-title"><?php echo $book["title"]; ?></div>
                <div class="bookpage-authors"><?php echo implode(", ", $authors) ?></div>
                <?php
                    if (empty($book["description_short"]) && !empty($book["description_long"])) {
                        echo "<div class=\"bookpage-description\">" . $book["description_long"] . "</div>";
                    } else if (!empty($book["description_short"])) {
                        echo "<div class=\"bookpage-description\">" . $book["description_short"] . "</div>";
                    }
                ?>
                <div class="bookpage-details">
                    <?php
                        if (!empty($book["isbn"]))echo "<div>ISBN</div><div>" . $book["isbn"] . "</div>";
                        if (!empty($book["page_count"])) echo "<div>Počet strán</div><div>" . $book["page_count"] . "</div>";
                        if (!empty($book["status"])) echo "<div>Status vydania</div><div>" . $book["status"] . "</div>";
                        if (!empty($book["publish_date"])) echo "<div>Dátum vydania</div><div>" . date("d.m.Y", $book["publish_date"]) . "</div>";
                        echo "<div>Počet predaných kusov</div><div>" . $book["sold_count"] . "</div>";
                        echo "<div>Počet kusov na sklade</div><div>" . $book["available_count"] . "</div>";
                    ?>
                </div>
            </div>
            <div class="bookpage-bottom">
                <div class="bookpage-cost"><?php echo $book["cost"] . " €"; ?></div>
                <div>
                    <?php
                        $url = "/knihkupectvo/cart.php?add=" . $book["id"];
                        $msg = "Pridať do košíka";

                        if (!isset($_SESSION["logged"])) {
                            $url = "/knihkupectvo/login.php";
                            $msg = "Prihláste sa";
                        }
                        if ($unavailable) {
                            $msg = "Vypredané";
                        }
                    ?>
                    <a href="<?php echo $url; ?>" class="bookpage-buy-button a-button <?php if ($unavailable) echo "disabled" ?>">
                        <?php echo $msg; ?>
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>