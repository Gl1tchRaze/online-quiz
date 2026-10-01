<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title       = $_POST['title'];
    $description = $_POST['description'];
    $time_limit  = $_POST['time_limit'];
    $creator_id  = $_SESSION['user_id'];

    $sql = "INSERT INTO quizzes (creator_id, title, description, time_limit) 
            VALUES ('$creator_id', '$title', '$description', '$time_limit')";

    if(mysqli_query($conn, $sql)) {
        $quiz_id = mysqli_insert_id($conn);
        header("Location: add_questions.php?quiz_id=$quiz_id");
        exit();
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Quiz</title>
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
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h4 class="mb-4">Create New Quiz</h4>

                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Quiz Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Time Limit (minutes)</label>
                            <input type="number" name="time_limit" class="form-control" value="30" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Quiz & Add Questions</button>
                        <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>