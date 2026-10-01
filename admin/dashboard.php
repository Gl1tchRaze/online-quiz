<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$total_quizzes  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM quizzes"))['total'];
$total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='student'"))['total'];
$total_attempts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM quiz_attempts"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">Online Quiz System</span>
    <div>
        <span class="text-white me-3">Welcome, <?= $_SESSION['user_name'] ?></span>
        <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <h4 class="mb-4">Admin Dashboard</h4>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Quizzes</h5>
                    <h2><?= $total_quizzes ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Students</h5>
                    <h2><?= $total_students ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Attempts</h5>
                    <h2><?= $total_attempts ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow">
                <div class="card-body">
                    <h5>Create Quiz</h5>
                    <p>Create a new quiz and add questions.</p>
                    <a href="create_quiz.php" class="btn btn-primary w-100">Create New Quiz</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow">
                <div class="card-body">
                    <h5>Manage Quizzes</h5>
                    <p>Edit, delete or deactivate quizzes.</p>
                    <a href="manage_quizzes.php" class="btn btn-warning w-100">Manage Quizzes</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow">
                <div class="card-body">
                    <h5>View Results</h5>
                    <p>See all student quiz results.</p>
                    <a href="results.php" class="btn btn-success w-100">View Results</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>