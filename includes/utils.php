<?php

function strip_punctuation($string) {
    return strtolower(preg_replace('/\W+/', '_', $string));
}

function template_book_showcase($book) {
    $author = explode(",", $book["authors"])[0];
    $cost = $book["available_count"] == 0 ? "vypredané" : $book["cost"] . " €";
    $url_name = strip_punctuation($book["title"]) . "_" . $book["id"];
    echo <<<EOF
        <div class="book-showcase">
            <img class="book-showcase-img" src="{$book["thumbnail_url"]}" alt="">
            <div class="book-showcase-info">
                <span class="book-showcase-author">{$author}</span>
                <a href="/knihkupectvo/book.php?name={$url_name}" class="book-showcase-title">{$book["title"]}</a>
                <span class="book-showcase-cost">{$cost}</span>
            </div>
        </div>
EOF;
            }

?>