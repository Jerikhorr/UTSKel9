<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Both email and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            redirect('dashboard.php');
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Todo List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .input-group-text {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Login</h1>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
        ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="togglePassword">
                            <img src="https://img.icons8.com/ios-filled/16/000000/visible.png" id="passwordIcon" alt="Show Password"/>
                        </span>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p class="mt-3">Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');
        
        togglePassword.addEventListener('mousedown', function() {
            passwordField.type = 'text';
            passwordIcon.src = 'https://img.icons8.com/ios-filled/16/000000/invisible.png'; // Change to "invisible" icon
        });

        togglePassword.addEventListener('mouseup', function() {
            passwordField.type = 'password';
            passwordIcon.src = 'https://img.icons8.com/ios-filled/16/000000/visible.png'; // Change back to "visible" icon
        });

        togglePassword.addEventListener('mouseleave', function() {
            passwordField.type = 'password';
            passwordIcon.src = 'https://img.icons8.com/ios-filled/16/000000/visible.png'; // Ensure icon resets when mouse leaves
        });
    </script>
</body>
</html>