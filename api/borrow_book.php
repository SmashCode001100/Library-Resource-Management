<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$book_id = $data['book_id'] ?? null;

if (!$book_id) {
    echo json_encode(['success' => false, 'message' => 'Book ID is required']);
    exit();
}

try {
    // Check if book is available
    $check_query = "SELECT status FROM books WHERE id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $book_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $book = $result->fetch_assoc();

    if (!$book) {
        echo json_encode(['success' => false, 'message' => 'Book not found']);
        exit();
    }

    if ($book['status'] !== 'available') {
        echo json_encode(['success' => false, 'message' => 'Book is not available']);
        exit();
    }

    // Start transaction
    $conn->begin_transaction();

    // Insert borrowing record
    $borrow_query = "INSERT INTO borrowings (user_id, book_id, borrow_date) VALUES (?, ?, NOW())";
    $borrow_stmt = $conn->prepare($borrow_query);
    $borrow_stmt->bind_param("ii", $_SESSION['user_id'], $book_id);
    $borrow_stmt->execute();

    // Update book status
    $update_query = "UPDATE books SET status = 'borrowed' WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("i", $book_id);
    $update_stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Book borrowed successfully']);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Borrow book error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while borrowing the book']);
} 