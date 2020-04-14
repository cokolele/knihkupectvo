/*
    skript na importovanie dát z JSON súboru do našej MySQL databázy
    JSON som našiel na internete

    stolen from the internet
    https://github.com/bvaughn/infinite-list-reflow-examples/blob/master/books.json
*/

const mysql = require("mysql");
const books = require("./books.json");

const config = {
    host: "localhost",
    user: "root",
    password: "localrootpass",
    database: "bookstore",
    port: 3308
};

const asyncDBInit = (config) => new Promise((resolve, reject) => {
    const db = mysql.createConnection(config);
    db.connect(e => {
        if (e) {
            console.error("Error connecting to database: ", e);
            reject(e);
        } else {
            console.log("Connected to database as id: " + db.threadId);
            resolve(db);
        }
    });
});

const asyncQuery = (db, queryString, paramArray, cb) => new Promise((resolve, reject) => {
    const sql = mysql.format(queryString, paramArray);

    db.query(sql, (err, results) => {
        if(err) return reject(err);
        if (cb) cb();
        return resolve(results);
    });
});

async function main() {
    const db = await asyncDBInit(config);

    const book_required_properties = {
        title: null,
        isbn: null,
        pageCount: null,
        publishedDate: null,
        thumbnailUrl: "/knihkupectvo/images/missing.jpg",
        shortDescription: null,
        longDescription: null,
    }

    let statuses = [];
    let authors = [];
    let categories = [];

    let sql = "INSERT INTO book VALUES ";
    for (let book of books) {
        book = { ...book_required_properties, ...book };
        // cena = 20% z čísla od 5 do 90 + náhodná desatina + 1:2 šanca buď 9 stotín alebo 0 stotín
        book.cost = Math.floor( (Math.ceil((Math.random() * 85 + 5) * 0.2) + (Math.floor(Math.random() * 10) / 10) + (Math.random() > 0.66 ? 0 : 0.09)) * 100 ) / 100;
        // dostupnosť = 1:20 šanca buď 0 alebo číslo od 1 do 45
        book.availableCount = Math.random() > 0.95 ? 0 : Math.ceil(Math.random() * 45);
        // počet predaných = neprezradím
        book.soldCount = Math.floor( Math.random() * 1000 );
        if (book.publishedDate)
            book.publishedDate = new Date(book.publishedDate["$date"].slice(0,10)).getTime();

        if (!statuses.includes(book.status)) {
            await asyncQuery(db, "INSERT INTO book_status VALUES (0, ?)", [book.status]);
            statuses.push(book.status);
        }

        for (const category of book.categories)
            if (!categories.includes(category) && category)
                categories.push(category);

        for (const author of book.authors)
            if (!authors.includes(author) && author)
                authors.push(author);

        sql += mysql.format(
            "(0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?),",
            [book.title, book.isbn, book.pageCount, book.publishedDate, book.thumbnailUrl, book.shortDescription, book.longDescription, book.cost, book.availableCount, book.soldCount, statuses.indexOf(book.status) + 1]
        );
    }
    await asyncQuery(db, sql.slice(0, -1));

    sql = "INSERT INTO author VALUES ";
    for (const author of authors) {
        sql += mysql.format("(0, ?),", [author]);
    }
    await asyncQuery(db, sql.slice(0, -1));

    sql = "INSERT INTO book_author VALUES ";
    books.forEach((book, i) => {
        book.authors.forEach(author => {
            if (author)
                sql += mysql.format("(?, ?),", [i + 1, authors.indexOf(author) + 1]);
        });
    });
    await asyncQuery(db, sql.slice(0, -1));

    sql = "INSERT INTO category VALUES ";
    for (const category of categories) {
        sql += mysql.format("(0, ?),", [category]);
    }
    await asyncQuery(db, sql.slice(0, -1));

    sql = "INSERT INTO book_category VALUES ";
    books.forEach((book, i) => {
        book.categories.forEach(category => {
            if (category)
                sql += mysql.format("(?, ?),", [i + 1, categories.indexOf(category) + 1]);
        });
    });
    await asyncQuery(db, sql.slice(0, -1));

    db.end();
    console.log("done");
}

main();