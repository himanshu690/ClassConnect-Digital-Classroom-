<?php
// get_replies.php
header('Content-Type: application/json');
$repliesFilename = 'replies.txt';

if (!isset($_GET['discussion_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Discussion ID is required']);
    exit;
}

$discussionId = $_GET['discussion_id'];

function readReplies() {
    global $repliesFilename;
    $replies = [];
    
    if (file_exists($repliesFilename)) {
        $lines = file($repliesFilename, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            $entry = json_decode($line, true);
            if ($entry) {
                $replies[] = $entry;
            }
        }
    }
    
    return $replies;
}

$replies = readReplies();
$filteredReplies = array_filter($replies, function($reply) use ($discussionId) {
    return $reply['discussion_id'] === $discussionId;
});

echo json_encode(array_values($filteredReplies));
?>
