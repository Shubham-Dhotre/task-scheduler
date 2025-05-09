<?php
require_once 'functions.php';

$email = $_GET['email'] ?? '';
$status = null;

if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $status = unsubscribeEmail($email) ? 'success' : 'not_found';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
            background-color: #f2f2f2;
            text-align: center;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px #aaa;
            display: inline-block;
        }
        h2 {
            color: <?= $status === 'success' ? '#2ecc71' : '#e74c3c' ?>;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$email): ?>
            <h2>Invalid Request</h2>
            <p>Email address missing from the link.</p>
        <?php elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)): ?>
            <h2>Invalid Email</h2>
            <p>The email address format is incorrect.</p>
        <?php elseif ($status === 'success'): ?>
            <h2>Unsubscribed Successfully</h2>
            <p>You will no longer receive task reminders.</p>
        <?php elseif ($status === 'not_found'): ?>
            <h2>Unsubscribe Failed</h2>
            <p>The email was not found in the subscriber list.</p>
        <?php endif; ?>
    </div>
</body>
</html>
