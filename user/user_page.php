<?php

session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit();
}
if ($_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="box">
        <h1> Welcome, <span><?= htmlspecialchars($_SESSION['name']); ?></span></h1>
        <p>This is an <span>User</span> page</p>
        <button onclick="window.location.href='../auth/logout.php' ">Logout</button>
    </div>
    <script src="../script.js"></script>
</body>
</html>