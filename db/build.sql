DROP DATABASE IF EXISTS bookstore;

CREATE DATABASE bookstore DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE bookstore;

CREATE TABLE book_status (
    id int NOT NULL AUTO_INCREMENT,
    status char(50) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE book (
    id int NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    isbn char(13),
    page_count smallint,
    publish_date bigint,
    thumbnail_url varchar(155),
    description_short text,
    description_long text,

    cost float(2) NOT NULL,
    available_count smallint NOT NULL,
    sold_count int NOT NULL,

    id_status int NOT NULL,
    FOREIGN KEY (id_status) REFERENCES book_status(id),
    PRIMARY KEY (id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE author (
    id int NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE book_author (
    id_book int NOT NULL,
    id_author int NOT NULL,
    FOREIGN KEY (id_book) REFERENCES book(id),
    FOREIGN KEY (id_author) REFERENCES author(id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE category (
    id int NOT NULL AUTO_INCREMENT,
    category varchar(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE book_category (
    id_book int NOT NULL,
    id_category int NOT NULL,
    FOREIGN KEY (id_book) REFERENCES book(id),
    FOREIGN KEY (id_category) REFERENCES category(id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE customer (
    id int NOT NULL AUTO_INCREMENT,
    username varchar(55) NOT NULL,
    password char(60) NOT NULL,
    admin boolean NOT NULL,
    email varchar(100) NOT NULL,
    phone varchar(13),
    first_name varchar(35),
    last_name varchar(35),
    address varchar(55),
    zip_code char(5),
    city varchar(35),
    PRIMARY KEY (id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE shipping_method (
    id int NOT NULL AUTO_INCREMENT,
    method varchar(35) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB  CHARSET=utf8;

INSERT INTO shipping_method VALUES
    (0, "Osobný odber na predajni"),
    (0, "Na poštu - Slovenská pošta"),
    (0, "Dobierkou - Slovenská pošta"),
    (0, "Dobierkou - DHL");

CREATE TABLE payment_method (
    id int NOT NULL AUTO_INCREMENT,
    method varchar(35) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB  CHARSET=utf8;

INSERT INTO payment_method VALUES
    (0, "Pri odbere"),
    (0, "Bankovým prevodom"),
    (0, "VISA"),
    (0, "PayPal");

CREATE TABLE transaction (
    id int NOT NULL AUTO_INCREMENT,
    date bigint NOT NULL,
    final_cost float(2) NOT NULL,

    id_customer int NOT NULL,
    id_shipping_method int NOT NULL,
    id_payment_method int NOT NULL,
    FOREIGN KEY (id_customer) REFERENCES customer(id),
    FOREIGN KEY (id_shipping_method) REFERENCES shipping_method(id),
    FOREIGN KEY (id_payment_method) REFERENCES payment_method(id),
    PRIMARY KEY (id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE transaction_book (
    id_transaction int NOT NULL,
    id_book int NOT NULL,
    FOREIGN KEY (id_transaction) REFERENCES transaction(id),
    FOREIGN KEY (id_book) REFERENCES book(id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE cart (
    id_customer int NOT NULL,
    id_book int NOT NULL,
    FOREIGN KEY (id_customer) REFERENCES customer(id),
    FOREIGN KEY (id_book) REFERENCES book(id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE VIEW view_books_complete AS SELECT book.id, title, isbn, page_count, publish_date, thumbnail_url, description_short, description_long, cost, available_count, sold_count, book_status.status AS status, GROUP_CONCAT(DISTINCT(author.name) SEPARATOR "|") AS authors, GROUP_CONCAT(DISTINCT(category.category) SEPARATOR "|") AS categories FROM book
    JOIN book_status ON book.id_status = book_status.id
    JOIN book_author ON book.id = book_author.id_book
    JOIN author ON book_author.id_author = author.id
    JOIN book_category ON book.id = book_category.id_book
    JOIN category ON book_category.id_category = category.id
    GROUP BY book.id;

CREATE VIEW view_books_complete_values AS SELECT book.id, title, isbn, page_count, publish_date, thumbnail_url, description_short, description_long, cost, available_count, sold_count, book_status.id AS status, GROUP_CONCAT(DISTINCT(author.id) SEPARATOR "|") AS authors, GROUP_CONCAT(DISTINCT(category.id) SEPARATOR "|") AS categories FROM book
    JOIN book_status ON book.id_status = book_status.id
    JOIN book_author ON book.id = book_author.id_book
    JOIN author ON book_author.id_author = author.id
    JOIN book_category ON book.id = book_category.id_book
    JOIN category ON book_category.id_category = category.id
    GROUP BY book.id;

CREATE VIEW view_categories_complete AS SELECT category FROM category;

CREATE VIEW view_customers_basic AS SELECT id, username, password, email FROM customer;

CREATE VIEW view_customers_complete AS SELECT customer.id, username, password, admin, email, phone, first_name, last_name, address, zip_code, city, COUNT(transaction.id) AS transactions_count FROM customer
    LEFT JOIN transaction ON transaction.id_customer=customer.id
    GROUP BY customer.id;

CREATE VIEW view_transactions_complete AS SELECT transaction.id, id_customer, transaction.date, final_cost, shipping_method.method AS shipping_method, payment_method.method AS payment_method, GROUP_CONCAT(book.id SEPARATOR '|') AS books_id, GROUP_CONCAT(book.title SEPARATOR "|") AS books_title, GROUP_CONCAT(book.cost SEPARATOR "|") AS books_cost FROM transaction
    JOIN shipping_method ON transaction.id_shipping_method = shipping_method.id
    JOIN payment_method ON transaction.id_payment_method = payment_method.id
    JOIN transaction_book ON transaction.id = transaction_book.id_transaction
    JOIN book ON transaction_book.id_book = book.id
    GROUP BY transaction.id;

CREATE VIEW view_carts_complete AS SELECT id_customer, view_books_complete.id, title, isbn, page_count, publish_date, thumbnail_url, description_short, description_long, cost, available_count, sold_count, status, authors, categories FROM cart
    JOIN view_books_complete ON id_book = view_books_complete.id;

/*
testing
*/

INSERT INTO customer VALUES
    (0, "root", "$2y$10$0csBVDNa9eewWcKLXKxute90MAWOp2GG65iN2MBI1opwG0n/bFF0W", true, "email@email", null, null, null, null, null, null),
    (0, "priklad", "$2y$10$Die4MCpG3Lo/mo6qIfnAd.YGX1JCmFlz1cGT/Cwt9Pt6/IXy0ogi6", false, "priklad@priklad.sk", "+421901234567", "Príklad", "Príkladovič", "Príkladová 1", "01337", "Príkladovo");

INSERT INTO cart VALUES
    (2, 105),
    (2, 115);

INSERT INTO transaction VALUES
    (0, 1, 420.69, 1, 1, 1),
    (0, 1, 420.69, 2, 1, 1);

INSERT INTO transaction_book VALUES
    (1, 10),
    (1, 20),
    (1, 30),
    (1, 40),
    (2, 155),
    (2, 165);