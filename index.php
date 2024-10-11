<?php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Todo List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 2s ease-in-out;
        }

        body {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: linear-gradient(45deg, #4a90e2, #63b3ed);
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(105, 145, 175, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 7px 14px rgba(105, 145, 175, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #48bb78, #68d391);
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(104, 182, 132, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-5px);
            box-shadow: 0 7px 14px rgba(104, 182, 132, 0.4);
        }

        h1 {
            color: #ffffff;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.6);
        }

        p {
            color: #f1f1f1;
            text-shadow: 1px 1px 6px rgba(0, 0, 0, 0.4);
        }

        a {
            color: #e0e0e0;
            text-decoration: underline;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen font-sans">

    <div class="fade-in">
        <div class="glass-effect text-center w-full max-w-md mx-auto">
            <h1 class="text-4xl font-bold mb-6">Welcome to Todo List</h1>
            <p class="text-lg mb-6">Please login or register to continue.</p>
            <img src="https://tse2.mm.bing.net/th?id=OIP.emteEutAX1P0dsmvsb-D1wHaHa&pid=Api&P=0&h=180" alt="Todo Icon" class="mx-auto mb-8 w-20 h-20 object-cover">

            <div class="flex justify-center space-x-4">
                <a href="login.php" class="btn-primary text-white px-6 py-3 rounded-full text-lg font-semibold shadow-lg transition duration-300">Login</a>
                <a href="register.php" class="btn-secondary text-white px-6 py-3 rounded-full text-lg font-semibold shadow-lg transition duration-300">Register</a>
            </div>

            <p class="mt-6 text-sm">
                By continuing, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
            </p>
        </div>
    </div>

</body>
</html>
