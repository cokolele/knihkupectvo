<?php
    require_once("includes/session.php");

    if (!isset($_SESSION["logged"])) {
        header("Location:  /knihkupectvo/login.php");
        exit;
    }

    require_once("includes/utils.php");
    require_once("includes/db.php");

    function add_book_to_cart($id) {
        global $db;
        $stmt = $db->prepare("SELECT id, available_count FROM view_books_complete WHERE id = ?");
        $stmt->execute([$id]);
        $book = $stmt->fetchAll();

        //ak taka kniha neexistuje
        if (empty($book)) return;
        //vypredana kniha
        if ($book[0]["available_count"] == 0) return;

        $stmt = $db->prepare("SELECT * FROM cart WHERE id_customer = ? AND id_book = ?");
        $stmt->execute([$_SESSION["logged"]["id"], $id]);
        $same_item = $stmt->fetchAll();

        //ak taku knihu uz ma v kosiku
        if (!empty($same_item)) return;

        $stmt = $db->prepare("INSERT INTO cart VALUES (?, ?)");
        $stmt->execute([$_SESSION["logged"]["id"], $id]);
    }

    if (isset($_GET["add"])) {
        add_book_to_cart($_GET["add"]);
    }

    if (isset($_GET["remove"])) {
        global $db;
        $stmt = $db->prepare("DELETE FROM cart WHERE id_customer = ? AND id_book = ?");
        $stmt->execute([$_SESSION["logged"]["id"], $_GET["remove"]]);
    }
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
    <div class="text-center no-select">
        <span class="title-medium">Košík</span>
        <span class="title-medium color-grey">🠆</span>
        <span class="title-medium color-grey">Dodacie údaje</span>
        <span class="title-medium color-grey">🠆</span>
        <span class="title-medium color-grey">Doručenie a platba</span>
    </div>
    <div class="text-center title-big">Váš chars košík</div>
    <div class="cart-container three-q-width">
        <?php
            $cart_items = $db->query("SELECT id, title, isbn, cost, thumbnail_url FROM view_carts_complete WHERE id_customer = " . $_SESSION["logged"]["id"]);
            $cart_items = $cart_items->fetchAll();

            if (empty($cart_items))
                echo "<div class=\"title-medium color-grey\">Váš košík je prázdny :-(</div>";

            foreach ($cart_items as $book) {
                $url_name = strip_punctuation($book["title"]) . "_" . $book["id"];
                echo <<<EOF
        <div class="cart-item">
            <img class="cart-item-img" src="{$book["thumbnail_url"]}" alt="">
            <a href="/knihkupectvo/book.php?name={$url_name}" class="a-button cart-item-title">{$book["title"]}</a>
            <span class="cart-item-isbn">({$book["isbn"]})</span>
            <span class="flex-divider"></span>
            <span class="cart-item-cost">{$book["cost"]} €</span>
            <a class="a-button cart-item-remove" href="/knihkupectvo/cart.php?remove={$book["id"]}">🞪</a>
        </div>
EOF;
            }
        ?>
    </div>
    <div class="order-continue-container three-q-width">
        <div class="flex-divider"></div>
        <a href="/knihkupectvo/order/address.php" class="order-continue a-button <?php if (empty($cart_items)) echo "disabled" ?>">
            Pokračovať ⯈
        </a>
    </div>
</body>
</html>