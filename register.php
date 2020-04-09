<?php
    require_once("includes/utils.php");
    require_once("includes/db.php");
    require_once("includes/session.php");

    function check_register_data() {
        global $error, $db;

        $username = filter_var( trim(preg_replace("%\s+%u", " ", html_entity_decode($_POST["username"]))), FILTER_SANITIZE_SPECIAL_CHARS );
        $email = filter_var( trim(preg_replace("%\s+%u", " ", html_entity_decode($_POST["email"]))), FILTER_SANITIZE_EMAIL);
        $password = trim(preg_replace("%\s+%u", " ", html_entity_decode($_POST["password"])));
        $password2 = trim(preg_replace("%\s+%u", " ", html_entity_decode($_POST["password2"])));

        if (empty($username) || empty($email) || empty($password) || empty($password2))
            $error = "Zadané údaje sú prázdne";
        else if ($password != $password2)
            $error = "Heslá sa nezhodujú";
        else if (strlen($username) > 55)
            $error = "Prihlasovacie meno je príliš dlhé";
        else if (strlen($password) > 60)
            $error = "Heslo je príliš dlhé";
        else if (strlen($username) < 3)
            $error = "Prihlasovacie meno je príliš krátke";
        else if (strlen($password) < 3)
            $error = "Heslo je príliš krátke";
        else if (strlen($email) > 100)
            $error = "E-mailová adresa je príliš dlhá";
        else if (strpos($username, "@") !== false)
            $error = "Prihlasovacie meno nesmie obsahovať znak @";

        if (!empty($error))
            return;

        $stmt = $db->prepare("SELECT * FROM view_customers_basic WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $customer = $stmt->fetchAll();

        if (!empty($customer)) {
            $error = "Rovnaké prihlasovacie meno alebo E-mail je už registrované";
            return;
        } else {
            $stmt = $db->prepare("INSERT INTO customer VALUES (0, ?, ?, ?, null, null, null, null, null, null)");
            $stmt->execute([$username, password_hash($password, PASSWORD_BCRYPT), $email]);

            $stmt = $db->prepare("SELECT * FROM view_customers_complete WHERE username = ?");
            $stmt->execute([$username]);
            $customer = $stmt->fetchAll()[0];
            $_SESSION["logged"] = $customer;
            header("Location:  /knihkupectvo/user.php");
            exit;
        }
    }

    if (isset($_POST["username"])) {
        check_register_data();
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
    <form class="half-width account-form" method="post" autocomplete="off">
        <div class="text-center title-big">Registrácia</div>
        <input name="username" type="text" placeholder="Prihlasovacie meno" autocomplete="off" required>
        <input name="email" type="email" placeholder="E-mail" autocomplete="off" required>
        <input name="password" type="password" placeholder="Heslo" autocomplete="off" required>
        <input name="password2" type="password" placeholder="Heslo znova" autocomplete="off" required>
        <input type="submit">
        <?php
            if (isset($_POST["username"])) {
                echo "<p class=\"error\"><span>⮾</span> $error</p>";
            }
        ?>
        <p>Máte už účet ?<a href="/knihkupectvo/login.php" class="title-medium">PRIHLÁSTE SA</a></p>
    </form>
</body>
</html>