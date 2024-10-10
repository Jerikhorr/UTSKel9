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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_task'])) {
    $updated_description = sanitize($_POST['updated_description']);
    if (!empty($updated_description)) {
        $stmt = $pdo->prepare("UPDATE tasks SET description = ? WHERE id = ?");
        $stmt->execute([$updated_description, $task_id]);
        redirect('tasks.php?list_id=' . $task['list_id']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - Todo List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: rgb(2, 0, 36);
            background: linear-gradient(90deg, rgba(2, 0, 36, 1) 0%, rgba(6, 6, 89, 1) 22%, rgba(9, 9, 121, 1) 35%, rgba(6, 87, 173, 1) 60%, rgba(6, 96, 179, 1) 63%, rgba(0, 212, 255, 1) 100%);
            font-family: Arial, sans-serif;
            color: #fff;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9); /* Kontainer putih transparan */
            border-radius: 8px; /* Sudut membulat */
            padding: 20px; /* Ruang di dalam kontainer */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Bayangan halus */
            margin-top: 50px; /* Jarak atas */
            animation: fadeIn 1s ease-in; /* Animasi fade-in untuk kontainer */
        }

        h1 {
            color: #343a40; /* Warna judul */
            margin-bottom: 20px; /* Jarak bawah judul */
            text-align: center; /* Pusatkan judul */
        }

        .btn-primary {
            background-color: #007bff; /* Warna tombol biru */
            border-color: #007bff; /* Border tombol */
            transition: background-color 0.3s ease; /* Transisi untuk hover */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Warna tombol saat hover */
            border-color: #0056b3; /* Border tombol saat hover */
        }

        .btn-success {
            background-color: #28a745; /* Warna tombol hijau */
            border-color: #28a745; /* Border tombol */
            transition: background-color 0.3s ease; /* Transisi untuk hover */
        }

        .btn-success:hover {
            background-color: #218838; /* Warna tombol saat hover */
            border-color: #1e7e34; /* Border tombol saat hover */
        }

        .form-group label {
            font-weight: bold; /* Tebalkan label */
            color: #343a40; /* Ubah warna label */
        }

        input.form-control {
            transition: transform 0.2s, box-shadow 0.2s; /* Transisi untuk efek */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Task</h1>
        <a href="tasks.php?list_id=<?php echo $task['list_id']; ?>" class="btn btn-primary mb-3">Back to Tasks</a>

        <form method="POST" action="">
            <div class="form-group">
                <label for="taskDescription">Task Description</label>
                <input type="text" class="form-control" name="updated_description" id="taskDescription" value="<?php echo htmlspecialchars($task['description']); ?>" required>
            </div>
            <button type="submit" name="save_task" class="btn btn-success">Save Changes</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
