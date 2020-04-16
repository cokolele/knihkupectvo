<?php
    require_once("../includes/session.php");

    function onError() {
        header("Location:  /knihkupectvo");
        exit;
    }

    if (!isset($_SESSION["logged"]) || !$_SESSION["logged"]["admin"])
        onError();

    require_once("../includes/utils.php");
    require_once("../includes/db.php");

    $book = [
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
        "authors" => [0],
        "categories" => [0]
    ];
    $id = null;

    if (isset($_GET["id"])) {
        $id = filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT);

        $stmt = $db->prepare("SELECT * FROM view_books_complete_values WHERE id = ?");
        $stmt->execute([$id]);
        $_book = $stmt->fetchAll();

        if (!empty($_book)) {
            $book = array_merge($book, $_book[0]);
            $book["authors"] = explode("|", $book["authors"]);
            $book["categories"] = explode("|", $book["categories"]);
        }
    }

    $results = $db->query("SELECT * FROM category ORDER BY category");
    $categories = $results->fetchAll();

    $results = $db->query("SELECT * FROM author ORDER BY name");
    $authors = $results->fetchAll();

    $results = $db->query("SELECT * FROM book_status");
    $statuses = $results->fetchAll();
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
    <?php
        if (isset($_GET["id"]))
            echo "<div class=\"text-center title-big\">Úprava knihy</div>";
        else
            echo "<div class=\"text-center title-big\">Pridanie knihy</div>";
    ?>
    <form class="bookpage-edit-container">
        <?php
            $status_options = "";
            foreach ($statuses as $status) {
                $status_options .= "<option value=\"" . $status["id"] . "\" " . ($status["id"] == $book["status"] ? "selected" : "") . ">" . $status["status"] . "</option>";
            }

            $i = 0;
            $authors_html = "";
            foreach ($book["authors"] as $book_author) {
                $options = "<option value=\"\"></option>";
                foreach ($authors as $author) {
                    $options .= "<option value=\"" . $author["id"] . "\"" . ($author["id"] == $book_author ? "selected" : "") . ">" . $author["name"] . "</option>";
                }
                $label = ($i++ == 0 ? "<label for=\"authors\">Autori</label>" : "<label></label>");
                $buttons = ($i++ == 1 ? "<button type=\"button\" data-add-row=\"true\" data-name=\"authors\" data-add-text=\"false\">+ pridať</button>\n<button type=\"button\" data-add-row=\"true\" data-name=\"authors\" data-add-text=\"true\">+ nová</button>" : "");
                $authors_html .= <<<EOF
        <div>
            {$label}
            <div class="inline-inputs">
                <select id="authors" name="authors">
                  {$options}
                </select>
                {$buttons}
            </div>
        </div>
EOF;
            }

            $i = 0;
            $categories_html = "";
            foreach ($book["categories"] as $book_category) {
                $options = "<option value=\"\"></option>";
                foreach ($categories as $category) {
                    $options .= "<option value=\"" . $category["id"] . "\"" . ($category["id"] == $book_category ? "selected" : "") . ">" . $category["category"] . "</option>";
                }
                $label = ($i++ == 0 ? "<label for=\"categories\">Kategórie</label>" : "<label></label>");
                $buttons = ($i++ == 1 ? "<button type=\"button\" data-add-row=\"true\" data-name=\"categories\" data-add-text=\"false\">+ pridať</button>\n<button type=\"button\" data-add-row=\"true\" data-name=\"categories\" data-add-text=\"true\">+ nová</button>" : "");
                $categories_html .= <<<EOF
        <div>
            {$label}
            <div class="inline-inputs">
                <select id="categories" name="categories">
                  {$options}
                </select>
                {$buttons}
            </div>
        </div>
EOF;
            }


            echo <<<EOF
        <input type="hidden" name="id" value="{$id}">
        <div>
            <label for="title">Názov</label>
            <input id="title" name="title" type="text" value="{$book["title"]}" required>
        </div>
        <div>
            <label for="isbn">ISBN</label>
            <input id="isbn" name="isbn" type="text" value="{$book["isbn"]}">
        </div>
        <div>
            <label for="status">Status</label>
            <div class="inline-inputs">
                <select id="status" name="status" required>
                  {$status_options}
                </select>
                alebo
                <button type="button" data-add-row="false" data-name="status">+ nový</button>
            </div>
        </div>
        {$authors_html}
        {$categories_html}
        <div>
            <label for="page_count">Počet strán</label>
            <input id="page_count" name="page_count" type="number" value="{$book["page_count"]}">
        </div>
        <div>
            <label for="publish_date">Dátum vydania</label>
            <input id="publish_date" name="publish_date" type="date" value="{$book["publish_date"]}">
        </div>
        <div>
            <label for="thumbnail_url">thumbnail URL</label>
            <input id="thumbnail_url" name="thumbnail_url" type="text" value="{$book["thumbnail_url"]}">
        </div>
        <div>
            <label for="description_short">Popisok - krátky</label>
            <textarea id="description_short" name="description_short">{$book["description_short"]}</textarea>
        </div>
        <div>
            <label for="description_long">Popisok - dlhý</label>
            <textarea id="description_long" name="description_long">{$book["description_long"]}</textarea>
        </div>
        <div>
            <label for="cost">Cena</label>
            <input id="cost" name="cost" type="number" value="{$book["cost"]}" required>
        </div>
        <div>
            <label for="available_count">Počet kusov na sklade</label>
            <input id="available_count" name="available_count" type="number" value="{$book["available_count"]}" required>
        </div>
EOF;
        ?>
        <input class="order-continue a-button" type="submit" value="Potvrdiť">
    </form>
    <script>
        document.querySelectorAll(".bookpage-edit-container .inline-inputs button").forEach(e => {
            e.addEventListener("click", () => {
                if (e.getAttribute("data-add-row") == "false") {
                    const parent = e.parentElement;
                    parent.innerHTML = `<input id="${ e.getAttribute("data-name") }" name="${ e.getAttribute("data-name") }" type="text" required>`;
                } else {
                    if (e.getAttribute("data-add-text") == "false") {
                        const parent = e.parentElement.parentElement;
                        const clone = parent.cloneNode(true);
                        clone.children[0].innerHTML = "";
                        clone.children[1].removeChild(clone.children[1].children[1]);
                        clone.children[1].removeChild(clone.children[1].children[1]);
                        parent.parentElement.insertBefore(clone, parent.nextSibling);
                    } else {
                        const parent = e.parentElement.parentElement;
                        const row = document.createElement("div");
                        row.innerHTML = `
                            <label></label>
                            <input id="${ e.getAttribute("data-name") }" name="${ e.getAttribute("data-name") }" type="text">
                        `;
                        parent.parentElement.insertBefore(row, parent.nextSibling);
                    }
                }
            })
        });

        document.querySelector("form.bookpage-edit-container").addEventListener("submit", (e) => {
            e.preventDefault();

            const data = new FormData(e.target);
            data.set("authors", data.getAll("authors").join("|"))
            data.set("categories", data.getAll("categories").join("|"))

            const params = new URLSearchParams(data).toString();
            window.location.href = "/knihkupectvo/admin/add_book_process.php?" + params;
        });
    </script>
</body>
</html>