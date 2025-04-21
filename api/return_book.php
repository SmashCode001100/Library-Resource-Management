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
    // Check if book is borrowed by the user
    $check_query = "SELECT b.id FROM books b 
                   JOIN borrowings br ON b.id = br.book_id 
                   WHERE b.id = ? AND br.user_id = ? AND br.return_date IS NULL";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $book_id, $_SESSION['user_id']);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if (!$result->fetch_assoc()) {
        echo json_encode(['success' => false, 'message' => 'Book is not borrowed by you']);
        exit();
    }

    // Start transaction
    $conn->begin_transaction();

    // Update borrowing record
    $return_query = "UPDATE borrowings SET return_date = NOW() 
                    WHERE book_id = ? AND user_id = ? AND return_date IS NULL";
    $return_stmt = $conn->prepare($return_query);
    $return_stmt->bind_param("ii", $book_id, $_SESSION['user_id']);
    $return_stmt->execute();

    // Update book status
    $update_query = "UPDATE books SET status = 'available' WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("i", $book_id);
    $update_stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Book returned successfully']);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Return book error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while returning the book']);
} 