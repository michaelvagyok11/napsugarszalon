<?php
// auth.php
require_once __DIR__ . '/db.php';

session_start();

function isAdminLoggedIn(): bool {
    return !empty($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function adminLogin($username, $password): bool {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        return true;
    }
    return false;
}

function adminLogout() {
    session_unset();
    session_destroy();
}

function getAdminInfo() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT id, username, email FROM admin_users WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['admin_id']]);
    return $stmt->fetch();
}
