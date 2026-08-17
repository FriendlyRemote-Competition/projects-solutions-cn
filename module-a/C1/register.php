<?php

session_start();

$file = __DIR__ . '/users.json';

$message = '';
$error = [];
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $error=[];
    if (!$username) {
        $error['username'] = 'Username is required';
    }
    if (!$password) {
        $error['password'] = 'Password is required';
    }

    if (empty($error)) {
        if ($username === '' || $password === '') {
            $message = 'Username and password are required.';
            $type = 'error';
        } else {
            $users = json_decode(file_get_contents($file), true);
            $exists = false;
            foreach ($users as $user) {
                if ($user['username'] === $username) {
                    $exists = true;
                    break;
                }
            }

            if ($exists) {
                $message = 'Username already exists.';
                $type = 'danger';
            } else {
                $users[] = [
                        'username' => $username,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                ];
                file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
                $message = 'Registration successful.';
                $type = 'success';
            }
        }
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register</title>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>

<div class="card">

    <h1>Register</h1>

    <?php if ($message): ?>
        <div class="message <?= $type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label for="username">Username</label>

        <input
                type="text"
                name="username"
                id="username"
                placeholder="Enter username"
        >
        <div class="error">
            <?php echo $error['username']??'' ?>
        </div>

        <label for="password">Password</label>

        <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter password"
        >
        <div class="error">
            <?php echo $error['password']??'' ?>
        </div>

        <button type="submit">
            Register
        </button>

    </form>

    <div class="link">
        Already have an account?
        <a href="./login.php">Login</a>
    </div>

</div>

</body>
</html>