<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT username, email, profile_picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$update_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash(sanitize($_POST['password']), PASSWORD_BCRYPT) : null;

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$username, $user_id]);
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        $update_msg = 'Username is already taken, please choose another one.';
    } else {
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

            $stmt = $pdo->prepare("SELECT username, email, profile_picture FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            $_SESSION['username'] = $user['username'];
        } else {
            $update_msg = 'Please fill out all required fields.';
        }

        if (!empty($_FILES['profile_picture']['name'])) {
            $profile_picture = $_FILES['profile_picture'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 1024 * 1024; // 1MB

            if ($profile_picture['size'] > $max_size) {
                $update_msg = 'Profile picture is too large. Maximum size is 1MB.';
            } elseif (!in_array($profile_picture['type'], $allowed_types)) {
                $update_msg = 'Invalid profile picture type. Only JPEG, PNG, and GIF are allowed.';
            } else {
                $upload_dir = 'images/';
                $profile_picture_name = uniqid() . '_' . $profile_picture['name'];
                $profile_picture_path = $upload_dir . $profile_picture_name;

                if (move_uploaded_file($profile_picture['tmp_name'], $profile_picture_path)) {
                    $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                    $stmt->execute([$profile_picture_name, $user_id]);

                    $update_msg = 'Profile picture updated successfully!';
                } else {
                    $update_msg = 'Failed to upload profile picture.';
                }
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
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#username').on('keyup', function() {
            var username = $(this).val().trim();
            if (username.length > 0) {
                $.ajax({
                    url: 'check_username.php',
                    method: 'POST',
                    data: { username: username },
                    success: function(response) {
                        if (response == 'taken') {
                            $('#username-status').text('Username is already taken').css('color', 'red');
                        } else {
                            $('#username-status').text('Username is available').css('color', 'green');
                        }
                    }
                });
            } else {
                $('#username-status').text('');
            }
        });
    });
</script>

</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto max-w-md mt-12 p-6 bg-white rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-8"><i class="fas fa-user-edit"></i> Edit Profile</h1>
        <?php if ($update_msg): ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded" role="alert">
                <p><?php echo $update_msg; ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="" id="profile-form" class="space-y-6" enctype="multipart/form-data">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-user"></i> Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                       <div id="username-status" class="text-sm mt-2"></div>

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
            <div>
                <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-camera"></i> Profile Picture</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg, image/png, image/gif" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
