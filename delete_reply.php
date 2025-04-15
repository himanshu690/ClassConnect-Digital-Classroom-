<?php
// delete_reply.php
header('Content-Type: application/json');
$repliesFilename = 'replies.txt';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Reply ID is required']);
        exit;
    }

    $replyId = $_GET['id'];

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

    function saveReplies($replies) {
        global $repliesFilename;
        $content = '';
        foreach ($replies as $reply) {
            $content .= json_encode($reply) . PHP_EOL;
        }
        file_put_contents($repliesFilename, $content);
    }

    $replies = readReplies();
    $newReplies = array_filter($replies, function($reply) use ($replyId) {
        return $reply['id'] !== $replyId;
    });

    if (count($newReplies) < count($replies)) {
        saveReplies($newReplies);
        echo json_encode(['status' => 'deleted']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Reply not found']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
