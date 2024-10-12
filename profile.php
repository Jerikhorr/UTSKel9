<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Ambil informasi pengguna dari database
$stmt = $pdo->prepare("SELECT username, email, profile_picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Path default untuk gambar profil
$defaultImagePath = 'images/default-avatar.png';

// Cek apakah pengguna memiliki gambar profil
$userImagePath = !empty($user['profile_picture']) ? 'images/' . $user['profile_picture'] : $defaultImagePath;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .profile-transition {
            transition: all 0.3s ease-in-out;
        }
        .profile-transition:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            max-height: 80%;
            animation-name: zoom;
            animation-duration: 0.6s;
        }
        @keyframes zoom {
            from {transform:scale(0)}
            to {transform:scale(1)}
        }
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }
        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gradient-to-r from-blue-100 to-purple-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto p-6">
        
        <div class="bg-white p-8 rounded-2xl shadow-lg max-w-md mx-auto profile-transition">
            <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-700 mb-4">Profile Information</h2>
                <img src="<?php echo htmlspecialchars($userImagePath); ?>" alt="Profile Picture" class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-blue-500 shadow-md cursor-pointer" id="profilePic">
                <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($user['username']); ?></h1>
                <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="space-y-4">
                <div class="flex items-center p-3 bg-gray-100 rounded-lg">
                    <i class="fas fa-user text-blue-500 mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Username</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($user['username']); ?></p>
                    </div>
                </div>
                <div class="flex items-center p-3 bg-gray-100 rounded-lg">
                    <i class="fas fa-envelope text-blue-500 mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
            </div>
            <div class="flex justify-between mt-8">
                <a href="edit_profile.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                    <i class="fas fa-edit mr-2"></i>Edit Profile
                </a>
                <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="imageModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="modalImg">
    </div>

    <script>
        // Animasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelector('.profile-transition').style.opacity = '0';
            setTimeout(() => {
                document.querySelector('.profile-transition').style.opacity = '1';
            }, 100);
        });

        // Script untuk modal
        var modal = document.getElementById("imageModal");
        var img = document.getElementById("profilePic");
        var modalImg = document.getElementById("modalImg");
        var span = document.getElementsByClassName("close")[0];

        img.onclick = function() {
            modal.style.display = "flex";
            modalImg.src = this.src;
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>