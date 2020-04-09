<?php
    $config = [
        "host" => "localhost",
        "port" => "3308",
        "user" => "root",
        "password" => "localrootpass",
        "database" => "bookstore",
        "charset" => "utf8"
    ];
    $options = [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    $db;

    try {
        $db = new PDO("mysql:host=" . $config["host"] . ";port=" . $config["port"] . ";dbname=" . $config["database"] . ";charset=" . $config["charset"], $config["user"], $config["password"], $options);
    }
    catch (PDOException $e) {
        $db = $e;
    }
?>