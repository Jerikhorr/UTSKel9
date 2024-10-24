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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .new-task {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-5xl mx-auto my-8 bg-white rounded-2xl shadow-sm px-8 py-6">
        <div class="flex items-center justify-between mb-8 pb-4 border-b-2 border-gray-100">
            <h2 class="text-2xl font-semibold text-gray-800"><?php echo htmlspecialchars($list['title']); ?></h2>
            <a href="dashboard.php" class="flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-600 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>

        <form method="POST" action="" class="mb-8">
            <div class="flex gap-2">
                <input type="text" 
                       class="flex-1 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition-all outline-none" 
                       name="task_description" 
                       placeholder="What needs to be done?" 
                       required>
                <button type="submit" 
                        name="new_task" 
                        class="px-6 py-3 bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>Add Task
                </button>
            </div>
        </form>

        <div class="rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="w-1/2 px-4 py-4 text-left font-semibold text-gray-600">Task</th>
                        <th class="w-1/4 px-4 py-4 text-center font-semibold text-gray-600">Status</th>
                        <th class="w-1/4 px-16 py-4 text-right font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tasks)): ?>
                    <tr>
                        <td colspan="3" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-tasks fa-2x mb-3"></i>
                            <p class="mb-0">No tasks yet. Add your first task above!</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                    <tr class="border-t border-gray-200 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4 font-medium text-gray-800"><?php echo htmlspecialchars($task['description']); ?></td>
                        <td class="px-4 py-4 text-center">
                            <form method="POST" action="" class="inline">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" 
                                        name="toggle_task" 
                                        class="<?php echo $task['is_completed'] 
                                            ? 'bg-green-100 text-green-700 hover:bg-green-200' 
                                            : 'bg-amber-100 text-amber-700 hover:bg-amber-200'; ?> 
                                            px-4 py-2 rounded-full font-medium text-sm w-32 transition-colors">
                                    <?php if ($task['is_completed']): ?>
                                        <i class="fas fa-check mr-2"></i>Completed
                                    <?php else: ?>
                                        <i class="fas fa-clock mr-2"></i>In Progress
                                    <?php endif; ?>
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex justify-end gap-2">
                                <form method="GET" action="edit_task.php" class="inline">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" 
                                            class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg border border-gray-200 hover:bg-gray-200 transition-colors text-sm font-medium">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                </form>
                                <button type="button" 
                                        class="px-4 py-2 bg-red-50 text-red-600 rounded-lg border border-red-100 hover:bg-red-100 transition-colors text-sm font-medium"
                                        onclick="openDeleteModal(<?php echo $task['id']; ?>, '<?php echo htmlspecialchars($task['description']); ?>')">
                                    <i class="fas fa-trash-alt mr-1"></i>Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>
                    <h5 class="text-lg font-semibold text-gray-800">Delete Task</h5>
                </div>
            </div>
            <div class="px-6 py-4">
                <p class="mb-1 text-gray-600">Are you sure you want to delete this task? This action cannot be undone.</p>
                <p class="mb-0"><strong>Task:</strong> <span id="taskDescription" class="text-gray-800"></span></p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <form method="POST" action="" id="deleteForm">
                    <input type="hidden" name="task_id" id="modalTaskId">
                    <button type="button" 
                            onclick="closeDeleteModal()" 
                            class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit" 
                            name="delete_task" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                        <i class="fas fa-trash-alt mr-1"></i>Delete Task
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openDeleteModal(taskId, description) {
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('modalTaskId').value = taskId;
            document.getElementById('taskDescription').textContent = description;
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Add fade-in animation for new tasks
        document.querySelector('form').addEventListener('submit', function() {
            setTimeout(function() {
                const firstRow = document.querySelector('tbody tr:first-child');
                if (firstRow) {
                    firstRow.classList.add('new-task');
                }
            }, 100);
        });
    </script>
</body>
</html>