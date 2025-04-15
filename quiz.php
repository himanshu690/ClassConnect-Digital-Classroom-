<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $score = 0;
    $total = 1;

    // Correct answer for q1 is 'b'
    if (isset($_POST["q1"]) && $_POST["q1"] === "b") {
        $score++;
    }

    echo "<h2 style='text-align:center;'>Your Score: $score / $total</h2>";
    echo "<div style='text-align:center; margin-top:20px;'>
            <a href='../quizzes.html' style='text-decoration:none; color:blue;'>Go back to Quiz</a>
          </div>";
} else {
    echo "<p style='color:red;'>Invalid Request.</p>";
}
?>
