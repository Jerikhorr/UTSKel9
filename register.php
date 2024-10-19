<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Username or email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            try {
                $stmt->execute([$username, $email, $hashed_password]);
                redirect('login.php'); 
            } catch (PDOException $e) {
                $errors[] = "An error occurred: " . $e->getMessage(); 
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
            padding-left: 2.5rem;
        }

        .input-group input:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2);
            padding-left: 1rem;
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
                <img src="https://img.icons8.com/clouds/100/000000/add-user-male.png" alt="Register Icon" class="mx-auto mb-4">
                <h1 class="text-4xl font-bold">Create Account</h1>
                <p class="text-gray-300 mt-2">Join us and start organizing your tasks!</p>
            </div>

            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="alert">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <div class="input-group">
                    <label for="username" class="block text-sm font-medium mb-1">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <img src="https://img.icons8.com/ios-glyphs/24/999999/user.png" alt="Username Icon" class="w-5 h-5">
                        </span>
                        <input type="text" id="username" name="username" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="email" class="block text-sm font-medium mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <img src="https://img.icons8.com/ios-glyphs/24/999999/email.png" alt="Email Icon" class="w-5 h-5">
                        </span>
                        <input type="email" id="email" name="email" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="password" class="block text-sm font-medium mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <img src="https://img.icons8.com/ios-glyphs/24/999999/lock.png" alt="Password Icon" class="w-5 h-5">
                        </span>
                        <input type="password" id="password" name="password" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                        <span id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                            <img src="https://img.icons8.com/ios-filled/16/000000/visible.png" id="passwordIcon" alt="Show Password" class="w-5 h-5"/>
                        </span>
                    </div>
                </div>

                <div class="input-group">
                    <label for="confirm_password" class="block text-sm font-medium mb-1">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <img src="https://img.icons8.com/ios-glyphs/24/999999/lock.png" alt="Confirm Password Icon" class="w-5 h-5">
                        </span>
                        <input type="password" id="confirm_password" name="confirm_password" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                        <span id="toggleConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                            <img src="https://img.icons8.com/ios-filled/16/000000/visible.png" id="confirmPasswordIcon" alt="Show Password" class="w-5 h-5"/>
                        </span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 btn-glow">
                    Create Account
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-gray-300">
                 Already have an account? 
                <a href="login.php" class="text-yellow-400 hover:text-yellow-500 hover:underline">Login here</a>
            </p>

        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');
        
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            passwordIcon.src = type === 'password' ? "https://img.icons8.com/ios-filled/16/000000/visible.png" : "https://img.icons8.com/ios-filled/16/000000/invisible.png";
        });

        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPassword = document.getElementById('confirm_password');
        const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');
        
        toggleConfirmPassword.addEventListener('click', function () {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            confirmPasswordIcon.src = type === 'password' ? "https://img.icons8.com/ios-filled/16/000000/visible.png" : "https://img.icons8.com/ios-filled/16/000000/invisible.png";
        });
    </script>
</body>
</html>
