<?php
require_once 'functions.php';

try {
    sendTaskReminders();
    echo "Task reminders sent successfully.";
} catch (Exception $e) {
    echo "Failed to send task reminders: " . htmlspecialchars($e->getMessage());
}
?>
