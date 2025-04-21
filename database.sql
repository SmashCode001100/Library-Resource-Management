-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS athena_library;
USE athena_library;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    description TEXT,
    cover_image VARCHAR(255),
    status ENUM('available', 'borrowed') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Borrowings table
CREATE TABLE IF NOT EXISTS borrowings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    return_date TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- Rename the table to match the PHP code
RENAME TABLE borrowings TO borrowed_books;

-- Saved books table
CREATE TABLE IF NOT EXISTS saved_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id),
    UNIQUE KEY unique_save (user_id, book_id)
);

-- Insert some sample books
INSERT INTO books (title, author, description, cover_image) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', 'A story of the fabulously wealthy Jay Gatsby and his love for the beautiful Daisy Buchanan.', 'https://example.com/gatsby.jpg'),
('To Kill a Mockingbird', 'Harper Lee', 'The story of racial injustice and the loss of innocence in the American South.', 'https://example.com/mockingbird.jpg'),
('1984', 'George Orwell', 'A dystopian social science fiction novel and cautionary tale.', 'https://example.com/1984.jpg'),
('Pride and Prejudice', 'Jane Austen', 'A romantic novel of manners.', 'https://example.com/pride.jpg'),
('The Hobbit', 'J.R.R. Tolkien', 'The adventure of Bilbo Baggins, a hobbit who embarks on a quest.', 'https://example.com/hobbit.jpg');

<?php
$update_status = $conn->prepare("UPDATE books SET status = 'borrowed' WHERE id = ?");
$update_status->bind_param("i", $book_id);
$update_status->execute();
$update_status->close();
?>