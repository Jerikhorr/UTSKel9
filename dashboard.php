<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_list'])) {
    $list_title = sanitize($_POST['list_title']);  // Ambil judul yang diinput user
    $list_category = sanitize($_POST['list_category']);  // Ambil kategori yang dipilih user

    if (!empty($list_title) && !empty($list_category)) {
        $stmt = $pdo->prepare("INSERT INTO todo_lists (user_id, title, category) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $list_title, $list_category]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_list'])) {
    $list_id = sanitize($_POST['list_id']);
    $stmt = $pdo->prepare("DELETE FROM todo_lists WHERE id = ? AND user_id = ?");
    $stmt->execute([$list_id, $user_id]);
}

// Fetch todo lists
$stmt = $pdo->prepare("SELECT * FROM todo_lists WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$todo_lists = $stmt->fetchAll();

// Search tasks
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Todo List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Haol, <?php echo $_SESSION['username']; ?>!</h1>
        <a href="profile.php" class="btn btn-info mb-3">View Profile</a>
        <a href="logout.php" class="btn btn-danger mb-3">Logout</a>

        <h2>To-Do List</h2>

        <form method="POST" action="" class="mb-3">
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="list_title" placeholder="Enter list title" required>
            </div>
            <div class="input-group mb-2">
                <select class="form-control" name="list_category" required>
                    <option value="">Select Category</option>
                    <option value="Work">Work</option>
                    <option value="Personal">Personal</option>
                    <option value="Groceries">Groceries</option>
                </select>
            </div>
            <div class="input-group-append">
                <button type="submit" name="new_list" class="btn btn-primary">Create New List</button>
            </div>
        </form>

        <div class="row">
            <?php foreach ($todo_lists as $list): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $list['title']; ?></h5>
                            <p class="card-text">Category: <?php echo $list['category']; ?></p>
                            <a href="tasks.php?list_id=<?php echo $list['id']; ?>" class="btn btn-primary">View Tasks</a>
                            <form method="POST" action="" class="d-inline">
                                <button type="button" class="btn btn-danger" onclick="showDeleteModal(<?php echo $list['id']; ?>)">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>Search and Filter Tasks</h2>
        <form method="GET" action="" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search tasks" value="<?php echo $search_query; ?>">
                <select name="filter" class="custom-select">
                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Tasks</option>
                    <option value="completed" <?php echo $filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="incomplete" <?php echo $filter === 'incomplete' ? 'selected' : ''; ?>>Incomplete</option>
                </select>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>List</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?php echo $task['description']; ?></td>
                        <td><?php echo $task['list_title']; ?></td>
                        <td><?php echo $task['is_completed'] ? 'Completed' : 'Incomplete'; ?></td>
                        <td>
                            <a href="tasks.php?list_id=<?php echo $task['list_id']; ?>" class="btn btn-sm btn-info">View List</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal -->
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
                        Are you sure you want to delete this To-Do list?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <form id="deleteForm" method="POST" action="">
                            <input type="hidden" name="list_id" id="modalListId">
                            <button type="submit" name="delete_list" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function showDeleteModal(listId) {
            document.getElementById('modalListId').value = listId;
            $('#deleteModal').modal('show'); // Menampilkan modal menggunakan Bootstrap
        }
    </script>
</body>
</html>
