<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Fetch user information from database
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Update profile if form is submitted
$update_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash(sanitize($_POST['password']), PASSWORD_BCRYPT) : null;

    if (!empty($username) && !empty($email)) {
        $query = "UPDATE users SET username = ?, email = ?";
        $params = [$username, $email];

        if ($password) {
            $query .= ", password = ?";
            $params[] = $password;
        }

        $query .= " WHERE id = ?";
        $params[] = $user_id;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $update_msg = 'Profile updated successfully!';

        $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        $_SESSION['username'] = $user['username'];
    } else {
        $update_msg = 'Please fill out all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto max-w-md mt-12 p-6 bg-white rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-8"><i class="fas fa-user-edit"></i> Edit Profile</h1>
        <?php if ($update_msg): ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded" role="alert">
                <p><?php echo $update_msg; ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="" id="profile-form" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-user"></i> Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="relative">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-lock"></i> Password (leave blank to keep current password)</label>
                <input type="password" id="password" name="password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-eye password-toggle absolute right-3 top-9 text-gray-500 cursor-pointer"></i>
            </div>
            <div class="flex justify-center space-x-4 mt-8">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                    <i class="fas fa-save mr-2"></i> Update Profile
                </button>
                <a href="profile.php" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Password visibility toggle
            $('.password-toggle').click(function() {
                var passwordField = $('#password');
                var passwordFieldType = passwordField.attr('type');
                if (passwordFieldType == 'password') {
                    passwordField.attr('type', 'text');
                    $(this).removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Form validation
            $('#profile-form').submit(function(e) {
                var username = $('#username').val().trim();
                var email = $('#email').val().trim();

                if (username === '' || email === '') {
                    e.preventDefault();
                    alert('Please fill out all required fields.');
                }
            });

            // Smooth scroll to top after form submission
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
            $(window).on('load', function() {
                if (location.hash) {
                    setTimeout(function() {
                        window.scrollTo(0, 0);
                    }, 1);
                }
            });
        });
    </script>
</body>
</html>