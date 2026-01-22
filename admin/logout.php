<?php
require_once __DIR__ . '/_init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('/admin/login.php');
}

csrf_validate();

unset($_SESSION['is_admin'], $_SESSION['user_role']);

if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id'], $_SESSION['user_name']);
}

session_regenerate_id(true);
redirect_to('/admin/login.php');

