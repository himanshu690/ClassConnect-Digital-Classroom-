<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$repliesFile = 'replies.txt';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    if (!file_exists($repliesFile)) {
        file_put_contents($repliesFile, '');
        chmod($repliesFile, 0666);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle reply posting
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['discussion_id']) || empty($input['author_name']) || empty($input['content'])) {
            throw new Exception('All fields are required', 400);
        }
        
        $newReply = [
            'id' => uniqid(),
            'discussion_id' => $input['discussion_id'],
            'author_name' => htmlspecialchars($input['author_name']),
            'content' => htmlspecialchars($input['content']),
            'time' => date('Y-m-d H:i:s')
        ];
        
        file_put_contents($repliesFile, json_encode($newReply) . PHP_EOL, FILE_APPEND);
        echo json_encode($newReply);
    }
    elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Handle reply deletion
        if (empty($_GET['reply_id'])) {
            throw new Exception('Reply ID is required', 400);
        }
        
        $replyId = $_GET['reply_id'];
        $lines = file($repliesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $updated = false;
        
        $newLines = array_filter($lines, function($line) use ($replyId, &$updated) {
            $reply = json_decode($line, true);
            if ($reply && $reply['id'] === $replyId) {
                $updated = true;
                return false;
            }
            return true;
        });
        
        if ($updated) {
            file_put_contents($repliesFile, implode(PHP_EOL, $newLines) . PHP_EOL);
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Reply not found', 404);
        }
    }
    else {
        throw new Exception('Method not allowed', 405);
    }
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['error' => $e->getMessage()]);
}