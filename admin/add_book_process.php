<?php
    require_once("../includes/session.php");
    require_once("../includes/utils.php");
    require_once("../includes/db.php");

    function invalid_data() {
        global $db;

        header("Location:  /knihkupectvo");
        exit;
    }

    if (!isset($_SESSION["logged"]) || !$_SESSION["logged"]["admin"])
        invalid_data();

    try {
        $db->beginTransaction();

        $defaultBook = [
            "id" => null,
            "title" => null,
            "isbn" => null,
            "page_count" => null,
            "publish_date" => null,
            "thumbnail_url" => "/knihkupectvo/images/missing.jpg",
            "description_short" => null,
            "description_long" => null,
            "cost" => null,
            "available_count" => null,
            "sold_count" => null,
            "status" => null,
            "authors" => [],
            "categories" => []
        ];

        foreach ($_GET as $key => $value) {
            if (empty($value) || empty(str_replace("|", "", $value)))
                $_GET[$key] = null;
        }

        $book = array_merge($defaultBook, $_GET);

        var_dump($book);

        if (empty($book["title"]) || empty($book["cost"]) || empty($book["available_count"]) || empty($book["status"]))
            invalid_data();

        $stmt = $db->prepare("SELECT * FROM view_books_complete_values WHERE id = ?");
        $stmt->execute([$book["id"]]);
        $exists = $stmt->fetchAll();

        $results = $db->query("SELECT * FROM category");
        $categories = $results->fetchAll();

        $results = $db->query("SELECT * FROM author");
        $authors = $results->fetchAll();

        $results = $db->query("SELECT * FROM book_status");
        $statuses = $results->fetchAll();

        if (!empty($exists)) {
            $stmt = $db->prepare("DELETE FROM book_author WHERE id_book = ?");
            $stmt->execute([$book["id"]]);

            if (!empty($book["authors"]))
                foreach (explode("|", $book["authors"]) as $book_author) {
                    if (empty($book_author))
                        continue;

                    $author_exists = false;
                    $author_id = null;
                    foreach ($authors as $author) {
                        if ($author["id"] == $book_author || $book_author == $author["name"]) {
                            $author_exists = true;
                            $author_id= $author["id"];
                        }
                    }

                    if ($author_exists) {
                        $stmt = $db->prepare("INSERT INTO book_author VALUES (?, ?)");
                        $stmt->execute([$book["id"], $author_id]);
                    } else {
                        $stmt = $db->prepare("INSERT INTO author VALUES (0, ?)");
                        $stmt->execute([$book_author]);
                        $author_id = $db->lastInsertId();

                        $stmt = $db->prepare("INSERT INTO book_author VALUES (?, ?)");
                        $stmt->execute([$book["id"], $author_id]);
                    }
                }

            $stmt = $db->prepare("DELETE FROM book_category WHERE id_book = ?");
            $stmt->execute([$book["id"]]);

            if (!empty($book["categories"]))
                foreach (explode("|", $book["categories"]) as $book_category) {
                    if (empty($book_category))
                        continue;

                    $category_exists = false;
                    $category_id = null;

                    foreach ($categories as $category) {
                        if ($category["id"] == $book_category || $book_category == $category["category"]) {
                            $category_exists = true;
                            $category_id = $category["id"];
                        }
                    }

                    if ($category_exists) {
                        $stmt = $db->prepare("INSERT INTO book_category VALUES (?, ?)");
                        $stmt->execute([$book["id"], $category_id]);
                    } else {
                        $stmt = $db->prepare("INSERT INTO category VALUES (0, ?)");
                        $stmt->execute([$book_category]);
                        $category_id = $db->lastInsertId();

                        $stmt = $db->prepare("INSERT INTO book_category VALUES (?, ?)");
                        $stmt->execute([$book["id"], $category_id]);
                    }
                }

            $status_exists = false;
            $status_id = null;
            foreach ($statuses as $status) {
                if ($book["status"] == $status["id"] || $book["status"] == $status["status"]) {
                    $status_exists = true;
                    $status_id = $status["id"];
                }
            }

            if (!$status_exists) {
                $stmt = $db->prepare("INSERT INTO book_status VALUES (0, ?)");
                $stmt->execute([$book["status"]]);
                $status_id = $db->lastInsertId();
            }

            if (empty($book["publish_date"]))
                $book["publish_date"] = 0;
            else
                $book["publish_date"] = strtotime($book["publish_date"]);

            $stmt = $db->prepare("UPDATE book SET title = ?, isbn = ?, page_count = ?, publish_date = ?, thumbnail_url = ?, description_short = ?, description_long = ?, cost = ?, available_count = ?, id_status = ? WHERE id = ?");
            $stmt->execute([$book["title"], $book["isbn"], $book["page_count"], $book["publish_date"], $book["thumbnail_url"], $book["description_short"], $book["description_long"], $book["cost"], $book["available_count"], $status_id, $book["id"]]);
        }
        else
        {
            //http://localhost:8080/knihkupectvo/admin/add_book_process.php?id=&title=Moj+kamo%C5%A1&isbn=6969&status=Lalala&authors=%7CMoj+kejmo%C5%A1&categories=%7CMojho+kejmo%C5%A1a&page_count=58&publish_date=2020-04-25&thumbnail_url=%2Fknihkupectvo%2Fimages%2Fmissing.jpg&description_short=hmm&description_long=hmmmmm&cost=5&available_count=5

            $status_exists = false;
            $status_id = null;
            foreach ($statuses as $status) {
                if ($book["status"] == $status["id"] || $book["status"] == $status["status"]) {
                    $status_exists = true;
                    $status_id = $status["id"];
                }
            }

            if (!$status_exists) {
                $stmt = $db->prepare("INSERT INTO book_status VALUES (0, ?)");
                $stmt->execute([$book["status"]]);
                $status_id = $db->lastInsertId();
            }

            if (empty($book["publish_date"]))
                $book["publish_date"] = 0;
            else
                $book["publish_date"] = strtotime($book["publish_date"]);

            $stmt = $db->prepare("INSERT INTO book VALUES (0, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)");
            $stmt->execute([$book["title"], $book["isbn"], $book["page_count"], $book["publish_date"], $book["thumbnail_url"], $book["description_short"], $book["description_long"], $book["cost"], $book["available_count"], $status_id]);
            $book["id"] = $db->lastInsertId();

            if (!empty($book["authors"]))
                foreach (explode("|", $book["authors"]) as $book_author) {
                    if (empty($book_author))
                        continue;

                    $author_exists = false;
                    $author_id = null;
                    foreach ($authors as $author) {
                        if ($author["id"] == $book_author || $book_author == $author["name"]) {
                            $author_exists = true;
                            $author_id= $author["id"];
                        }
                    }

                    if ($author_exists) {
                        $stmt = $db->prepare("INSERT INTO book_author VALUES (?, ?)");
                        $stmt->execute([$book["id"], $author_id]);
                    } else {
                        $stmt = $db->prepare("INSERT INTO author VALUES (0, ?)");
                        $stmt->execute([$book_author]);
                        $author_id = $db->lastInsertId();

                        $stmt = $db->prepare("INSERT INTO book_author VALUES (?, ?)");
                        $stmt->execute([$book["id"], $author_id]);
                    }
                }

            if (!empty($book["categories"]))
                foreach (explode("|", $book["categories"]) as $book_category) {
                    if (empty($book_category))
                        continue;

                    $category_exists = false;
                    $category_id = null;

                    foreach ($categories as $category) {
                        if ($category["id"] == $book_category || $book_category == $category["category"]) {
                            $category_exists = true;
                            $category_id = $category["id"];
                        }
                    }

                    if ($category_exists) {
                        $stmt = $db->prepare("INSERT INTO book_category VALUES (?, ?)");
                        $stmt->execute([$book["id"], $category_id]);
                    } else {
                        $stmt = $db->prepare("INSERT INTO category VALUES (0, ?)");
                        $stmt->execute([$book_category]);
                        $category_id = $db->lastInsertId();

                        $stmt = $db->prepare("INSERT INTO book_category VALUES (?, ?)");
                        $stmt->execute([$book["id"], $category_id]);
                    }
                }
        }

        $db->commit();
        header("Location:  /knihkupectvo/book.php?id=_" . $book["id"]);
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        invalid_data();
    }

    echo "ok";
?>