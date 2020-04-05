DROP DATABASE IF EXISTS bookstore;

CREATE DATABASE bookstore DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE bookstore;

CREATE TABLE books_statuses (
    id int NOT NULL AUTO_INCREMENT,
    status char(50) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE books (
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
    FOREIGN KEY (id_status) REFERENCES books_statuses(id),
    PRIMARY KEY (id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE authors (
    id int NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE books_authors (
    id_book int NOT NULL,
    id_author int NOT NULL,
    FOREIGN KEY (id_book) REFERENCES books(id),
    FOREIGN KEY (id_author) REFERENCES authors(id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE categories (
    id int NOT NULL AUTO_INCREMENT,
    category varchar(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE books_categories (
    id_book int NOT NULL,
    id_category int NOT NULL,
    FOREIGN KEY (id_book) REFERENCES books(id),
    FOREIGN KEY (id_category) REFERENCES categories(id)
) ENGINE=InnoDB CHARSET=utf8;
