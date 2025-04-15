<?php
// get_discussions.php
header('Content-Type: application/json');
$filename = 'discussions.txt';

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

$discussions = readDiscussions();
echo json_encode($discussions);
?>
