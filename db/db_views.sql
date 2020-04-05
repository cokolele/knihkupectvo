CREATE VIEW view_books_complete AS SELECT books.id, title, isbn, page_count, publish_date, thumbnail_url, description_short, description_long, cost, available_count, sold_count, books_statuses.status AS status, GROUP_CONCAT(authors.name SEPARATOR ',') AS authors, GROUP_CONCAT(categories.category SEPARATOR ',') AS categories FROM books
    JOIN books_statuses ON books.id_status=books_statuses.id
    JOIN books_authors ON books.id=books_authors.id_book
    JOIN authors ON books_authors.id_author=authors.id
    JOIN books_categories ON books.id=books_categories.id_book
    JOIN categories ON books_categories.id_category=categories.id
    GROUP BY books.title;