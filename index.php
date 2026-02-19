<?php
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';

if (is_logged_in()) {
    redirect('views/dashboard/' . $_SESSION['role'] . '.php');
} else {
    redirect('login.php');
}
?>