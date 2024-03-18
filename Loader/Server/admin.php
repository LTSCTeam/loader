<?php
session_start();

// Authorization data
const ADMIN_LOGIN = 'admin';
const ADMIN_PASSWORD = 'admin';

// Connecting to the database
$host = 'localhost';
$database = 'test';
$user = 'root';
$password = '';

// Checking cookies
if (isset($_COOKIE['admin_auth']) && $_COOKIE['admin_auth'] === hash('sha256', ADMIN_LOGIN.ADMIN_PASSWORD)) {
    $_SESSION['logged_in'] = true;
}

// Processing a Login Request
if (isset($_POST['login']) && isset($_POST['password'])) {
    if ($_POST['login'] === ADMIN_LOGIN && $_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['logged_in'] = true;
        setcookie('admin_auth', hash('sha256', ADMIN_LOGIN.ADMIN_PASSWORD), time() + 3600); // Cookies at 1 hour
    } else {
        echo "Wrong login or password!";
    }
}

// Checking authentication
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    // Displaying the login form
    echo '<form action="admin.php" method="post">
            Login: <input type="text" name="login" /><br />
            Password: <input type="password" name="password" /><br />
            <input type="submit" value="Log in" />
          </form>';
    exit;
}

// Handling HWID Addition
if (isset($_POST['add_hwid']) && isset($_POST['hwid_to_add'])) {
    $connection = new mysqli($host, $user, $password, $database);
    $hwidToAdd = $connection->real_escape_string($_POST['hwid_to_add']);
    $connection->query("INSERT INTO `hwid_table` (`hwid`) VALUES ('{$hwidToAdd}')");
    echo "HWID added!";
    $connection->close();
}

// Displaying the admin panel
echo '<form action="admin.php" method="post">
        HWID to add: <input type="text" name="hwid_to_add" /><br />
        <input type="submit" name="add_hwid" value="Add" />
      </form>';
?>
