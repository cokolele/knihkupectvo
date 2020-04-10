<header>
    <div class="header-logo">
        <a href="/knihkupectvo/"><?php require("icons/logo.html"); ?></a>
    </div>
    <div class="header-search">
        <form action="/knihkupectvo/catalogue.php" method="get">
            <input name="search" type="text" placeholder="Hľadaj knihu ...">
            <label>
                <input type="submit" style="display: none;"/>
                <?php require("icons/search.html"); ?>
            </label>
        </form>
    </div>
    <div class="header-options inline-flex">
        <div class="inline-flex">
            <?php require("icons/delivery-point.html"); ?>
            <div>
                <span class="description-wide-accent no-select">DOPRAVA ZADARMO</span><br>
                <span class="description-wide-grey no-select">NA VEĽA MIESTACH</span>
            </div>
        </div>
        <div>
            <a href="/knihkupectvo/user.php"> <?php require("icons/user.html"); ?> </a>
        </div>
        <?php
            if (isset($_SESSION["logged"])) {
                $cart_items_count = $db->query("SELECT id, available_count, cost FROM view_carts_complete WHERE id_customer = " . $_SESSION["logged"]["id"]);
                $cart_items_count = count($cart_items_count->fetchAll());

                if ($cart_items_count)
                    $cart_items_count = "<div class=\"cart-count no-select\">$cart_items_count</div>";
                else
                    $cart_items_count = "";

                echo "<div class=\"cart-icon-container\"><a href=\"/knihkupectvo/cart.php\">";
                require("icons/cart.html");
                echo "</a>$cart_items_count</div>";
            }
        ?>
    </div>
</header>