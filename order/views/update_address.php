<?php
    function check_customer_data() {
        global $error, $db;

        $email = filter_var( trim(preg_replace("%\s+%u", " ", html_entity_decode($_POST["email"]))), FILTER_SANITIZE_EMAIL);
        $phone = filter_var( trim(preg_replace("%\s+%u", " ", html_entity_decode($_POST["phone"]))), FILTER_SANITIZE_NUMBER_INT );
        $first_name = filter_var( trim(preg_replace("%\s+%u", " ", html_entity_decode($_POST["first_name"]))), FILTER_SANITIZE_SPECIAL_CHARS );
        $last_name = filter_var( trim(preg_replace("%\s+%u", " ", html_entity_decode($_POST["last_name"]))), FILTER_SANITIZE_SPECIAL_CHARS );
        $address = filter_var( trim(preg_replace("%\s+%u", " ", html_entity_decode($_POST["address"]))), FILTER_SANITIZE_SPECIAL_CHARS );
        $zip_code = filter_var( trim(preg_replace("%\s+%u", " ", html_entity_decode($_POST["zip_code"]))), FILTER_SANITIZE_NUMBER_INT );
        $city = filter_var( trim(preg_replace("%\s+%u", " ", html_entity_decode($_POST["city"]))), FILTER_SANITIZE_SPECIAL_CHARS );

        if (empty($email) || empty($phone) || empty($first_name) || empty($last_name) || empty($address) || empty($zip_code) || empty($city))
            $error = "Zadané údaje sú prázdne";
        else if (strlen($email) > 100)
            $error = "E-mailová adresa je príliš dlhá";
        else if (strlen($phone) > 13)
            $error = "Neplatný telefónny kontakt";
        else if (strlen($first_name) > 35)
            $error = "Meno je príliš dlhé";
        else if (strlen($last_name) > 35)
            $error = "Priezvisko je príliš dlhé";
        else if (strlen($address) > 55)
            $error = "Adresa je príliš dlhá";
        else if (strlen($zip_code) != 5)
            $error = "Neplatné PSČ";
        if (strlen($city) > 35)
            $error = "Názov mesta je príliš dlhý";

        if (!empty($error))
            return;

        $stmt = $db->prepare("SELECT * FROM view_customers_complete WHERE email = ? OR phone = ?");
        $stmt->execute([$email, $phone]);
        $same_email_phone = $stmt->fetchAll();

        if (!empty($same_email_phone)) {
            foreach ($same_email_phone as $same) {
                if ($same["id"] != $_SESSION["logged"]["id"]) {
                    if ($email == $same["email"])
                        $error = "Zadaná E-mailová adresa je už registrovaná";
                    else
                        $error = "Zadaný telefónny kontakt je už registrovaný";
                    return;
                }
            }
        }

        //var_dump(["email" => $email, "phone" => $phone, "name" => $first_name, "surname" => $last_name, "address" => $address, "zip" => $zip_code, "city" => $city]);

        $stmt = $db->prepare("UPDATE customer SET email = ?, phone = ?, first_name = ?, last_name = ?, address = ?, zip_code = ?, city = ? WHERE id = ?");
        $stmt->execute([$email, $phone, $first_name, $last_name, $address, $zip_code, $city, $_SESSION["logged"]["id"]]);

        $stmt = $db->prepare("SELECT * FROM view_customers_complete WHERE id = ?");
        $stmt->execute([$_SESSION["logged"]["id"]]);
        $customer = $stmt->fetchAll();
        $_SESSION["logged"] = $customer[0];
    }


    if (isset($_POST["email"]))
        check_customer_data();
?>

<form class="half-width address-form" method="post">
    <div class="flex-half">
        <label for="email">Email</label>
        <input name="email" type="email" required value="<?php if (isset($_SESSION["logged"]["email"])) echo $_SESSION["logged"]["email"]; ?>">
    </div>
    <div class="flex-half">
        <label for="phone">Telefónny kontakt</label>
        <input name="phone" type="text" required value="<?php if (isset($_SESSION["logged"]["phone"])) echo $_SESSION["logged"]["phone"]; ?>">
    </div>
    <div class="flex-half">
        <label for="first_name">Meno</label>
        <input name="first_name" type="text" required value="<?php if (isset($_SESSION["logged"]["first_name"])) echo $_SESSION["logged"]["first_name"]; ?>">
    </div>
    <div class="flex-half">
        <label for="last_name">Priezvisko</label>
        <input name="last_name" type="text" required value="<?php if (isset($_SESSION["logged"]["last_name"])) echo $_SESSION["logged"]["last_name"]; ?>">
    </div>
    <div class="flex-full">
        <label for="address">Ulica</label>
        <input name="address" type="text" required value="<?php if (isset($_SESSION["logged"]["address"])) echo $_SESSION["logged"]["address"]; ?>">
    </div>
    <div class="flex-q">
        <label for="zip_code">PSČ</label>
        <input name="zip_code" type="text" required value="<?php if (isset($_SESSION["logged"]["zip_code"])) echo $_SESSION["logged"]["zip_code"]; ?>">
    </div>
    <div class="flex-rest">
        <label for="city">Mesto</label>
        <input name="city" type="text" required value="<?php if (isset($_SESSION["logged"]["city"])) echo $_SESSION["logged"]["city"]; ?>">
    </div>
    <div class="flex-full"></div>
    <div class="flex-rest">
        <?php
            if (!empty($error)) {
                echo "<p class=\"error\"><span>⮾</span> $error</p>";
            }
        ?>
    </div>
    <div class="flex-q">
        <input type="submit" value="Aktualizovať">
    </div>
</form>