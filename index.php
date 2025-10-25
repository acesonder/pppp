<?php
// Simple redirect to index.html for landing page
// Or redirect to dashboard if logged in
session_start();

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
} else {
    header('Location: index.html');
    exit();
}
?>
