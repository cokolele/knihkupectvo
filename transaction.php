<?php
    require_once("includes/session.php");

    if (!isset($_SESSION["logged"])) {
        header("Location:  /knihkupectvo/login.php");
        exit;
    }

    require_once("includes/utils.php");
    require_once("includes/db.php");

    $stmt = $db->prepare("SELECT * FROM view_transactions_complete WHERE id = ?");
    $stmt->execute([$_GET["id"]]);
    $transaction = $stmt->fetchAll();

    if (empty($transaction) || $transaction[0]["id_customer"] != $_SESSION["logged"]["id"]) {
        header("Location:  /knihkupectvo/user.php");
        exit;
    }

    $transaction = $transaction[0];
    $date = date("d.m.Y", strtotime($transaction["date"]));
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
    <div class="transaction-container">
        <div class="text-center title-big">
            Objednávka č. <?php echo $transaction["id"] ?>
        </div>
        <?php
            echo <<<EOF
            <div class="bookpage-details half-width">
                <div>Vytvorené</div>
                <div>$date</div>
                <div>Cena</div>
                <div>{$transaction["final_cost"]} €</div>
                <div>Platba</div>
                <div>{$transaction["payment_method"]}</div>
                <div>Doručenie</div>
                <div>{$transaction["shipping_method"]}</div>
            </div>
EOF;
        ?>
    </div>
    <div class="cart-container three-q-width">
        <?php
            $ids = explode("|", $transaction["books_id"]);
            $titles = explode("|", $transaction["books_title"]);
            $costs = explode("|", $transaction["books_cost"]);

            for ($i = 0; $i < count($ids); $i++) {
                $url_name = strip_punctuation($titles[$i]) . "_" . $ids[$i];
                echo <<<EOF
        <div class="cart-item">
            <a href="/knihkupectvo/book.php?name={$url_name}" class="a-button cart-item-title">{$titles[$i]}</a>
            <span class="flex-divider"></span>
            <span class="cart-item-cost">{$costs[$i]} €</span>
        </div>
EOF;

            }
        ?>
    </div>
</body>
</html>