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
        <span class="title-medium color-grey">Dodacie údaje</span>
        <span class="title-medium color-grey">🠆</span>
        <span class="title-medium">Doručenie a platba</span>
    </div>
    <div class="text-center title-big">Vyberte si spôsob platby a doručenia</div>
    <form method="post" action="/knihkupectvo/order/summary.php">
        <div class="half-width address-form">
            <div class="title-medium">Doručenie</div>
            <div class="flex-full form-radios">
                <?php
                    $i = 0;

                    $results = $db->query("SELECT * FROM shipping_method");
                    $results = $results->fetchAll();
                    foreach ($results as $method) {
                        echo <<<EOF
                <input type="radio" id="{$method["method"]}" name="shipping_method" value="{$method["id"]}" required>
                <label for="{$method["method"]}">{$method["method"]}</label>
                <div class="flex-full"></div>
EOF;
                    }
                ?>
            </div>
            <div class="title-medium">Platba</div>
            <div class="flex-full form-radios">
                <?php
                    $results = $db->query("SELECT * FROM payment_method");
                    $results = $results->fetchAll();
                    foreach ($results as $method) {
                        echo <<<EOF
                <input type="radio" id="{$method["method"]}" name="payment_method" value="{$method["id"]}" required>
                <label for="{$method["method"]}">{$method["method"]}</label>
                <div class="flex-full"></div>
EOF;
                    }
                ?>
            </div>
        </div>
        <div class="order-continue-container three-q-width">
            <a href="/knihkupectvo/order/address.php" class="order-continue-blank a-button">
                ⯇ Späť
            </a>
            <input class="order-continue a-button" type="submit" value="Pokračovať ⯈">
        </div>
    </form>
</body>
</html>