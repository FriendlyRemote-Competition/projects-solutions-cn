<?php

session_start();

$file = __DIR__ . '/users.json';

$message = '';
$error = [];
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $error = [];
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
            $authenticated = false;
            foreach ($users as $user) {
                if ($user['username'] === $username && password_verify($password, $user['password'])) {
                    $authenticated = true;
                    $_SESSION['username'] = $username;
                    break;
                }
            }

            if ($authenticated) {
                $message = 'Login successful. Click the link to the home page.';
                $type = 'success';
            } else {
                $message = 'Invalid username or password.';
                $type = 'error';
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

    <title>Login</title>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>

<div class="card">

    <h1>Login</h1>

    <?php if ($message): ?>
        <div class="message <?= $type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($type === 'success'): ?>
        <a href="./index.php">Home</a>
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
            <?php echo $error['username'] ?? '' ?>
        </div>

        <label for="password">Password</label>

        <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter password"
        >
        <div class="error">
            <?php echo $error['password'] ?? '' ?>
        </div>

        <button type="submit">
            Login
        </button>

    </form>

    <div class="link">
        New here?
        <a href="./register.php">Register</a>
    </div>

</div>

</body>
</html>