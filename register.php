<?php
session_start();
require 'config/db.php';

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $role     = $_POST['role'];

    if($password !== $confirm) {
        $error = "Passwords do not match!";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $check  = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");

        if(mysqli_num_rows($check) > 0) {
            $error = "An account with this email already exists!";
        } else {
            $sql = "INSERT INTO users (name, email, password, role) 
                    VALUES ('$name', '$email', '$hashed', '$role')";
            if(mysqli_query($conn, $sql)) {
                $success = "Account created successfully! Please login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Create Account</h3>

                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label>Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" 
                                       class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" 
                                        onclick="togglePassword('password', 'eye1')">
                                    <i class="bi bi-eye" id="eye1"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <div class="input-group">
                                <input type="password" name="confirm_password" id="confirm_password" 
                                       class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" 
                                        onclick="togglePassword('confirm_password', 'eye2')">
                                    <i class="bi bi-eye" id="eye2"></i>
                                </button>
                            </div>
                            <small id="match_msg" class="text-muted"></small>
                        </div>

                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role" class="form-select">
                                <option value="student">Student</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>

                    <p class="text-center mt-3">
                        Already have an account? <a href="login.php">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show/Hide password
function togglePassword(fieldId, iconId) {
    var field = document.getElementById(fieldId);
    var icon  = document.getElementById(iconId);
    if(field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

// Live password match check
document.getElementById('confirm_password').addEventListener('keyup', function() {
    var pass    = document.getElementById('password').value;
    var confirm = this.value;
    var msg     = document.getElementById('match_msg');
    if(confirm === '') {
        msg.textContent = '';
    } else if(pass === confirm) {
        msg.textContent = ' Passwords match!';
        msg.className   = 'text-success';
    } else {
        msg.textContent = ' Passwords do not match!';
        msg.className   = 'text-danger';
    }
});
</script>

</body>
</html>