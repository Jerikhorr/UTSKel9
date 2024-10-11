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

$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND list_id IN (SELECT id FROM todo_lists WHERE user_id = ?)");
$stmt->execute([$task_id, $user_id]);
$task = $stmt->fetch();

if (!$task) {
    redirect('dashboard.php');
}

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
