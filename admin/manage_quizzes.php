<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$success = "";
$error = "";

// Delete quiz
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM answers WHERE attempt_id IN 
                        (SELECT id FROM quiz_attempts WHERE quiz_id='$id')");
    mysqli_query($conn, "DELETE FROM quiz_attempts WHERE quiz_id='$id'");
    mysqli_query($conn, "DELETE FROM options WHERE question_id IN 
                        (SELECT id FROM questions WHERE quiz_id='$id')");
    mysqli_query($conn, "DELETE FROM questions WHERE quiz_id='$id'");
    mysqli_query($conn, "DELETE FROM quizzes WHERE id='$id'");
    $success = "Quiz deleted successfully!";
}

// Toggle active/inactive
if(isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $quiz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT is_active FROM quizzes WHERE id='$id'"));
    $new_status = $quiz['is_active'] ? 0 : 1;
    mysqli_query($conn, "UPDATE quizzes SET is_active='$new_status' WHERE id='$id'");
    $success = "Quiz status updated!";
}

// Update quiz info
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id          = $_POST['quiz_id'];
    $title       = $_POST['title'];
    $description = $_POST['description'];
    $time_limit  = $_POST['time_limit'];

    mysqli_query($conn, "UPDATE quizzes SET title='$title', description='$description', 
                         time_limit='$time_limit' WHERE id='$id'");
    $success = "Quiz updated successfully!";
}

// Get all quizzes
$quizzes = mysqli_query($conn, "SELECT * FROM quizzes ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Quizzes</title>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Manage Quizzes</h4>
        <a href="create_quiz.php" class="btn btn-primary">+ Create New Quiz</a>
    </div>

    <?php if($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if(mysqli_num_rows($quizzes) == 0): ?>
        <div class="alert alert-info">No quizzes found. Create one!</div>
    <?php else: ?>
    <table class="table table-bordered table-hover shadow">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Time Limit</th>
                <th>Status</th>
                <th>Questions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php $i = 1; while($quiz = mysqli_fetch_assoc($quizzes)):
            $q_count = mysqli_fetch_assoc(mysqli_query($conn, 
                "SELECT COUNT(*) as total FROM questions WHERE quiz_id='{$quiz['id']}'"))['total'];
        ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $quiz['title'] ?></td>
                <td><?= $quiz['time_limit'] ?> min</td>
                <td>
                    <?php if($quiz['is_active']): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </td>
                <td><?= $q_count ?> questions</td>
                <td>
                    <!-- Edit Button -->
                    <button class="btn btn-warning btn-sm" 
                        onclick="openEdit(<?= $quiz['id'] ?>, '<?= addslashes($quiz['title']) ?>', 
                        '<?= addslashes($quiz['description']) ?>', <?= $quiz['time_limit'] ?>)">
                        Edit
                    </button>

                    <!-- Add Questions -->
                    <a href="add_questions.php?quiz_id=<?= $quiz['id'] ?>" 
                       class="btn btn-info btn-sm">+ Questions</a>

                    <!-- Toggle Active -->
                    <a href="manage_quizzes.php?toggle=<?= $quiz['id'] ?>" 
                       class="btn btn-secondary btn-sm"
                       onclick="return confirm('Change quiz status?')">
                       <?= $quiz['is_active'] ? 'Deactivate' : 'Activate' ?>
                    </a>

                    <!-- Delete -->
                    <a href="manage_quizzes.php?delete=<?= $quiz['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure? This will delete all questions and results!')">
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Quiz</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="quiz_id" id="edit_id">
                    <div class="mb-3">
                        <label>Quiz Title</label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Time Limit (minutes)</label>
                        <input type="number" name="time_limit" id="edit_time" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openEdit(id, title, description, time_limit) {
    document.getElementById('edit_id').value          = id;
    document.getElementById('edit_title').value       = title;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_time').value        = time_limit;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

</body>
</html>