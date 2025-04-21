<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Database connection
$conn = new mysqli("localhost", "root", "", "athena_library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user details
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

// Get borrowed books
$borrowed_books = [];
$borrowed_stmt = $conn->prepare("
    SELECT b.id, b.title, b.author, bb.borrowed_date 
    FROM borrowed_books bb 
    JOIN books b ON bb.book_id = b.id 
    WHERE bb.user_id = ?
    ORDER BY bb.borrowed_date DESC
");
$borrowed_stmt->bind_param("i", $user_id);
$borrowed_stmt->execute();
$borrowed_result = $borrowed_stmt->get_result();
while ($row = $borrowed_result->fetch_assoc()) {
    $borrowed_books[] = $row;
}
$borrowed_stmt->close();

// Handle book borrowing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow_book'])) {
    $book_id = $_POST['book_id'];
    
    // First verify the book exists
    $book_check = $conn->prepare("SELECT id FROM books WHERE id = ?");
    $book_check->bind_param("i", $book_id);
    $book_check->execute();
    $book_result = $book_check->get_result();
    
    if ($book_result->num_rows == 0) {
        $_SESSION['borrow_error'] = "Book not found in database.";
        $book_check->close();
        header("Location: dashboard.php");
        exit();
    }
    $book_check->close();
    
    // Check if book is already borrowed by this user
    $check_stmt = $conn->prepare("SELECT id FROM borrowed_books WHERE book_id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $book_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows == 0) {
        // Borrow the book
        $borrow_stmt = $conn->prepare("INSERT INTO borrowed_books (user_id, book_id, borrowed_date) VALUES (?, ?, NOW())");
        $borrow_stmt->bind_param("ii", $user_id, $book_id);
        
        if ($borrow_stmt->execute()) {
            // Update the book's status to 'borrowed'
            $update_status = $conn->prepare("UPDATE books SET status = 'borrowed' WHERE id = ?");
            $update_status->bind_param("i", $book_id);
            $update_status->execute();
            $update_status->close();

            $_SESSION['borrow_success'] = "Book borrowed successfully!";
        } else {
            $_SESSION['borrow_error'] = "Error borrowing book: " . $borrow_stmt->error;
        }
        $borrow_stmt->close();
    } else {
        $_SESSION['borrow_error'] = "You have already borrowed this book.";
    }
    $check_stmt->close();
    
    header("Location: dashboard.php");
    exit();
}

// Handle AJAX requests for borrowing books
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_borrow_book'])) {
    $book_id = $_POST['book_id'];

    // Verify the book exists
    $book_check = $conn->prepare("SELECT id FROM books WHERE id = ?");
    $book_check->bind_param("i", $book_id);
    $book_check->execute();
    $book_result = $book_check->get_result();

    if ($book_result->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Book not found.']);
        exit();
    }
    $book_check->close();

    // Check if the book is already borrowed
    $check_stmt = $conn->prepare("SELECT id FROM borrowed_books WHERE book_id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $book_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {
        // Add the book to borrowed_books
        $borrow_stmt = $conn->prepare("INSERT INTO borrowed_books (user_id, book_id, borrowed_date) VALUES (?, ?, NOW())");
        $borrow_stmt->bind_param("ii", $user_id, $book_id);

        if ($borrow_stmt->execute()) {
            // Update the book's status to 'borrowed'
            $update_status = $conn->prepare("UPDATE books SET status = 'borrowed' WHERE id = ?");
            $update_status->bind_param("i", $book_id);
            $update_status->execute();
            $update_status->close();

            // Fetch book details to return as a response
            $book_details_stmt = $conn->prepare("SELECT title, author FROM books WHERE id = ?");
            $book_details_stmt->bind_param("i", $book_id);
            $book_details_stmt->execute();
            $book_details = $book_details_stmt->get_result()->fetch_assoc();
            echo json_encode(['status' => 'success', 'book' => $book_details]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error borrowing book.']);
        }
        $borrow_stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'You already borrowed this book.']);
    }
    $check_stmt->close();
    exit();
}

// Handle book return
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return_book'])) {
    $book_id = $_POST['book_id'];
    
    $return_stmt = $conn->prepare("DELETE FROM borrowed_books WHERE book_id = ? AND user_id = ?");
    $return_stmt->bind_param("ii", $book_id, $user_id);
    
    if ($return_stmt->execute()) {
        $_SESSION['borrow_success'] = "Book returned successfully!";
    } else {
        $_SESSION['borrow_error'] = "Error returning book: " . $return_stmt->error;
    }
    $return_stmt->close();
    
    header("Location: dashboard.php");
    exit();
}

// Get available books (books not borrowed by this user)
$books = [];
$stmt = $conn->prepare("SELECT b.id, b.title, b.author 
                       FROM books b 
                       LEFT JOIN borrowed_books bb ON b.id = bb.book_id AND bb.user_id = ?
                       WHERE bb.id IS NULL");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Athena Library</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;  /* Indigo */
            --primary-dark: #4f46e5;
            --secondary: #ec4899;  /* Pink */
            --accent: #f59e0b;  /* Amber */
            --success: #10b981;  /* Emerald */
            --dark: #1e293b;  /* Slate */
            --light: #f8fafc;
            --warning: #f59e0b;
            --error: #ef4444;
            --border-radius: 12px;
            --card-radius: 16px;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Navbar Styles */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.2);
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo i {
            font-size: 2rem;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .logo:hover i {
            transform: rotate(15deg);
        }

        .logo h1 {
            font-size: 1.5rem;
            color: white;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-bar {
            display: flex;
            gap: 0.5rem;
            flex: 1;
            max-width: 500px;
            margin: 0 2rem;
        }

        .search-bar input {
            flex: 1;
            padding: 0.8rem 1.5rem;
            border: 2px solid rgba(138, 43, 226, 0.3);
            border-radius: 50px;
            font-size: 1rem;
            transition: var(--transition);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            color: var(--dark);
        }

        .search-bar input::placeholder {
            color: #adb5bd;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(255, 105, 180, 0.3);
        }

        .search-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.8rem 1.5rem;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .search-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .nav-actions {
            display: flex;
            gap: 1rem;
        }

        .nav-btn {
            background: linear-gradient(135deg, #8A2BE2 0%, #4B0082 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            border: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(138, 43, 226, 0.2);
        }

        .nav-btn:hover {
            background: linear-gradient(135deg, #D62929 0%, #8B0000 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(214, 41, 41, 0.3);
        }

        .nav-btn i {
            font-size: 16px;
            transition: transform 0.3s ease;
        }

        .nav-btn:hover i {
            transform: scale(1.1);
        }

        /* Add a subtle pulse animation for the saved books button */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        #saved-books-btn {
            animation: pulse 2s infinite;
        }

        #saved-books-btn:hover {
            animation: none;
        }

        /* Welcome Message */
        .welcome-message {
            text-align: center;
            padding: 3rem 0;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);
            border: 1px solid rgba(99, 102, 241, 0.2);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            margin: 2rem 0;
        }

        .welcome-message h1 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 50%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: none;
        }

        .welcome-message p {
            font-size: 1.2rem;
            color: var(--neutral);
            font-weight: 300;
        }

        /* Books Section */
        .section {
            padding: 2rem 0;
        }

        .section-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            font-size: 1.8rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 0.5rem;
            font-weight: 600;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--primary);
            border-radius: 3px;
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .book-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: var(--card-radius);
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.1);
            transition: var(--transition);
            position: relative;
            border: 1px solid rgba(99, 102, 241, 0.1);
            transform: translateY(0);
            backdrop-filter: blur(10px);
            animation: float 6s ease-in-out infinite;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.2);
        }

        .book-cover {
            position: relative;
            height: 200px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 50%, var(--accent) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .book-info {
            padding: 1.5rem;
        }

        .book-info h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-weight: 600;
            transition: var(--transition);
        }

        .book-card:hover .book-info h3 {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .book-info .author {
            color: var(--neutral);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .book-info .description {
            color: var(--neutral);
            margin-bottom: 1rem;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .book-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .save-btn, .read-more-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .save-btn {
            background: linear-gradient(135deg, var(--success) 0%, #45a049 100%);
            color: white;
        }

        .save-btn:hover {
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
            transform: translateY(-2px);
        }

        .save-btn.saved {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .read-more-btn {
            background: linear-gradient(135deg, var(--accent) 0%, #ffd700 100%);
            color: var(--dark);
        }

        .read-more-btn:hover {
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
            transform: translateY(-2px);
        }

        .book-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .book-btn:hover {
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
            transform: translateY(-2px);
        }

        .book-btn i {
            font-size: 16px;
        }

        .book-btn.bookmarked {
            background: linear-gradient(135deg, var(--accent) 0%, #ffd700 100%);
            color: var(--dark);
        }

        .book-btn.bookmarked i {
            color: var(--accent);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination-btn {
            padding: 8px 20px;
            border: 2px solid var(--primary);
            background: white;
            border-radius: 25px;
            cursor: pointer;
            transition: var(--transition);
            color: var(--primary);
            font-weight: 500;
        }

        .pagination-btn:hover {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            transform: translateY(-2px);
        }

        .pagination-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
        }

        .pagination-btn.arrow {
            padding: 0.5rem;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-content {
                flex-direction: column;
                gap: 1rem;
            }

            .search-bar {
                max-width: 100%;
                margin: 1rem 0;
            }

            .nav-actions {
                width: 100%;
                justify-content: center;
            }

            .books-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .welcome-message h1 {
                font-size: 2rem;
            }

            .navbar {
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            }

            .book-card {
                animation: none;
            }
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 25px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .notification.success {
            background: linear-gradient(135deg, var(--success) 0%, #45a049 100%);
        }

        .notification.error {
            background: linear-gradient(135deg, var(--error) 0%, #dc2626 100%);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .profile-section {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.2);
            width: 90%;
            max-width: 600px;
            z-index: 1000;
            display: none;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .profile-header h2 {
            color: var(--dark);
            margin: 0;
            font-weight: 600;
        }

        .close-btn {
            background: none;
            border: none;
            color: var(--neutral);
            cursor: pointer;
            font-size: 1.5rem;
            padding: 0;
        }

        .close-btn:hover {
            color: var(--secondary);
            transform: rotate(90deg);
        }

        .profile-content {
            display: grid;
            gap: 1.5rem;
        }

        .profile-details {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(236, 72, 153, 0.05) 100%);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border: 1px solid rgba(99, 102, 241, 0.1);
        }

        .profile-details h3 {
            color: var(--dark);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: 500;
            color: var(--primary);
        }

        .detail-value {
            color: var(--dark);
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
            backdrop-filter: blur(3px);
        }

        .borrowed-books-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .borrowed-book-item {
            background: white;
            padding: 1.2rem;
            border-radius: var(--border-radius);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            border: 1px solid #e9ecef;
            transition: var(--transition);
        }

        .borrowed-book-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .book-info h4 {
            margin: 0;
            color: var(--dark);
            font-weight: 600;
        }

        .book-info p {
            margin: 0.5rem 0;
            color: var(--neutral);
            font-size: 0.9rem;
        }

        .borrow-date {
            font-size: 0.8rem;
            color: var(--neutral);
            background: #f8f9fa;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            display: inline-block;
        }

        .return-form {
            margin: 0;
        }

        .return-btn {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .return-btn:hover {
            background-color: #d90429;
            transform: translateY(-2px);
        }

        .no-books {
            text-align: center;
            padding: 2rem;
            color: var(--neutral);
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid #e9ecef;
        }

        .no-books p {
            margin: 0;
            font-size: 1.1rem;
        }

        /* Footer Styles */
        .footer {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: var(--spacing-xl) 0 var(--spacing-md);
            position: relative;
            overflow: hidden;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 3rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-xl);
            margin-bottom: var(--spacing-xl);
            position: relative;
            z-index: 1;
        }

        .footer-links-section {
            text-align: left;
        }

        .footer-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: var(--spacing-md);
            color: white;
            position: relative;
            padding-bottom: var(--spacing-xs);
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--primary);
            border-radius: 2px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: var(--spacing-sm);
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            transition: var(--transition);
            display: inline-block;
            position: relative;
            font-size: 1rem;
        }

        .footer-links a:hover {
            color: var(--accent);
            transform: translateX(5px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: var(--spacing-md);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
        }

        .footer-bottom p {
            color: white;
            font-size: 0.9rem;
        }

        .footer-bottom a {
            color: white;
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
        }

        .footer-bottom a:hover {
            color: var(--accent);
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .footer-content {
                grid-template-columns: 1fr;
            }
        }

        .loading-spinner {
            text-align: center;
            padding: 2rem;
            color: var(--primary);
        }

        .loading-spinner i {
            font-size: 2rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            text-align: center;
            padding: 2rem;
            color: var(--error);
            background: rgba(239, 68, 68, 0.1);
            border-radius: 10px;
            margin: 1rem 0;
        }

        .retry-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .retry-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .no-results {
            text-align: center;
            padding: 2rem;
            color: var(--neutral);
            font-size: 1.1rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid #e9ecef;
        }

        /* Book Modal */
        .book-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 1rem;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--card-radius);
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalSlideIn 0.3s ease-out;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.2);
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: white;
            border: none;
            color: var(--neutral);
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
            z-index: 1;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .close-modal:hover {
            color: var(--secondary);
            transform: rotate(90deg);
            background: #f8f9fa;
        }

        .modal-header {
            display: flex;
            gap: 2rem;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(236, 72, 153, 0.05) 100%);
            border-radius: var(--card-radius) var(--card-radius) 0 0;
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
        }

        .modal-book-cover {
            width: 200px;
            height: 300px;
            object-fit: cover;
            border-radius: var(--border-radius);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.2);
        }

        .modal-book-info {
            flex: 1;
        }

        .modal-book-info h2 {
            font-size: 1.8rem;
            color: var(--dark);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .modal-author {
            color: var(--neutral);
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .modal-date, .modal-publisher {
            color: var(--neutral);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-section {
            margin-bottom: 1.5rem;
        }

        .modal-section h3 {
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .modal-section p {
            color: var(--neutral);
            line-height: 1.6;
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .modal-borrow-btn, .modal-preview-btn {
            padding: 10px 20px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s ease;
        }

        .modal-borrow-btn {
            background-color: var(--primary);
            color: white;
        }

        .modal-borrow-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .modal-preview-btn {
            padding: 10px 20px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s ease;
        }

        .modal-preview-btn:hover {
            background-color: #1976D2;
        }

        .bookmark-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: var(--accent);
            font-size: 1.2rem;
            animation: fadeIn 0.3s ease-out;
            background: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 768px) {
            .modal-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .modal-book-cover {
                width: 150px;
                height: 225px;
            }

            .modal-actions {
                flex-direction: column;
            }

            .book-actions {
                flex-direction: column;
            }

            .book-actions button {
                width: 100%;
            }
        }

        .video-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 50%, var(--accent) 100%);
            padding: 0;
            margin: 2rem 0;
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .video-container {
            width: 100%;
            margin: 0;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 8px rgba(0, 0, 0, 0.2);
        }

        .video-container video {
            width: 100%;
            height: 500px;
            display: block;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .video-section {
                margin: 1rem 0;
            }

            .video-container video {
                height: 300px;
            }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .book-card:nth-child(2n) {
            animation-delay: 0.5s;
        }

        .book-card:nth-child(3n) {
            animation-delay: 1s;
        }

        .section-actions {
            display: flex;
            gap: 1rem;
        }

        .library-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .tab-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 50px;
            background: rgba(99, 102, 241, 0.1);
            color: var(--dark);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .book-card {
            position: relative;
        }

        .book-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
            z-index: 1;
        }

        .book-badge.borrowed {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .book-badge.saved {
            background: linear-gradient(135deg, var(--accent) 0%, var(--warning) 100%);
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 2rem;
            border-radius: 50px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .notification.success {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .notification.error {
            background: linear-gradient(135deg, var(--error) 0%, #dc2626 100%);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .saved-books-section {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .book-card.saved {
            border: 2px solid var(--accent);
        }

        .book-card.saved .book-badge {
            background: linear-gradient(135deg, var(--accent) 0%, var(--warning) 100%);
        }

        .no-saved-books {
            text-align: center;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
            margin: 2rem 0;
        }

        .no-saved-books i {
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .no-saved-books h3 {
            color: var(--dark);
            margin-bottom: 1rem;
        }

        .no-saved-books p {
            color: var(--dark);
            opacity: 0.8;
        }

        .featured-categories {
            padding: 2rem 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .category-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
            cursor: pointer;
            border: 1px solid rgba(99, 102, 241, 0.1);
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(99, 102, 241, 0.2);
            border-color: var(--primary);
        }

        .category-card i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1rem;
            transition: var(--transition);
        }

        .category-card:hover i {
            transform: scale(1.1);
            color: var(--secondary);
        }

        .category-card h3 {
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }

        .category-card p {
            color: var(--dark);
            opacity: 0.8;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .category-card {
                padding: 1rem;
            }

            .category-card i {
                font-size: 1.5rem;
            }

            .category-card h3 {
                font-size: 1rem;
            }

            .category-card p {
                font-size: 0.8rem;
            }
        }
    </style>
    <script>
        // Force 15 books per page
        window.resultsPerPage = 15;
    </script>
    <script src="js/script.js?v=<?php echo time(); ?>" defer></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="js/dashboard.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <div class="logo">
                    <i class="fas fa-book-open"></i>
                    <h1>Athena Library</h1>
                </div>
                <div class="search-bar">
                    <input type="text" id="search-input" placeholder="Search books, authors, or topics...">
                    <button class="search-btn" id="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="nav-actions">
                    <button class="nav-btn" id="profile-btn">
                        <i class="fas fa-user"></i>
                        Profile
                    </button>
                    <button class="nav-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <a href="logout.php">Logout</a>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="welcome-message">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>Explore our collection below.</p>
        </div>
    </div>

    <section class="video-section">
        <div class="video-container">
            <video id="library-video" autoplay muted loop playsinline>
                <source src="css/library.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </section>
        
    <!-- Featured Categories Section -->
    <section class="featured-categories">
        <div class="container">
            <h2 class="section-title">Featured Categories</h2>
            <div class="categories-grid">
                <div class="category-card" data-category="Fiction">
                    <i class="fas fa-book"></i>
                    <h3>Fiction</h3>
                    <p>Explore imaginative stories and novels</p>
                </div>
                <div class="category-card" data-category="Science">
                    <i class="fas fa-flask"></i>
                    <h3>Science</h3>
                    <p>Discover scientific knowledge and research</p>
                </div>
                <div class="category-card" data-category="History">
                    <i class="fas fa-landmark"></i>
                    <h3>History</h3>
                    <p>Journey through time and historical events</p>
                </div>
                <div class="category-card" data-category="Technology">
                    <i class="fas fa-laptop-code"></i>
                    <h3>Technology</h3>
                    <p>Learn about computers, programming, and tech</p>
                </div>
                <div class="category-card" data-category="Biography">
                    <i class="fas fa-user-tie"></i>
                    <h3>Biography</h3>
                    <p>Read about remarkable people's lives</p>
                </div>
                <div class="category-card" data-category="Business">
                    <i class="fas fa-chart-line"></i>
                    <h3>Business</h3>
                    <p>Explore entrepreneurship and management</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="search-results">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Featured Books</h2>
                <div class="section-actions">
                <style>
                    .nav-btn {
                        background-color: #8A2BE2;
                        color: white;
                        transition: background-color 0.3s ease;
                    }
                    .nav-btn:hover {
                        background-color: rgb(214, 41, 41);
                        color: white;
                    }
                </style>    
                    <button class="nav-btn" id="saved-books-btn">
                        <i class="fas fa-bookmark"></i> Saved Books
                    </button>
                </div>
            </div>
            <div class="books-grid" id="books-container">
                <!-- Books will be loaded here -->
            </div>
        </div>
    </section>

    <section class="section" id="saved-books-section" style="display: none;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">My Saved Books</h2>
                <div class="section-actions">
                    <button class="nav-btn" id="back-to-search">
                        <i class="fas fa-arrow-left"></i> Back to Search
                    </button>
                </div>
            </div>
            <div class="books-grid" id="saved-books-container">
                <!-- Saved books will be loaded here -->
            </div>
        </div>
    </section>

    <?php if (isset($_SESSION['borrow_success'])): ?>
        <div class="notification success">
            <?php 
            echo $_SESSION['borrow_success'];
            unset($_SESSION['borrow_success']); 
            ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['borrow_error'])): ?>
        <div class="notification error">
            <?php 
            echo $_SESSION['borrow_error'];
            unset($_SESSION['borrow_error']); 
            ?>
        </div>
    <?php endif; ?>

    <!-- Profile Section -->
    <div class="overlay" id="overlay"></div>
    <div class="profile-section" id="profile-section">
        <div class="profile-header">
            <h2>Your Profile</h2>
            <button class="close-btn" id="close-profile">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="profile-content">
            <div class="profile-details">
                <h3>Personal Information</h3>
                <div class="detail-row">
                    <span class="detail-label">Username:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Member Since:</span>
                    <span class="detail-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                </div>
            </div>
            
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-links-section">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-links-section">
                    <h3 class="footer-title">Support</h3>
                    <ul class="footer-links">
                        <li><a href="FAQ.html">FAQ</a></li>
                        <li><a href="help.html">Help Center</a></li>
                        <li><a href="terms.html">Terms of Service</a></li>
                        <li><a href="privacy.html">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Athena Library. All rights reserved. Designed by <a href="about.php">Prasant Yadav</a></p>
            </div>
        </div>
    </footer>
</body>
</html>