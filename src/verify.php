<?php
require_once 'functions.php';

$email = $_GET['email'] ?? '';
$code = $_GET['code'] ?? '';
$verified = false;

if ($email && $code) {
    $verified = verifySubscription($email, $code);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
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
            color: <?= $verified ? '#2ecc71' : '#e74c3c' ?>;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($email && $code): ?>
            <?php if ($verified): ?>
                <h2>Email Verified Successfully!</h2>
                <p>You will now receive task reminders.</p>
            <?php else: ?>
                <h2>Verification Failed!</h2>
                <p>Invalid or expired verification code.</p>
            <?php endif; ?>
        <?php else: ?>
            <h2>Invalid Request</h2>
            <p>Missing email or verification code in the URL.</p>
        <?php endif; ?>
    </div>
</body>
</html>
