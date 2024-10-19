<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $errors[] = "Email, new password, and confirmation password are required.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "New password and confirmation password do not match.";
    } else {
        // Logika untuk reset password
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password in the database
            $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update_stmt->execute([$hashed_password, $email]);

            // Password reset berhasil
            redirect('login.php'); // Ganti dengan URL halaman login atau halaman yang diinginkan
        } else {
            $errors[] = "No user found with that email address.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Todo List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        body {
            background-image: linear-gradient(135deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .container {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        label, p, h1 {
            color: #ffffff;
        }

        .alert {
            background-color: rgba(255, 0, 0, 0.8); 
            color: #ffffff; 
            border-left: 4px solid #ff0000; 
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="flex justify-center items-center min-h-screen bg-gray-900">
    <div class="fade-in w-full max-w-md">
        <div class="container p-8 md:p-10">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold">Reset Password</h1>
                <p class="text-gray-300 mt-2">Please enter your email and new password.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="alert">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Reset Password Form -->
            <form method="POST" action="" class="space-y-6">
                <div class="input-group">
                    <label for="email" class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" id="email" name="email" class="w-full border border-gray-300 rounded-lg py-2 px-3 focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                </div>

                <div class="input-group">
                    <label for="new_password" class="block text-sm font-medium mb-1">New Password</label>
                    <div class="relative">
                        <input type="password" id="new_password" name="new_password" class="w-full border border-gray-300 rounded-lg py-2 px-3 focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                        <span id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                            <img src="https://img.icons8.com/ios-filled/16/000000/visible.png" id="passwordIcon" alt="Show Password" class="w-5 h-5"/>
                        </span>
                    </div>
                </div>

                <div class="input-group">
                    <label for="confirm_password" class="block text-sm font-medium mb-1">Confirm New Password</label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password" class="w-full border border-gray-300 rounded-lg py-2 px-3 focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                        <span id="toggleConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                            <img src="https://img.icons8.com/ios-filled/16/000000/visible.png" id="confirmPasswordIcon" alt="Show Password" class="w-5 h-5"/>
                        </span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    Reset Password
                </button>
            </form>

            <p class="mt-8 text-center text-gray-300">Remembered your password? <a href="login.php" class="text-yellow-400 hover:text-yellow-500 hover:underline">Login here</a></p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('new_password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordIcon.src = 'https://img.icons8.com/ios-filled/16/000000/invisible.png';
            } else {
                passwordField.type = 'password';
                passwordIcon.src = 'https://img.icons8.com/ios-filled/16/000000/visible.png';
            }
        }

        function toggleConfirmPasswordVisibility() {
            const confirmPasswordField = document.getElementById('confirm_password');
            const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');
            
            if (confirmPasswordField.type === 'password') {
                confirmPasswordField.type = 'text';
                confirmPasswordIcon.src = 'https://img.icons8.com/ios-filled/16/000000/invisible.png';
            } else {
                confirmPasswordField.type = 'password';
                confirmPasswordIcon.src = 'https://img.icons8.com/ios-filled/16/000000/visible.png';
            }
        }

        document.getElementById('togglePassword').addEventListener('click', togglePasswordVisibility);
        document.getElementById('toggleConfirmPassword').addEventListener('click', toggleConfirmPasswordVisibility);
    </script>
</body>
</html>
