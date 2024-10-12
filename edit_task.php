<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

if ($task_id === 0) {
    redirect('dashboard.php');
}

// Verify that the task belongs to the current user
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND list_id IN (SELECT id FROM todo_lists WHERE user_id = ?)");
$stmt->execute([$task_id, $user_id]);
$task = $stmt->fetch();

if (!$task) {
    redirect('dashboard.php');
}

// Handle the task update
$update_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_task'])) {
    $updated_description = sanitize($_POST['updated_description']);
    if (!empty($updated_description)) {
        $stmt = $pdo->prepare("UPDATE tasks SET description = ? WHERE id = ?");
        $stmt->execute([$updated_description, $task_id]);
        redirect('tasks.php?list_id=' . $task['list_id']);
    } else {
        $update_msg = 'Please fill out the task description.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        .back-button {
            background-color: #6c63ff; /* Warna tombol sebelum di-hover */
            color: white; /* Warna teks tombol */
            border-radius: 0.375rem; /* Sudut membulat */
            transition: background-color 0.3s; /* Transisi untuk efek hover */
        }
        
        .back-button:hover {
            background-color: #5b54e0; /* Warna tombol saat di-hover */
        }
    </style>
</head>
<body>
    <div class="container mx-auto max-w-md mt-12 p-6 bg-white rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-8"><i class="fas fa-edit"></i> Edit Task</h1>
        
        <?php if ($update_msg): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <p><?php echo $update_msg; ?></p>
            </div>
        <?php endif; ?>

        <a href="tasks.php?list_id=<?php echo $task['list_id']; ?>" class="inline-block mb-4 px-4 py-2 back-button">
            <i class="fas fa-arrow-left mr-2"></i> Back to Tasks
        </a>

        <form method="POST" action="">
            <div class="form-group mb-6">
                <label for="taskDescription" class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-tasks"></i> Task Description</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="updated_description" id="taskDescription" value="<?php echo htmlspecialchars($task['description']); ?>" required>
            </div>
            <button type="submit" name="save_task" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                <i class="fas fa-save mr-2"></i> Save Changes
            </button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</body>
</html>
