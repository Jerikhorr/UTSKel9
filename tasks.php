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
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 900px;
            margin-top: 40px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .btn-custom {
            background-color: #6c63ff;
            color: #fff;
        }

        .btn-custom:hover {
            box-shadow: 0 0 0 0 rem;
            background-color: #7300FF;
            color: #fff;

        }

        .badge-completed {
            background-color: #28a745;
            color: white;
        }

        .badge-incomplete {
            background-color: #ffc107;
            color: #343a40;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .form-control {
            border-radius: 20px;
        }

        .input-group-append .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-radius: 20px;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .modal-header {
            background-color: #6c63ff;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4"><?php echo htmlspecialchars($list['title']); ?></h2>
        <a href="dashboard.php" class="btn btn-custom mb-3">Back to Dashboard</a>

        <form method="POST" action="" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="task_description" placeholder="New task description" required>
                <div class="input-group-append">
                    <button type="submit" name="new_task" class="btn btn-custom">Add Task</button>
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Task</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['description']); ?></td>
                        <td class="text-center">
                            <span class="badge <?php echo $task['is_completed'] ? 'badge-completed' : 'badge-incomplete'; ?> btn">
                                <?php echo $task['is_completed'] ? 'Completed' : 'Incomplete'; ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" name="toggle_task" class="btn btn-sm btn-<?php echo $task['is_completed'] ? 'info' : 'warning'; ?>">
                                    <?php echo $task['is_completed'] ? 'Mark Incomplete' : 'Mark Complete'; ?>
                                </button>
                            </form>
                            <form method="GET" action="edit_task.php" class="d-inline">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-warning">Edit</button>
                            </form>
                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $task['id']; ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this task? This action cannot be undone.</p>
                    <p><strong>Task Description:</strong> <span id="taskDescription"></span></p>
                </div>
                <div class="modal-footer">
                    <form method="POST" action="" id="deleteForm">
                        <input type="hidden" name="task_id" id="modalTaskId">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_task" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var taskId = button.data('id');
            var taskDescription = button.closest('tr').find('td:first').text(); // Get the task description

            var modal = $(this);
            modal.find('#modalTaskId').val(taskId);
            modal.find('#taskDescription').text(taskDescription); // Set the task description in the modal
        });
    </script>
</body>
</html>
