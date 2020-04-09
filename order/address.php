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
    <div class="text-center no-select">
        <span class="title-medium color-grey">Košík</span>
        <span class="title-medium color-grey">🠆</span>
        <span class="title-medium">Dodacie údaje</span>
        <span class="title-medium color-grey">🠆</span>
        <span class="title-medium color-grey">Doručenie a platba</span>
    </div>
    <div class="text-center title-big">Zadajte vašu dodaciu a fakturačnú adresu</div>
    <?php
        require_once("views/update_address.php");
    ?>
    <div class="order-continue-container three-q-width">
        <a href="/knihkupectvo/cart.php" class="order-continue-blank a-button">
            ⯇ Späť
        </a>
        <a href="/knihkupectvo/order/shipping_payment.php" class="order-continue a-button <?php if (empty($_SESSION["logged"]["address"])) echo "disabled" ?>">
            Pokračovať ⯈
        </a>
    </div>
</body>
</html>