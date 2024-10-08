<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$list_id = isset($_GET['list_id']) ? intval($_GET['list_id']) : 0;

if ($list_id === 0) {
    redirect('dashboard.php');
}

// Verify that the list belongs to the current user
$stmt = $pdo->prepare("SELECT * FROM todo_lists WHERE id = ? AND user_id = ?");
$stmt->execute([$list_id, $user_id]);
$list = $stmt->fetch();

if (!$list) {
    redirect('dashboard.php');
}

// Add new task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_task'])) {
    $task_description = sanitize($_POST['task_description']);
    if (!empty($task_description)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (list_id, description) VALUES (?, ?)");
        $stmt->execute([$list_id, $task_description]);
    }
}

// Toggle task completion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_task'])) {
    $task_id = intval($_POST['task_id']);
    $stmt = $pdo->prepare("UPDATE tasks SET is_completed = NOT is_completed WHERE id = ? AND list_id = ?");
    $stmt->execute([$task_id, $list_id]);
}

// Edit task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_task'])) {
    $task_id = intval($_POST['task_id']);
    $updated_description = sanitize($_POST['updated_description']);
    if (!empty($updated_description)) {
        $stmt = $pdo->prepare("UPDATE tasks SET description = ? WHERE id = ? AND list_id = ?");
        $stmt->execute([$updated_description, $task_id, $list_id]);
    }
}

// Delete task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_task'])) {
    $task_id = intval($_POST['task_id']);
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND list_id = ?");
    $stmt->execute([$task_id, $list_id]);
}

// Fetch tasks
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE list_id = ? ORDER BY created_at DESC");
$stmt->execute([$list_id]);
$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $list['title']; ?> - Todo List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1><?php echo $list['title']; ?></h1>
        <a href="dashboard.php" class="btn btn-primary mb-3">Back to Dashboard</a>

        <form method="POST" action="" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="task_description" placeholder="New task description" required>
                <div class="input-group-append">
                    <button type="submit" name="new_task" class="btn btn-primary">Add Task</button>
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td>
                            <?php if (isset($_POST['edit_task']) && $_POST['task_id'] == $task['id']): ?>
                                <form method="POST" action="" class="d-inline">
                                    <input type="text" name="updated_description" value="<?php echo htmlspecialchars($task['description']); ?>" required>
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" name="save_task" class="btn btn-sm btn-success">Save</button>
                                    <a href="" class="btn btn-sm btn-secondary">Cancel</a>
                                </form>
                            <?php else: ?>
                                <?php echo $task['description']; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $task['is_completed'] ? 'Completed' : 'Incomplete'; ?></td>
                        <td>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" name="toggle_task" class="btn btn-sm btn-info">
                                    <?php echo $task['is_completed'] ? 'Mark Incomplete' : 'Mark Complete'; ?>
                                </button>
                            </form>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" name="edit_task" class="btn btn-sm btn-warning">Edit</button>
                            </form>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" name="delete_task" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
