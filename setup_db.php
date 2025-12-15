<?php
include 'db_connect.php';

// Read the SQL file
$sql = file_get_contents('database.sql');

// Execute multi query
if ($conn->multi_query($sql)) {
    echo "Database setup successfully.\n";
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
        // Check for more results
    } while ($conn->next_result());
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

$conn->close();
?>