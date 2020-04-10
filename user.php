<?php
    require_once("includes/session.php");

    if (!isset($_SESSION["logged"])) {
        header("Location:  /knihkupectvo/login.php");
        exit;
    }

    require_once("includes/utils.php");
    require_once("includes/db.php");
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
    <div class="user-container">
        <div class="text-center title-big">
            Váš chars účet
            <a href="/knihkupectvo/logout.php" class="logout-button">Odhlásiť sa</a>
        </div>
        <div class="title-medium three-q-width">Vaša dodacia a fakturačná adresa</div>
        <?php
            require("order/views/update_address.php");
        ?>
        <div class="title-medium three-q-width">Predošlé objednávky</div>
        <div class="cart-container three-q-width">
            <?php
                $transactions = $db->query("SELECT id, date, final_cost FROM view_transactions_complete WHERE id_customer = " . $_SESSION["logged"]["id"]);
                $transactions = $transactions->fetchAll();

                foreach ($transactions as $transaction) {
                    $date = date("d.m.Y", strtotime($transaction["date"]));
                    echo <<<EOF
            <div class="cart-item">
                <a href="/knihkupectvo/transaction.php?id={$transaction["id"]}" class="a-button title-small">Objednávka ID {$transaction["id"]}</a>
                <span class="cart-item-isbn">({$date})</span>
                <span class="flex-divider"></span>
                <span class="cart-item-cost">{$transaction["final_cost"]} €</span>
            </div>
EOF;
                }
            ?>
        </div>
    </div>
</body>
</html>