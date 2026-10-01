<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$results = mysqli_query($conn, 
    "SELECT qa.*, q.title, q.time_limit 
     FROM quiz_attempts qa 
     JOIN quizzes q ON qa.quiz_id = q.id 
     WHERE qa.user_id = '$user_id' 
     ORDER BY qa.submitted_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-success px-4">
    <span class="navbar-brand">Online Quiz System</span>
    <div>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">Back to Quizzes</a>
        <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <h4 class="mb-4">My Results</h4>

    <?php if(mysqli_num_rows($results) == 0): ?>
        <div class="alert alert-info">You have not attempted any quiz yet.</div>
    <?php else: ?>
    <table class="table table-bordered table-hover shadow">
        <thead class="table-dark">
            <tr>
                <th>Quiz Title</th>
                <th>Score</th>
                <th>Total Marks</th>
                <th>Percentage</th>
                <th>Grade</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        <?php while($r = mysqli_fetch_assoc($results)):
            $percent = round(($r['score'] / $r['total_marks']) * 100);
            if($percent >= 80) $grade = 'A';
            elseif($percent >= 60) $grade = 'B';
            elseif($percent >= 40) $grade = 'C';
            else $grade = 'F';
        ?>
            <tr>
                <td><?= $r['title'] ?></td>
                <td><?= $r['score'] ?></td>
                <td><?= $r['total_marks'] ?></td>
                <td><?= $percent ?>%</td>
                <td><strong><?= $grade ?></strong></td>
                <td><?= date('d M Y', strtotime($r['submitted_at'])) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

</body>
</html>