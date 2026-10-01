<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

// Get all active quizzes
$quizzes = mysqli_query($conn, "SELECT * FROM quizzes WHERE is_active = 1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-success px-4">
    <span class="navbar-brand">Online Quiz System</span>
    <div>
        <span class="text-white me-3">Welcome, <?= $_SESSION['user_name'] ?></span>
        <a href="my_results.php" class="btn btn-outline-light btn-sm me-2">My Results</a>
        <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <h4 class="mb-4">Available Quizzes</h4>

    <?php if(mysqli_num_rows($quizzes) == 0): ?>
        <div class="alert alert-info">No quizzes available right now.</div>
    <?php else: ?>
        <div class="row">
        <?php while($quiz = mysqli_fetch_assoc($quizzes)): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= $quiz['title'] ?></h5>
                        <p class="card-text text-muted"><?= $quiz['description'] ?></p>
                        <p><strong>Time Limit:</strong> <?= $quiz['time_limit'] ?> minutes</p>

                        <?php
                        // Check if student already attempted this quiz
                        $user_id = $_SESSION['user_id'];
                        $attempted = mysqli_fetch_assoc(mysqli_query($conn, 
                            "SELECT id FROM quiz_attempts 
                             WHERE user_id='$user_id' AND quiz_id='{$quiz['id']}'"));
                        ?>

                        <?php if($attempted): ?>
                            <a href="my_results.php" class="btn btn-secondary w-100">Already Attempted</a>
                        <?php else: ?>
                            <a href="take_quiz.php?quiz_id=<?= $quiz['id'] ?>" 
                               class="btn btn-success w-100">Start Quiz</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>