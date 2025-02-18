--
-- Use a specific schema and set it as default - thingy.
--
DROP SCHEMA IF EXISTS lbaw2416 CASCADE;
CREATE SCHEMA IF NOT EXISTS lbaw2416;
SET search_path TO lbaw2416;

DROP TABLE IF EXISTS bookInCart CASCADE;
DROP TABLE IF EXISTS "comment" CASCADE;
DROP TABLE IF EXISTS administrator CASCADE;
DROP TABLE IF EXISTS "order" CASCADE;
DROP TABLE IF EXISTS shoppingCart CASCADE;
DROP TABLE IF EXISTS wishlist CASCADE;
DROP TABLE IF EXISTS address CASCADE;
DROP TABLE IF EXISTS country CASCADE;
DROP TABLE IF EXISTS category CASCADE;
DROP TABLE IF EXISTS author CASCADE;
DROP TABLE IF EXISTS book CASCADE;
DROP TABLE IF EXISTS "user" CASCADE;
DROP TABLE IF EXISTS book_Author CASCADE;


DROP TYPE IF EXISTS orderstatus CASCADE;
DROP TYPE IF EXISTS formattype CASCADE;


DROP DOMAIN IF EXISTS rating CASCADE;
DROP DOMAIN IF EXISTS price CASCADE;


CREATE TYPE orderstatus AS ENUM('PENDING', 'SHIPPED', 'COMPLETED');
CREATE TYPE formattype AS ENUM('HARDCOVER', 'PAPERBACK', 'EBOOK');


CREATE DOMAIN rating AS DECIMAL(2, 1) 
CHECK (VALUE >= 0 AND VALUE <= 10);

CREATE DOMAIN price AS DECIMAL(10,2);


CREATE TABLE "user" (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    status BOOLEAN NOT NULL
);

CREATE TABLE administrator (
    id INT PRIMARY KEY,
    userId INT NOT NULL,
    FOREIGN KEY (userId) REFERENCES "user"(id)
);

CREATE TABLE category (
    id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE author (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE book (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    score rating,
    format formattype NOT NULL,
    status BOOLEAN NOT NULL,
    price price CHECK (price >= 0) NOT NULL,
    categoryId INT NOT NULL,
    image VARCHAR(255),
    FOREIGN KEY (categoryId) REFERENCES category(id)
);

CREATE TABLE book_author (
    id_book INT NOT NULL,
    id_author INT NOT NULL,
    PRIMARY KEY (id_book, id_author),
    FOREIGN KEY (id_book) REFERENCES book(id),
    FOREIGN KEY (id_author) REFERENCES author(id)
);

CREATE TABLE "review" (
    id SERIAL PRIMARY KEY,
    score INT NULL CHECK (score BETWEEN 0 AND 5),
    content TEXT NOT NULL,
    userId INT NOT NULL,
    bookId INT NOT NULL,
    FOREIGN KEY (userId) REFERENCES "user"(id),
    FOREIGN KEY (bookId) REFERENCES book(id) ON DELETE CASCADE
);

CREATE TABLE country (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE address (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    street VARCHAR(255) NOT NULL,
    number VARCHAR(10) NOT NULL,
    code VARCHAR(20) NOT NULL,
    city VARCHAR(100) NOT NULL,
    countryId INT NOT NULL,
    FOREIGN KEY (countryId) REFERENCES country(id)
);

CREATE TABLE shoppingCart (
    id SERIAL PRIMARY KEY,
    price price NOT NULL,
    userId INT NOT NULL,
    FOREIGN KEY (userId) REFERENCES "user"(id) ON DELETE CASCADE
);

CREATE TABLE bookInCart (
    id SERIAL PRIMARY KEY,
    cartId INT NOT NULL,
    bookId INT NOT NULL,
    quantity INT CHECK (quantity > 0) NOT NULL,
    price price NOT NULL,
    FOREIGN KEY (cartId) REFERENCES shoppingCart(id) ON DELETE CASCADE,
    FOREIGN KEY (bookId) REFERENCES book(id)
);

CREATE TABLE "order" (
    id SERIAL PRIMARY KEY,
    status orderstatus NOT NULL,
    purchaseDate DATE DEFAULT CURRENT_DATE NOT NULL,
    totalPrice price CHECK (totalPrice >= 0) NOT NULL,
    userId INT NOT NULL,
    addressId INT NOT NULL,
    FOREIGN KEY (userId) REFERENCES "user"(id),
    FOREIGN KEY (addressId) REFERENCES address(id)
);

CREATE TABLE book_order (
    id SERIAL PRIMARY KEY,
    orderId INT NOT NULL,
    bookId INT,
    quantity INT CHECK (quantity > 0) NOT NULL,
    price price NOT NULL,
    FOREIGN KEY (orderId) REFERENCES "order"(id) ON DELETE CASCADE,
    FOREIGN KEY (bookId) REFERENCES book(id)
);

CREATE TABLE wishlist (
    userId INT NOT NULL,
    bookId INT NOT NULL,
    PRIMARY KEY (userId, bookId),
    FOREIGN KEY (userId) REFERENCES "user"(id),
    FOREIGN KEY (bookId) REFERENCES book(id)
);

-- Authors
INSERT INTO author (name) VALUES
('J.K. Rowling'),
('George Orwell'),
('F. Scott Fitzgerald'),
('J.D. Salinger'),
('Aldous Huxley'),
('Frank Herbert'),
('J.R.R. Tolkien'),
('Agatha Christie'),
('Jane Austen'),
('Harper Lee'),
('John Green'),
('Dan Brown'),
('Gillian Flynn'),
('Stephen Hawking'),
('Paulo Coelho'),
('Stephen King'),
('Suzanne Collins'),
('Homer'),
('William Goldman'),
('Margaret Atwood');

-- Categorie table
INSERT INTO category (id, name) VALUES
(1, 'Fantasy'),
(2, 'Dystopian'),
(3, 'Classic'),
(4, 'Science Fiction'),
(5, 'Mystery'),
(6, 'Romance'),
(7, 'Non-fiction');

-- Country table
INSERT INTO country (name) VALUES
('Austria'),
('Belgium'),
('Denmark'),
('France'),
('Germany'),
('Italy'),
('Poland'),
('Portugal'),
('Spain'),
('Sweden');

-- Book table
INSERT INTO book (title, score, format, status, price, categoryId, image) VALUES
('Harry Potter and the Philosopher Stone', 9.5, 'HARDCOVER', TRUE, 19.99, 1, 'images/books/book1.jpg'),
('1984', 8.5, 'PAPERBACK', TRUE, 14.99, 2, 'images/books/book2.jpg'),
('The Great Gatsby', 9.0, 'HARDCOVER', TRUE, 10.99, 3, 'images/books/book3.jpg'),
('The Catcher in the Rye', 8.0, 'PAPERBACK', TRUE, 12.99, 3, 'images/books/book4.jpg'),
('Brave New World', 9.2, 'EBOOK', TRUE, 13.49, 2, 'images/books/book5.jpg'),
('Dune', 9.8, 'HARDCOVER', TRUE, 25.99, 4, 'images/books/book6.jpg'),
('The Hobbit', 9.3, 'PAPERBACK', TRUE, 15.99, 1, 'images/books/book7.jpg'),
('Murder on the Orient Express', 9.1, 'HARDCOVER', TRUE, 20.00, 5, 'images/books/book8.jpg'),
('Pride and Prejudice', 9.7, 'PAPERBACK', TRUE, 11.99, 6, 'images/books/book9.jpg'),
('To Kill a Mockingbird', 9.0, 'HARDCOVER', TRUE, 17.99, 3, 'images/books/book10.jpg'),
('The Fault in Our Stars', 8.8, 'PAPERBACK', TRUE, 9.99, 6, 'images/books/book11.jpg'),
('The Martian', 9.5, 'EBOOK', TRUE, 10.99, 4, 'images/books/book12.jpg'),
('The Da Vinci Code', 8.7, 'PAPERBACK', TRUE, 16.49, 5, 'images/books/book13.jpg'),
('Gone Girl', 9.3, 'HARDCOVER', TRUE, 19.99, 5, 'images/books/book14.jpg'),
('A Brief History of Time', 9.2, 'HARDCOVER', TRUE, 22.00, 7, 'images/books/book15.jpg'),
('The Alchemist', 8.9, 'PAPERBACK', TRUE, 14.49, 6, 'images/books/book16.jpg'),
('The Shining', 9.0, 'HARDCOVER', TRUE, 18.99, 5, 'images/books/book17.jpg'),
('The Hunger Games', 9.0, 'EBOOK', TRUE, 12.49, 2, 'images/books/book18.jpg'),
('The Odyssey', 8.8, 'PAPERBACK', TRUE, 10.49, 3, 'images/books/book19.jpg'),
('The Princess Bride', 9.1, 'HARDCOVER', TRUE, 13.99, 1, 'images/books/book20.jpg');


INSERT INTO book_author (id_book, id_author) VALUES
(1, 1),  -- Harry Potter and the Philosopher Stone by J.K. Rowling
(2, 2),  -- 1984 by George Orwell
(3, 3),  -- The Great Gatsby by F. Scott Fitzgerald
(4, 4),  -- The Catcher in the Rye by J.D. Salinger
(5, 2),  -- Brave New World by Aldous Huxley
(6, 5),  -- Dune by Frank Herbert
(7, 6),  -- The Hobbit by J.R.R. Tolkien
(8, 7),  -- Murder on the Orient Express by Agatha Christie
(9, 8),  -- Pride and Prejudice by Jane Austen
(10, 9), -- To Kill a Mockingbird by Harper Lee
(11, 10), -- The Fault in Our Stars by John Green
(12, 5),  -- The Martian by Andy Weir
(13, 11), -- The Da Vinci Code by Dan Brown
(14, 12), -- Gone Girl by Gillian Flynn
(15, 13), -- A Brief History of Time by Stephen Hawking
(16, 14), -- The Alchemist by Paulo Coelho
(17, 15), -- The Shining by Stephen King
(18, 16), -- The Hunger Games by Suzanne Collins
(19, 17), -- The Odyssey by Homer
(20, 18); -- The Princess Bride by William Goldman
