<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    // Validasi input
    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    } else {
        // Cek apakah username atau email sudah ada
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Username or email already exists.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user baru ke database
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            try {
                $stmt->execute([$username, $email, $hashed_password]);
                redirect('login.php'); // Redirect ke halaman login setelah berhasil
            } catch (PDOException $e) {
                $errors[] = "An error occurred: " . $e->getMessage(); // Tampilkan error
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Todo List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .input-group-text {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Register</h1>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
        ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
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
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="toggleConfirmPassword">
                            <img src="https://img.icons8.com/ios-filled/16/000000/visible.png" id="confirmPasswordIcon" alt="Show Password"/>
                        </span>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script>
        // Toggle password visibility for the password field
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

        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordField = document.getElementById('confirm_password');
        const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');
        
        toggleConfirmPassword.addEventListener('mousedown', function() {
            confirmPasswordField.type = 'text';
            confirmPasswordIcon.src = 'https://img.icons8.com/ios-filled/16/000000/invisible.png'; // Change to "invisible" icon
        });

        toggleConfirmPassword.addEventListener('mouseup', function() {
            confirmPasswordField.type = 'password';
            confirmPasswordIcon.src = 'https://img.icons8.com/ios-filled/16/000000/visible.png'; // Change back to "visible" icon
        });

        toggleConfirmPassword.addEventListener('mouseleave', function() {
            confirmPasswordField.type = 'password';
            confirmPasswordIcon.src = 'https://img.icons8.com/ios-filled/16/000000/visible.png'; // Ensure icon resets when mouse leaves
        });
    </script>
</body>
</html>