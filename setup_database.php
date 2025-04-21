<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "athena_library");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read and execute SQL file
$sql = file_get_contents('create_tables.sql');

if ($conn->multi_query($sql)) {
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    
    echo "Database tables created successfully!";
} else {
    echo "Error creating tables: " . $conn->error;
}

$conn->close();
?> 