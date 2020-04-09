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
    <div class="text-center title-big">Objednávka bola úspešne vykonaná</div>
    <div class="text-center title-big">Ďakujeme</div>
</body>
</html>