<?php
    require_once("includes/utils.php");
    require_once("includes/db.php");
    require_once("includes/session.php");

    if (isset($_POST["username"])) {
        $stmt = $db->prepare("SELECT * FROM view_customers_basic WHERE username = ? OR email = ?");
        $stmt->execute([$_POST["username"], $_POST["username"]]);
        $customer = $stmt->fetchAll();

        if (!empty($customer) && password_verify($_POST["password"], $customer[0]["password"])) {
            $stmt = $db->prepare("SELECT * FROM view_customers_complete WHERE id = ?");
            $stmt->execute([$customer[0]["id"]]);
            $customer = $stmt->fetchAll()[0];

            $_SESSION["logged"] = $customer;
            header("Location:  /knihkupectvo/user.php");
            exit;
        }
    }
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
    <form class="half-width account-form" method="post">
        <div class="text-center title-big">Prihlásenie</div>
        <input name="username" type="text" placeholder="Prihlasovacie meno alebo E-mail" required>
        <input name="password" type="password" placeholder="Heslo" required>
        <input type="submit">
        <?php
            if (isset($_POST["username"])) {
                echo "<p class=\"error\"><span>⮾</span> Zadané meno alebo heslo je nesprávne</p>";
            }
        ?>
        <p>Nemáte ešte účet ?<a href="/knihkupectvo/register.php" class="title-medium">ZAREGISTRUJTE SA</a></p>
    </form>
</body>
</html>