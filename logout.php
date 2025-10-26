<?php
require_once 'includes/config.php';

if (is_logged_in()) {
    log_activity('logout', 'User logged out');
    
    session_unset();
    session_destroy();
}

redirect('login.php');
?>
