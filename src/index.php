<?php
include 'functions.php';

$emailMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task-name'])) {
        addTask($_POST['task-name']);
        header("Location: index.php");
        exit;
    }

    if (isset($_POST['task-id'])) {
        $is_completed = isset($_POST['complete']) ? 1 : 0;
        markTaskAsCompleted($_POST['task-id'], $is_completed);
        header("Location: index.php");
        exit;
    }

    if (isset($_POST['delete-id'])) {
        deleteTask($_POST['delete-id']);
        header("Location: index.php");
        exit;
    }

    if (isset($_POST['email'])) {
        subscribeEmail($_POST['email']);
        $emailMessage = "<p>Verification email sent. Please check your inbox.</p>";
        // No redirect here, so the message is shown
    }
}

$tasks = getAllTasks();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Scheduler</title>
</head>
<body>
    <h1>Task Planner</h1>

    <!-- Form to add a task -->
    <form method="POST">
        <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
        <button type="submit" id="add-task">Add Task</button>
    </form>

    <h2>Subscribe for Task Reminders</h2>
    <form method="POST">
        <input type="email" name="email" required />
        <button id="submit-email">Submit</button>
    </form>

    <?php if (!empty($emailMessage)) echo $emailMessage; ?>

    <!-- Display list of tasks -->
    <ul class="task-list">
        <?php foreach ($tasks as $task): ?>
            <li class="task-item <?php echo $task['completed'] ? 'completed' : ''; ?>">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="task-id" value="<?= $task['id'] ?>">
                    <input type="checkbox" class="task-status" name="complete" onchange="this.form.submit()" <?= $task['completed'] ? 'checked' : '' ?>>
                </form>
                <?= htmlspecialchars($task['name']) ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete-id" value="<?= $task['id'] ?>">
                    <button class="delete-task">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
