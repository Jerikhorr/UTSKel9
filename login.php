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

        .btn-glow {
            transition: all 0.3s ease;
        }

        .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.5);
        }

        .container {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .input-group input {
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.9);
        }

        .input-group input:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2);
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
                <img src="https://img.icons8.com/clouds/100/000000/login-rounded-right.png" alt="Login Icon" class="mx-auto mb-4">
                <h1 class="text-4xl font-bold">Login</h1>
                <p class="text-gray-300 mt-2">Welcome back! Please login to your account.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Login form -->
            <form method="POST" action="" class="space-y-6">
                <div class="input-group">
                    <label for="email" class="block text-sm font-medium mb-1">Email</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <img src="https://img.icons8.com/ios-glyphs/24/999999/email.png" alt="Email Icon" class="w-5 h-5">
                        </span>
                    </div>
                </div>

                <div class="input-group">
                    <label for="password" class="block text-sm font-medium mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <img src="https://img.icons8.com/ios-glyphs/24/999999/lock.png" alt="Lock Icon" class="w-5 h-5">
                        </span>
                        <span id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                            <img src="https://img.icons8.com/ios-filled/16/000000/visible.png" id="passwordIcon" alt="Show Password" class="w-5 h-5"/>
                        </span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 btn-glow">
                    Login
                </button>
            </form>

            <p class="mt-8 text-center text-gray-300">Don't have an account? <a href="register.php" class="text-blue-400 hover:underline">Register here</a></p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordIcon.src = 'https://img.icons8.com/ios-filled/16/000000/invisible.png';
            } else {
                passwordField.type = 'password';
                passwordIcon.src = 'https://img.icons8.com/ios-filled/16/000000/visible.png';
            }
        }

        document.getElementById('togglePassword').addEventListener('click', togglePasswordVisibility);
    </script>
</body>
</html>
