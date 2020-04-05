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
        <div>
            <a href="/knihkupectvo/cart.php"> <?php require("icons/cart.html"); ?> </a>
        </div>
    </div>
</header>