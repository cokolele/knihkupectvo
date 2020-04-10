<?php
    require_once("../includes/session.php");

    if (!isset($_SESSION["logged"])) {
        header("Location:  /knihkupectvo/login.php");
        exit;
    }

    require_once("../includes/utils.php");
    require_once("../includes/db.php");
?>
<!DOCTYPE html>
<html lang="en">
    <?php
        require_once("../includes/views/head.php");
    ?>
<body class="page-container">
    <?php
        require_once("../includes/views/header.php");
    ?>
    <div class="text-center title-big">Sumarizácia</div>
    <form method="post" action="/knihkupectvo/order/process.php">
        <input type="hidden" name="shipping_method" value="<?php if (isset($_POST["shipping_method"])) echo $_POST["shipping_method"] ?>">
        <input type="hidden" name="payment_method" value="<?php if (isset($_POST["payment_method"])) echo $_POST["payment_method"] ?>">
        <div class="title-medium three-q-width">Knihy v košíku</div>
        <div class="cart-container half-width">
            <?php
                $cart_items = $db->query("SELECT title, isbn, cost FROM view_carts_complete WHERE id_customer = " . $_SESSION["logged"]["id"]);
                $cart_items = $cart_items->fetchAll();
                $total_cost = 0;
                foreach ($cart_items as $book) {
                    $total_cost += $book["cost"];
                    echo <<<EOF
            <div class="cart-item">
                <span class="cart-item-title">{$book["title"]}</span>
                <span class="cart-item-isbn">({$book["isbn"]})</span>
                <span class="flex-divider"></span>
                <span class="cart-item-cost">{$book["cost"]} €</span>
            </div>
EOF;
                }
            ?>
        </div>
        <div class="cart-item half-width">
            <span class="cart-item-title color-grey">Spolu</span>
            <span class="flex-divider"></span>
            <span class="cart-item-cost"><?php echo $total_cost ?> €</span>
        </div>
        <div class="title-medium three-q-width">Doručenie</div>
        <div class="half-width catalogue-container">
            <?php
                $shipping_method = "";
                $payment_method = "";

                if (isset($_POST["shipping_method"])) {
                    $stmt = $db->prepare("SELECT method FROM shipping_method WHERE id = ?");
                    $stmt->execute([$_POST["shipping_method"]]);
                    $_shipping_method = $stmt->fetchAll();
                    if (!empty($_shipping_method)) $shipping_method = $_shipping_method[0]["method"];
                }

                if (isset($_POST["payment_method"])) {
                    $stmt = $db->prepare("SELECT method FROM payment_method WHERE id = ?");
                    $stmt->execute([$_POST["payment_method"]]);
                    $_payment_method = $stmt->fetchAll();
                    if (!empty($_payment_method)) $payment_method = $_payment_method[0]["method"];
                }
            ?>
            <div class="flex-q">
                <div class="title-small color-grey">Meno</div>
                <div class="title-small color-grey">Priezvisko</div>
                <div class="title-small color-grey">E-mailová adresa</div>
                <div class="title-small color-grey">Telefónny kontakt</div>
                <div class="title-small color-grey">Ulica</div>
                <div class="title-small color-grey">PSČ</div>
                <div class="title-small color-grey">Mesto</div>
                <div class="title-small color-grey">Spôsob doručenia</div>
                <div class="title-small color-grey">Spôsob platby</div>
            </div>
            <div class="flex-three-q">
                <div class="title-small color-grey"><?php echo $_SESSION["logged"]["first_name"] ?></div>
                <div class="title-small color-grey"><?php echo $_SESSION["logged"]["last_name"] ?></div>
                <div class="title-small color-grey"><?php echo $_SESSION["logged"]["email"] ?></div>
                <div class="title-small color-grey"><?php echo $_SESSION["logged"]["phone"] ?></div>
                <div class="title-small color-grey"><?php echo $_SESSION["logged"]["address"] ?></div>
                <div class="title-small color-grey"><?php echo $_SESSION["logged"]["zip_code"] ?></div>
                <div class="title-small color-grey"><?php echo $_SESSION["logged"]["city"] ?></div>
                <div class="title-small color-grey"><?php echo $payment_method ?></div>
                <div class="title-small color-grey"><?php echo $shipping_method ?></div>
            </div>
        </div>
        <div class="order-continue-container three-q-width">
            <a href="/knihkupectvo/order/shipping_payment.php" class="order-continue-blank a-button">
                ⯇ Späť
            </a>
            <input class="order-continue a-button" type="submit" value="Objednať">
        </div>
    </form>
</body>
</html>