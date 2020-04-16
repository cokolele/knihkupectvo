<?php
    require_once("../includes/session.php");
    require_once("../includes/utils.php");
    require_once("../includes/db.php");

    function invalid_data() {
        global $db;

        $db->rollBack();
        header("Location:  /knihkupectvo/order/unsuccessful.php");
        exit;
    }

    if (!isset($_SESSION["logged"]))
        invalid_data();

    try {
        $db->beginTransaction();

        $cart_items = $db->query("SELECT id, available_count, cost FROM view_carts_complete WHERE id_customer = " . $_SESSION["logged"]["id"]);
        $cart_items = $cart_items->fetchAll();

        if (empty($cart_items))
            invalid_data();

        $final_cost = 0;
        $cart_items_valid = [];
        foreach ($cart_items as $item) {
            if ($item["available_count"] > 0) {
                $final_cost += $item["cost"];
                $cart_items_valid[] = $item["id"];
            }
        }

        if (empty($cart_items_valid))
            invalid_data();

        if (!isset($_SESSION["logged"]["address"]) || $_SESSION["logged"]["address"] == null)
            invalid_data();

        $stmt = $db->prepare("SELECT method FROM shipping_method WHERE id = ?");
        $stmt->execute([$_POST["shipping_method"]]);
        $shipping_method = $stmt->fetchAll();

        $stmt = $db->prepare("SELECT method FROM payment_method WHERE id = ?");
        $stmt->execute([$_POST["payment_method"]]);
        $payment_method = $stmt->fetchAll();

        if (empty($shipping_method) || empty($payment_method))
            invalid_data();

        $delete_cart = $db->prepare("DELETE FROM cart WHERE id_customer = ?");
        $delete_cart->execute([$_SESSION["logged"]["id"]]);

        $add_transaction = $db->prepare("INSERT INTO transaction VALUES (0, NOW(), ?, ?, ?, ?)");
        $add_transaction->execute([$final_cost, $_SESSION["logged"]["id"], $_POST["shipping_method"], $_POST["payment_method"]]);
        $transaction_id = $db->lastInsertId();

        $values = "";
        $i = 0;
        foreach ($cart_items_valid as $item_id) {
            if ($i++)
                $values .= ",";
            $values .= " ($transaction_id, $item_id)";
        }

        $add_transaction_books = $db->prepare("INSERT INTO transaction_book VALUES" . $values);
        $add_transaction_books->execute();

        $values = "";
        $i = 0;
        foreach ($cart_items_valid as $item_id) {
            if ($i++)
                $values .= " OR";
            $values .= " id = $item_id";
        }

        $update_books = $db->prepare("UPDATE book SET available_count = available_count - 1, sold_count = sold_count + 1 WHERE" . $values);
        $update_books->execute();

        $db->commit();
        header("Location:  /knihkupectvo/order/successful.php");
        exit;
    } catch (Exception $e) {
        invalid_data();
    }
?>