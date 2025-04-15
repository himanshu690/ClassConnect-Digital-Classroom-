<?php
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $title = htmlspecialchars($_POST['title']);
//     $content = htmlspecialchars($_POST['content']);
//     $author = htmlspecialchars($_POST['author']);

//     $note = [
//         "title" => $title,
//         "content" => $content,
//         "author" => $author,
//         "timestamp" => date("Y-m-d H:i:s"),
//         "comments" => [] // Add this line
//     ];

//     $file = 'notes.json';
//     $notes = [];

//     if (file_exists($file)) {
//         $notes = json_decode(file_get_contents($file), true);
//     }

//     array_unshift($notes, $note); // add to beginning
//     file_put_contents($file, json_encode($notes, JSON_PRETTY_PRINT));

//     header("Location: notes.html");
//     exit();
// }


// notes.php


// notes.php

// notes.php

header("Content-Type: application/json");

// File to store notes
$filename = 'notes.json';

// Read existing notes
$notes = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

// Get action from URL
$action = $_GET['action'] ?? 'add';

// Function to save notes to file
function saveNotes($notes, $filename) {
    file_put_contents($filename, json_encode($notes, JSON_PRETTY_PRINT));
}

// Add new note
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $author = $_POST['author'] ?? 'Anonymous';

    if (trim($title) === '' || trim($content) === '' || trim($author) === '') {
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }

    $note = [
        'id' => uniqid(),
        'title' => htmlspecialchars($title),
        'content' => nl2br(htmlspecialchars($content)),
        'author' => htmlspecialchars($author),
        'timestamp' => date('Y-m-d H:i:s')
    ];

    array_unshift($notes, $note); // Add the new note at the beginning
    saveNotes($notes, $filename);
    echo json_encode($note);  // Return the newly added note
    exit;
}

// Read all notes
if ($action === 'read') {
    echo json_encode($notes);
    exit;
}

// Get a single note by ID
if ($action === 'get' && isset($_GET['id'])) {
    $id = $_GET['id'];
    foreach ($notes as $note) {
        if ($note['id'] === $id) {
            echo json_encode($note);
            exit;
        }
    }
    http_response_code(404);
    echo json_encode(['error' => 'Note not found.']);
    exit;
}

// Delete a note by ID
if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $newNotes = array_filter($notes, fn($note) => $note['id'] !== $id);
    saveNotes(array_values($newNotes), $filename);
    echo json_encode(['status' => 'deleted']);
    exit;
}

// Edit a note by ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $author = $_POST['author'] ?? 'Anonymous';

    if (trim($title) === '' || trim($content) === '' || trim($author) === '') {
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }

    $updated = false;

    // Find and update the existing note by ID
    // Edit a note by ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $author = $_POST['author'] ?? 'Anonymous';

    if (trim($title) === '' || trim($content) === '' || trim($author) === '') {
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }

    $updated = false;

    // Find and update the existing note by ID
    foreach ($notes as $key => $note) {
        if ($note['id'] === $id) {
            // Update the existing note's data
            $notes[$key]['title'] = htmlspecialchars($title);
            $notes[$key]['content'] = nl2br(htmlspecialchars($content));
            $notes[$key]['author'] = htmlspecialchars($author);
            $notes[$key]['timestamp'] = date('Y-m-d H:i:s');
            $updated = true;
            break;
        }
    }

    if ($updated) {
        saveNotes($notes, $filename);
        echo json_encode(['status' => 'updated']);
    } else {
        echo json_encode(['error' => 'Note not found']);
    }

    exit;
}
}

// Fallback for unsupported action
http_response_code(400);
echo json_encode(['error' => 'Invalid action.']);


?>
