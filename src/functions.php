<?php

function getAllTasks() {
    $tasksFile = __DIR__ . '/tasks.txt';
    if (!file_exists($tasksFile)) return [];
    $lines = file($tasksFile, FILE_IGNORE_NEW_LINES);

    $tasks = [];

    foreach ($lines as $line) {
        $parts = explode('|', $line);
        if (count($parts) === 3) {
            $tasks[] = [
                'id' => $parts[0],
                'name' => $parts[1],
                'completed' => $parts[2] === '1'
            ];
        }
    }

    return $tasks;
}


function addTask($task_name) {
    $task_name = trim($task_name);
    if ($task_name === '') return;

    $existing_tasks = getAllTasks();

    // Check for duplicate task name
    foreach ($existing_tasks as $task) {
        if (strcasecmp($task['name'], $task_name) === 0) {
            return; 
        }
    }

    $id = uniqid(); // unique task ID
    $line = $id . "|" . $task_name . "|0" . PHP_EOL;
    $tasksFile = __DIR__ . '/tasks.txt';
    file_put_contents($tasksFile, $line, FILE_APPEND);
    }

function markTaskAsCompleted($task_id, $is_completed) {
    $tasks = getAllTasks();
    $new_content = '';

    foreach ($tasks as $task) {
        if ($task['id'] === $task_id) {
            $task['completed'] = $is_completed;
        }
        $new_content .= $task['id'] . '|' . $task['name'] . '|' . ($task['completed'] ? '1' : '0') . PHP_EOL;
    }

    $tasksFile = __DIR__ . '/tasks.txt';
    file_put_contents($tasksFile, $new_content);
    }

function deleteTask($task_id) {
    $tasks = getAllTasks();
    $new_content = '';

    foreach ($tasks as $task) {
        if ($task['id'] !== $task_id) {
            $new_content .= $task['id'] . '|' . $task['name'] . '|' . ($task['completed'] ? '1' : '0') . PHP_EOL;
        }
    }

    $tasksFile = __DIR__ . '/tasks.txt';
    file_put_contents($tasksFile, $new_content);
    }

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}



function subscribeEmail($email) {
    $subscribersFile = __DIR__ . '/subscribers.txt';
    $pendingFile = __DIR__ . '/pending_subscriptions.txt';

    // Load current subscribers
    $subscribers = file_exists($subscribersFile) ? file($subscribersFile, FILE_IGNORE_NEW_LINES) : [];
    if (in_array($email, $subscribers)) return;

    // Load pending verifications
    $pending = file_exists($pendingFile) ? file($pendingFile, FILE_IGNORE_NEW_LINES) : [];
    foreach ($pending as $entry) {
        list($pending_email, ) = explode('|', $entry);
        if ($pending_email == $email) return;
    }

    // Generate code and store
    $code = generateVerificationCode();
    file_put_contents($pendingFile, "$email|$code\n", FILE_APPEND);

    // Build verification link
    $verification_link = "http://localhost:8000/src/verify.php?email=" . urlencode($email) . "&code=$code";

    // Send email
    $subject = "Verify subscription to Task Planner";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";
    $message = "<p>Click the link below to verify your subscription to Task Planner:</p>";
    $message .= "<p><a id='verification-link' href='$verification_link'>Verify Subscription</a></p>";

    mail($email, $subject, $message, $headers);
}


function verifySubscription($email, $code) {
    $pendingFile = __DIR__ . '/pending_subscriptions.txt';
    $subscribersFile = __DIR__ . '/subscribers.txt';

    if (!file_exists($pendingFile)) return false;

    $pending = file($pendingFile, FILE_IGNORE_NEW_LINES);
    $found = false;
    $newPending = [];

    foreach ($pending as $line) {
        list($pending_email, $pending_code) = explode('|', $line);
        if ($pending_email === $email && $pending_code === $code) {
            $found = true;
        } else {
            $newPending[] = $line;
        }
    }

    if ($found) {
        file_put_contents($pendingFile, implode("\n", $newPending));
        file_put_contents($subscribersFile, $email . "\n", FILE_APPEND);
        return true;
    }

    return false;
}


function unsubscribeEmail($email) {
    $subscribersFile = __DIR__ . '/subscribers.txt';

    if (!file_exists($subscribersFile)) return false;

    $subscribers = file($subscribersFile, FILE_IGNORE_NEW_LINES);
    $newSubscribers = array_filter($subscribers, fn($line) => trim($line) !== trim($email));

    if (count($subscribers) === count($newSubscribers)) return false; // email not found

    file_put_contents($subscribersFile, implode("\n", $newSubscribers) );
    return true;
}



function sendTaskReminders() {
    $subscribersFile = __DIR__ . '/subscribers.txt';
    $tasks = getAllTasks();

    // Get only incomplete tasks
    $pendingTasks = array_filter($tasks, fn($task) => !$task['completed']);

    if (empty($pendingTasks)) return;

    $subscribers = file($subscribersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($subscribers as $email) {
        sendTaskEmail($email, $pendingTasks);
    }
}


function sendTaskEmail($email, $pending_tasks) {
    $subject = 'Task Planner - Pending Tasks Reminder';

    $taskListHTML = "<ul>";
    foreach ($pending_tasks as $task) {
        $taskListHTML .= "<li>" . htmlspecialchars($task['name']) . "</li>";
    }
    $taskListHTML .= "</ul>";

    $unsubscribeLink = "http://localhost:8000/src/unsubscribe.php?email=" . urlencode($email);

    $message = "
        <html>
        <head><title>$subject</title></head>
        <body>
            <h2>Pending Tasks Reminder</h2>
            <p>Here are the current pending tasks:</p>
            $taskListHTML
            <p><a id='unsubscribe-link' href='$unsubscribeLink'>Unsubscribe from notifications</a></p>
        </body>
        </html>
    ";

    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@example.com" . "\r\n";

    mail($email, $subject, $message, $headers);
}



?>

<?php




