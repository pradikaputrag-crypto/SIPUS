<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

require_once '../config.php/config.php';

$identifier = '';
if (isset($_POST['identifier'])) {
    $identifier = trim($_POST['identifier']);
} elseif (isset($_POST['email'])) {
    $identifier = trim($_POST['email']);
}
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($identifier === '' || $password === '') {
    header('Location: login.php?error=' . urlencode('Masukkan username/email dan kata sandi.') . '&identifier=' . urlencode($identifier));
    exit;
}

$columns = [];
$resultColumns = mysqli_query($koneksi, 'SHOW COLUMNS FROM users');
if ($resultColumns) {
    while ($column = mysqli_fetch_assoc($resultColumns)) {
        $columns[] = $column['Field'];
    }
    mysqli_free_result($resultColumns);
}

$useUsername = in_array('username', $columns, true);
$useEmail = in_array('email', $columns, true);
$useId = in_array('id', $columns, true)
    ? 'id'
    : (in_array('user_id', $columns, true)
        ? 'user_id'
        : (in_array('id_user', $columns, true) ? 'id_user' : null));

if (!$useUsername && !$useEmail) {
    header('Location: login.php?error=' . urlencode('Kolom login tidak tersedia pada sistem.'));
    exit;
}

$query = '';
if ($useUsername && $useEmail) {
    $query = 'SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1';
} elseif ($useUsername) {
    $query = 'SELECT * FROM users WHERE username = ? LIMIT 1';
} else {
    $query = 'SELECT * FROM users WHERE email = ? LIMIT 1';
}

$stmt = mysqli_prepare($koneksi, $query);
if (!$stmt) {
    header('Location: login.php?error=' . urlencode('Terjadi kesalahan server.'));
    exit;
}

if ($useUsername && $useEmail) {
    mysqli_stmt_bind_param($stmt, 'ss', $identifier, $identifier);
} else {
    mysqli_stmt_bind_param($stmt, 's', $identifier);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

if (!$user) {
    header('Location: login.php?error=' . urlencode('Username/email atau kata sandi tidak cocok.') . '&identifier=' . urlencode($identifier));
    exit;
}

$storedPassword = isset($user['password']) ? $user['password'] : '';
$isValidPassword = false;

if ($storedPassword !== '' && password_verify($password, $storedPassword)) {
    $isValidPassword = true;
} elseif (hash_equals($storedPassword, $password)) {
    $isValidPassword = true;
}

if (!$isValidPassword) {
    header('Location: login.php?error=' . urlencode('Username/email atau kata sandi tidak cocok.') . '&identifier=' . urlencode($identifier));
    exit;
}

session_regenerate_id(true);
$_SESSION['id_user'] = $useId && isset($user[$useId]) ? $user[$useId] : null;
$_SESSION['user_id'] = $_SESSION['id_user'];
$_SESSION['username'] = isset($user['username']) ? $user['username'] : (isset($user['email']) ? $user['email'] : '');
$_SESSION['nama'] = isset($user['nama']) ? $user['nama'] : ($_SESSION['username'] ?: 'Pengguna');
$_SESSION['role'] = isset($user['role']) ? strtolower($user['role']) : (isset($user['level']) ? strtolower($user['level']) : 'user');

if ($_SESSION['role'] === 'admin') {
    header('Location: ../admin/dashboard.php');
    exit;
}

if ($_SESSION['role'] === 'user') {
    header('Location: ../user/dashboard.php');
    exit;
}

session_unset();
session_destroy();
header('Location: login.php?error=' . urlencode('Peran pengguna tidak valid. Hubungi administrator.'));
exit;
