<?php
$conn = new mysqli("localhost", "root", "", "athena_library");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if tables exist
$tables = ['users', 'books', 'borrowed_books'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        echo "Table '$table' does not exist. Please run setup_database.php first.<br>";
    } else {
        echo "Table '$table' exists.<br>";
    }
}

// Check if there are any books
$result = $conn->query("SELECT COUNT(*) as count FROM books");
$row = $result->fetch_assoc();
echo "Number of books in database: " . $row['count'] . "<br>";

// Check if there are any borrowed books for current user
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT COUNT(*) as count FROM borrowed_books WHERE user_id = $user_id");
    $row = $result->fetch_assoc();
    echo "Number of books borrowed by you: " . $row['count'] . "<br>";
}

$conn->close();
?> 