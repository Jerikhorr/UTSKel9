<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

$todo_lists = [];
$tasks = [];

$stmt = $pdo->prepare("SELECT * FROM todo_lists WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$todo_lists = $stmt->fetchAll();

$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'all';

$task_query = "SELECT t.*, l.title as list_title FROM tasks t 
               JOIN todo_lists l ON t.list_id = l.id 
               WHERE l.user_id = ?";

if (!empty($search_query)) {
    $task_query .= " AND t.description LIKE ?";
}

if ($filter === 'completed') {
    $task_query .= " AND t.is_completed = 1";
} elseif ($filter === 'incomplete') {
    $task_query .= " AND t.is_completed = 0";
}

$task_query .= " ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($task_query);

$params = [$user_id];
if (!empty($search_query)) {
    $params[] = "%$search_query%";
}

$stmt->execute($params);
$tasks = $stmt->fetchAll();

$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'all';

$task_query = "SELECT t.*, l.title as list_title FROM tasks t 
               JOIN todo_lists l ON t.list_id = l.id 
               WHERE l.user_id = ?";

if (!empty($search_query)) {
    $task_query .= " AND t.description LIKE ?";
}

if ($filter === 'completed') {
    $task_query .= " AND t.is_completed = 1";
} elseif ($filter === 'incomplete') {
    $task_query .= " AND t.is_completed = 0";
}

$task_query .= " ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($task_query);

$params = [$user_id];
if (!empty($search_query)) {
    $params[] = "%$search_query%";
}

$stmt->execute($params);
$tasks = $stmt->fetchAll();

if (isset($_POST['new_list'])) {
    $list_title = sanitize($_POST['list_title']);
    $list_category = sanitize($_POST['list_category']);

    $stmt = $pdo->prepare("INSERT INTO todo_lists (user_id, title, category) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $list_title, $list_category]);

    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['delete_list'])) {
    $list_id = sanitize($_POST['list_id']);

    $stmt = $pdo->prepare("DELETE FROM tasks WHERE list_id = ?");
    $stmt->execute([$list_id]);

    $stmt = $pdo->prepare("DELETE FROM todo_lists WHERE id = ? AND user_id = ?");
    $stmt->execute([$list_id, $user_id]);

    
    header("Location: dashboard.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Todo List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <nav class="bg-indigo-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-white text-xl font-bold">Super Todo</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="profile.php" class="text-gray-300 hover:bg-indigo-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Profile</a>
                    <a href="logout.php" class="text-gray-300 hover:bg-indigo-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <i class="fas fa-list text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Lists</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo count($todo_lists); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <i class="fas fa-tasks text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Tasks</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo count($tasks); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <i class="fas fa-check-circle text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Completed Tasks</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo count(array_filter($tasks, function($task) { return $task['is_completed']; })); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-8">
    <form method="GET" action="" class="flex items-center space-x-2 bg-white p-2 rounded-lg shadow-md">
        <div class="flex-grow">
            <input type="text" name="search" placeholder="Search tasks" value="<?php echo htmlspecialchars($search_query); ?>" class="w-full px-4 py-2 rounded-md border border-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 transition duration-150 ease-in-out">
        </div>
        <div class="w-48">
            <select name="filter" class="w-full rounded-md border border-gray-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 transition duration-150 ease-in-out">
                <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Tasks</option>
                <option value="completed" <?php echo $filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="incomplete" <?php echo $filter === 'incomplete' ? 'selected' : ''; ?>>Incomplete</option>
            </select>
        </div>
        <div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md border border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                <i class="fas fa-search mr-2"></i>Search
            </button>
        </div>
    </form>
</div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Your Lists</h2>
                <?php if (empty($todo_lists)): ?>
                    <p class="text-gray-500">You don't have any lists yet. Create one to get started!</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($todo_lists as $list): ?>
                            <div class="bg-white shadow rounded-lg p-4 hover:shadow-md transition duration-300">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($list['title']); ?></h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <?php echo htmlspecialchars($list['category']); ?>
                                </span>
                                <div class="mt-4 flex space-x-2">
                                    <a href="tasks.php?list_id=<?php echo $list['id']; ?>" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        View Tasks
                                    </a>
                                    <button onclick="showDeleteModal(<?php echo $list['id']; ?>)" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div>
            <div>
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Recent Tasks</h2>
    <?php if (empty($tasks)): ?>
        <p class="text-gray-500">You don't have any tasks yet. Create a list and add some tasks to get started!</p>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach (array_slice($tasks, 0, 5) as $task): ?>
                <a href="tasks.php?list_id=<?php echo $task['list_id']; ?>" class="block hover:shadow-lg transition duration-300">
                    <div class="bg-white shadow rounded-lg p-4 <?php echo $task['is_completed'] ? 'border-l-4 border-green-500' : ''; ?>">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($task['description']); ?></h3>
                        <p class="text-sm text-gray-500">
                            <span class="mr-2"><i class="fas fa-clipboard-list mr-1"></i><?php echo htmlspecialchars($task['list_title']); ?></span>
                            <span><i class="fas fa-calendar-alt mr-1"></i><?php echo date('M d, Y', strtotime($task['created_at'])); ?></span>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

        <button onclick="openNewListModal()" class="fixed bottom-8 right-8 bg-indigo-600 text-white rounded-lg p-4 shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 ease-in-out transform hover:scale-105">
    <i class="fas fa-plus text-xl"></i>
    <span class="ml-2 font-semibold">Add List</span>
</button>
    </div>

 <!-- New List Modal -->
<div id="newListModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form method="POST" action="">
                <div class="bg-white px-8 py-8">
                    <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-6" id="modal-title">Create New List</h3>
                    <div class="space-y-8">
                        <div>
                            <label for="list_title" class="block text-lg font-medium text-gray-700 mb-2">List Title</label>
                            <input type="text" name="list_title" id="list_title" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-lg border-2 border-gray-300 rounded-md p-4">
                            <p class="mt-2 text-sm text-gray-500">Choose a clear and descriptive name for your list</p>
                        </div>
                        <div>
                            <label for="list_category" class="block text-lg font-medium text-gray-700 mb-2">Category</label>
                            <select name="list_category" id="list_category" class="mt-1 block w-full py-4 px-4 border-2 border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-lg" required>
                                <option value="" disabled selected>Select a category</option>
                                <option value="Work">Work</option>
                                <option value="Personal">Personal</option>
                                <option value="Groceries">Groceries</option>
                                <option value="Other">Other</option>
                            </select>
                            <p class="mt-2 text-sm text-gray-500">Categorize your list for better organization</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-8 py-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" name="new_list" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-3 bg-indigo-600 text-lg font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto transition duration-150 ease-in-out">
                        Create List
                    </button>
                    <button type="button" onclick="closeNewListModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-6 py-3 bg-white text-lg font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto transition duration-150 ease-in-out">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Delete Confirmation</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">Are you sure you want to delete this To-Do list? This action cannot be undone.</p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form id="deleteForm" method="POST" action="">
                        <input type="hidden" name="list_id" id="modalListId">
                        <button type="submit" name="delete_list" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                    </form>
                    <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openNewListModal() {
            document.getElementById('newListModal').classList.remove('hidden');
        }

        function closeNewListModal() {
            document.getElementById('newListModal').classList.add('hidden');
        }

        function showDeleteModal(listId) {
            document.getElementById('modalListId').value = listId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            let newListModal = document.getElementById('newListModal');
            let deleteModal = document.getElementById('deleteModal');
            if (event.target == newListModal) {
                closeNewListModal();
            }
            if (event.target == deleteModal) {
                closeDeleteModal();
            }
        }
    </script>
</body>
</html>