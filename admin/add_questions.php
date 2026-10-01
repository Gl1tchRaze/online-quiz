<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$quiz_id = $_GET['quiz_id'];
$quiz    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM quizzes WHERE id='$quiz_id'"));

$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = $_POST['question_text'];
    $marks         = $_POST['marks'];
    $options       = $_POST['options'];
    $correct       = $_POST['correct'];

    // Save question
    $sql = "INSERT INTO questions (quiz_id, question_text, marks) 
            VALUES ('$quiz_id', '$question_text', '$marks')";
    mysqli_query($conn, $sql);
    $question_id = mysqli_insert_id($conn);

    // Save 4 options
    foreach($options as $index => $option_text) {
        $is_correct = ($index == $correct) ? 1 : 0;
        $sql = "INSERT INTO options (question_id, option_text, is_correct) 
                VALUES ('$question_id', '$option_text', '$is_correct')";
        mysqli_query($conn, $sql);
    }

    $success = "Question added successfully!";
}

// Get all questions for this quiz
$questions = mysqli_query($conn, "SELECT * FROM questions WHERE quiz_id='$quiz_id'");
$question_count = mysqli_num_rows($questions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">Online Quiz System</span>
    <div>
        <span class="text-white me-3">Welcome, <?= $_SESSION['user_name'] ?></span>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
        <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">

        <!-- Add Question Form -->
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h4>Add Question — <?= $quiz['title'] ?></h4>
                    <p class="text-muted">Questions added so far: <strong><?= $question_count ?></strong></p>

                    <?php if($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Question</label>
                            <textarea name="question_text" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Marks</label>
                            <input type="number" name="marks" class="form-control" value="1" required>
                        </div>

                        <label class="mb-2">Options (select the correct one)</label>

                        <?php for($i = 0; $i < 4; $i++): ?>
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input type="radio" name="correct" value="<?= $i ?>" <?= $i==0 ? 'checked' : '' ?> required>
                            </div>
                            <input type="text" name="options[]" class="form-control" 
                                   placeholder="Option <?= $i+1 ?>" required>
                        </div>
                        <?php endfor; ?>

                        <button type="submit" class="btn btn-primary w-100 mt-2">Add Question</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Finish Button -->
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-4 text-center">
                    <h5>Done adding questions?</h5>
                    <p class="text-muted">You have added <strong><?= $question_count ?></strong> questions so far.</p>
                    <a href="dashboard.php" class="btn btn-success w-100">Finish & Go to Dashboard</a>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>