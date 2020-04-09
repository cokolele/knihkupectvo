<?php
    function invalid_data() {
        header("Location:  /knihkupectvo/index.php");
        exit;
    }

    $cart_items = $db->query("SELECT * FROM view_carts_complete WHERE id_customer = " . $_SESSION["logged"]["id"]);
    $cart_items = $cart_items->fetchAll();

    if (empty($cart_items))
        invalid_data();

    foreach ($cart_items as $item) {
        if ($item["available_count"])
    }
?>