<?php
// discussion.php
header('Content-Type: application/json');
$filename = 'discussions.txt';

// Initialize file if it doesn't exist
if (!file_exists($filename)) {
    file_put_contents($filename, '');
}

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet();
        break;
    case 'POST':
        handlePost();
        break;
    case 'PUT':
        handlePut();
        break;
    case 'DELETE':
        handleDelete();
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

function handleGet() {
    global $filename;
    $discussions = readDiscussions();
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        foreach ($discussions as $discussion) {
            if ($discussion['id'] === $id) {
                echo json_encode($discussion);
                return;
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Discussion not found']);
    } else {
        echo json_encode($discussions);
    }
}

function handlePost() {
    global $filename;
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['name']) || !isset($input['question'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Name and question are required']);
        return;
    }
    
    $newDiscussion = [
        'id' => uniqid(),
        'name' => htmlspecialchars($input['name']),
        'question' => htmlspecialchars($input['question']),
        'time' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents($filename, json_encode($newDiscussion) . PHP_EOL, FILE_APPEND);
    echo json_encode($newDiscussion);
}

function handlePut() {
    global $filename;
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id']) || !isset($input['question'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID and question are required']);
        return;
    }
    
    $discussions = readDiscussions();
    $updated = false;
    
    foreach ($discussions as &$discussion) {
        if ($discussion['id'] === $input['id']) {
            $discussion['question'] = htmlspecialchars($input['question']);
            $discussion['time'] = date('Y-m-d H:i:s');
            $updated = true;
            break;
        }
    }
    
    if ($updated) {
        saveDiscussions($discussions);
        echo json_encode(['status' => 'updated']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Discussion not found']);
    }
}

function handleDelete() {
    global $filename;
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID is required']);
        return;
    }
    
    $id = $_GET['id'];
    $discussions = readDiscussions();
    $newDiscussions = array_filter($discussions, function($discussion) use ($id) {
        return $discussion['id'] !== $id;
    });
    
    if (count($newDiscussions) < count($discussions)) {
        saveDiscussions($newDiscussions);
        echo json_encode(['status' => 'deleted']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Discussion not found']);
    }
}

function readDiscussions() {
    global $filename;
    $discussions = [];
    
    if (file_exists($filename)) {
        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            $entry = json_decode($line, true);
            if ($entry) {
                $discussions[] = $entry;
            }
        }
    }
    
    return $discussions;
}

function saveDiscussions($discussions) {
    global $filename;
    $content = '';
    foreach ($discussions as $discussion) {
        $content .= json_encode($discussion) . PHP_EOL;
    }
    file_put_contents($filename, $content);
}
?>
