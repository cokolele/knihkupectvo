<?php
    require_once("../includes/session.php");

    function onError() {
        header("Location:  /knihkupectvo");
        exit;
    }

    if (!isset($_SESSION["logged"]) || !$_SESSION["logged"]["admin"] || !isset($_GET["id"]))
        onError();

    require_once("../includes/utils.php");
    require_once("../includes/db.php");

    $id = filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT);

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("DELETE FROM book_author WHERE id_book = ?");
        $stmt->execute([$id]);

        $stmt = $db->prepare("DELETE FROM book_category WHERE id_book = ?");
        $stmt->execute([$id]);

        $stmt = $db->prepare("DELETE FROM cart WHERE id_book = ?");
        $stmt->execute([$id]);

        $stmt = $db->prepare("DELETE FROM book WHERE id = ?");
        $stmt->execute([$id]);

        /*
            ešte dajak poriešiť keď si zakaznik pozera historiu objednavok a tam je ID neexistujucej knihy, ale neni čas, šak aj errory pre používateľov treba v každej poriadnej appke :)
        */

        $db->commit();
    } catch (Exception $e) {
        onError();
    }
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
    <div class="text-center title-big">Kniha bola odstránená</div>
</body>
</html>