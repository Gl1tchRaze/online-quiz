<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

$quiz_id = $_GET['quiz_id'];
$user_id = $_SESSION['user_id'];

// Get quiz info
$quiz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM quizzes WHERE id='$quiz_id'"));

// Already attempted check
$attempted = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT id FROM quiz_attempts WHERE user_id='$user_id' AND quiz_id='$quiz_id'"));
if($attempted) {
    header("Location: my_results.php");
    exit();
}

// Get all questions with options
$questions_result = mysqli_query($conn, "SELECT * FROM questions WHERE quiz_id='$quiz_id'");
$questions = [];
while($q = mysqli_fetch_assoc($questions_result)) {
    $q['options'] = [];
    $options_result = mysqli_query($conn, "SELECT * FROM options WHERE question_id='{$q['id']}'");
    while($o = mysqli_fetch_assoc($options_result)) {
        $q['options'][] = $o;
    }
    $questions[] = $q;
}

// Submit quiz
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $score       = 0;
    $total_marks = 0;

    // Create attempt record
    $sql = "INSERT INTO quiz_attempts (user_id, quiz_id, submitted_at) 
            VALUES ('$user_id', '$quiz_id', NOW())";
    mysqli_query($conn, $sql);
    $attempt_id = mysqli_insert_id($conn);

    foreach($questions as $q) {
        $total_marks += $q['marks'];
        $selected = isset($_POST['q_' . $q['id']]) ? $_POST['q_' . $q['id']] : null;

        // Save answer
        if($selected) {
            mysqli_query($conn, "INSERT INTO answers (attempt_id, question_id, selected_option) 
                                 VALUES ('$attempt_id', '{$q['id']}', '$selected')");

            // Check if correct
            $correct = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT is_correct FROM options WHERE id='$selected'"));
            if($correct['is_correct']) {
                $score += $q['marks'];
            }
        }
    }

    // Update score
    mysqli_query($conn, "UPDATE quiz_attempts SET score='$score', total_marks='$total_marks' 
                         WHERE id='$attempt_id'");

    header("Location: my_results.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $quiz['title'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-success px-4">
    <span class="navbar-brand"><?= $quiz['title'] ?></span>
    <span class="text-white">
        Time Left: <strong><span id="timer"></span></strong>
    </span>
</nav>

<div class="container mt-4">
    <form method="POST" id="quizForm">
        <?php foreach($questions as $index => $q): ?>
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5>Q<?= $index+1 ?>. <?= $q['question_text'] ?> 
                    <span class="text-muted">(<?= $q['marks'] ?> mark)</span>
                </h5>
                <?php foreach($q['options'] as $opt): ?>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="radio" 
                           name="q_<?= $q['id'] ?>" value="<?= $opt['id'] ?>">
                    <label class="form-check-label"><?= $opt['option_text'] ?></label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-success btn-lg w-100 mb-5">Submit Quiz</button>
    </form>
</div>

<script>
    // Countdown Timer
    var minutes = <?= $quiz['time_limit'] ?>;
    var seconds = minutes * 60;

    function updateTimer() {
        var m = Math.floor(seconds / 60);
        var s = seconds % 60;
        document.getElementById('timer').textContent = 
            (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;

        if(seconds <= 0) {
            document.getElementById('quizForm').submit();
        }
        seconds--;
    }

    updateTimer();
    setInterval(updateTimer, 1000);
</script>

</body>
</html>