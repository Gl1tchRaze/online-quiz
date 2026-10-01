<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get all results with student name and quiz title
$results = mysqli_query($conn,
    "SELECT qa.*, u.name as student_name, q.title as quiz_title
     FROM quiz_attempts qa
     JOIN users u ON qa.user_id = u.id
     JOIN quizzes q ON qa.quiz_id = q.id
     ORDER BY qa.submitted_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Results</title>
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
    <h4 class="mb-4">All Student Results</h4>

    <?php if(mysqli_num_rows($results) == 0): ?>
        <div class="alert alert-info">No results found yet.</div>
    <?php else: ?>
    <table class="table table-bordered table-hover shadow">
        <thead class="table-dark">
            <tr>
                <th>Student Name</th>
                <th>Quiz Title</th>
                <th>Score</th>
                <th>Total Marks</th>
                <th>Percentage</th>
                <th>Grade</th>
                <th>Submitted At</th>
            </tr>
        </thead>
        <tbody>
        <?php while($r = mysqli_fetch_assoc($results)):
           $percent = $r['total_marks'] > 0 ? round(($r['score'] / $r['total_marks']) * 100) : 0;
            if($percent >= 80)      $grade = 'A';
            elseif($percent >= 60)  $grade = 'B';
            elseif($percent >= 40)  $grade = 'C';
            else                    $grade = 'F';

            $badge = $percent >= 80 ? 'success' : ($percent >= 60 ? 'primary' : ($percent >= 40 ? 'warning' : 'danger'));
        ?>
            <tr>
                <td><?= $r['student_name'] ?></td>
                <td><?= $r['quiz_title'] ?></td>
                <td><?= $r['score'] ?></td>
                <td><?= $r['total_marks'] ?></td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-<?= $badge ?>" style="width: <?= $percent ?>%">
                            <?= $percent ?>%
                        </div>
                    </div>
                </td>
                <td><span class="badge bg-<?= $badge ?>"><?= $grade ?></span></td>
                <td><?= date('d M Y, h:i A', strtotime($r['submitted_at'])) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

</body>
</html>