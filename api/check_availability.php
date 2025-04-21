<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$book_id = $data['book_id'] ?? null;

if (!$book_id) {
    echo json_encode(['success' => false, 'message' => 'Book ID is required']);
    exit();
}

try {
    $query = "SELECT id, title, author, status, 
              (SELECT COUNT(*) FROM borrowings WHERE book_id = b.id AND return_date IS NULL) as borrowed_count
              FROM books b WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($book = $result->fetch_assoc()) {
        $available = $book['status'] === 'available' && $book['borrowed_count'] === 0;
        echo json_encode([
            'success' => true,
            'available' => $available,
            'book' => [
                'id' => $book['id'],
                'title' => $book['title'],
                'author' => $book['author'],
                'status' => $book['status']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Book not found']);
    }
} catch (Exception $e) {
    error_log("Check availability error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while checking availability']);
} 