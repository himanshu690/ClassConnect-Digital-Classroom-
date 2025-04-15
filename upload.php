<!-- <?php
// Create the uploads directory if it doesn't exist
$uploadDir = "./uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST["title"]);
    $file = $_FILES["note_file"];

    // Validate file type
    if ($file["type"] != "application/pdf") {
        echo "Only PDF files are allowed.";
        exit;
    }

    // Move uploaded file to uploads folder
    $fileName = basename($file["name"]);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file["tmp_name"], $targetPath)) {
        echo "<p style='color: green;'>Note uploaded successfully!</p>";
        echo "<a href='../notes.html'>Go back to Notes</a>";
    } else {
        echo "<p style='color: red;'>Failed to upload note.</p>";
    }
}

$files = scandir("uploads");
foreach ($files as $file) {
  if ($file !== '.' && $file !== '..') {
    echo "<div class='mb-2'><a href='uploads/$file' class='text-blue-600 hover:underline' target='_blank'>$file</a></div>";
  }
}

?> -->
