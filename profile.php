<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Ambil informasi pengguna dari database
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-center">Profile Information</h1>
            <table class="table-auto w-full">
                <tr>
                    <th class="text-left py-2">Username:</th>
                    <td class="py-2"><?php echo $user['username']; ?></td>
                </tr>
                <tr>
                    <th class="text-left py-2">Email:</th>
                    <td class="py-2"><?php echo $user['email']; ?></td>
                </tr>
            </table>
            <div class="flex justify-between mt-6">
                <a href="edit_profile.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit Profile</a>
                <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
