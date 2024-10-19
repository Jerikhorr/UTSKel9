<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Both email and password are required.";
    } else {
        // Update the password in the database
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $email]);
        $success = "Password has been reset successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Todo List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Add your styles here */
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
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                    <p><?php echo $success; ?></p>
                </div>
            <?php endif; ?>

            <!-- Reset Password form -->
            <form method="POST" action="" class="space-y-6">
                <div class="input-group">
                    <label for="email" class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                </div>

                <div class="input-group">
                    <label for="password" class="block text-sm font-medium mb-1">New Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" id="togglePassword">
                            <img src="https://img.icons8.com/ios-filled/16/000000/visible.png" id="passwordIcon" alt="Show Password" class="w-5 h-5"/>
                        </span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    Reset Password
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.src = 'https://img.icons8.com/ios-filled/16/000000/invisible.png';
            } else {
                input.type = 'password';
                icon.src = 'https://img.icons8.com/ios-filled/16/000000/visible.png';
            }
        }

        document.getElementById('togglePassword').addEventListener('click', function() {
            togglePasswordVisibility('password', 'passwordIcon');
        });
    </script>
</body>
</html>
