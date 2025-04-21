<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $query = "SELECT b.id, b.title, b.author, b.isbn, 
              br.borrow_date, br.due_date, br.return_date,
              CASE 
                WHEN br.return_date IS NULL AND br.due_date < CURDATE() THEN 'overdue'
                WHEN br.return_date IS NULL THEN 'borrowed'
                ELSE 'returned'
              END as status
              FROM borrowings br
              JOIN books b ON br.book_id = b.id
              WHERE br.user_id = ?
              ORDER BY br.borrow_date DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $borrowings = [];
    while ($row = $result->fetch_assoc()) {
        $borrowings[] = [
            'book_id' => $row['id'],
            'title' => $row['title'],
            'author' => $row['author'],
            'isbn' => $row['isbn'],
            'borrow_date' => $row['borrow_date'],
            'due_date' => $row['due_date'],
            'return_date' => $row['return_date'],
            'status' => $row['status']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'borrowings' => $borrowings
    ]);
} catch (Exception $e) {
    error_log("Borrowing history error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching borrowing history']);
} 