<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'database.php';
require_once 'models/UserModel.php';

$connection = connection();
$action     = $_GET['action'] ?? '';


if ($action == 'login')
{
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token']))
    {
        $result = getUserByToken($connection, $_COOKIE['remember_token']);
        $user   = $result->fetch_assoc();

        if ($user)
        {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];
            header('Location: index.php?action=home');
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';

        $result = getUserByEmail($connection, $email);
        $user   = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password_hash']))
        {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            if (isset($_POST['remember_me']))
            {
                $token = bin2hex(random_bytes(32));
                saveRememberToken($connection, $user['id'], $token);
                setcookie('remember_token', $token, time() + (86400 * 30), '/');
            }

            header('Location: index.php?action=home');
            exit;
        }
        else
        {
            $error = "Invalid email or password";
        }
    }

    include 'views/login.php';
}


else if ($action == 'register')
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $name        = $_POST['name']        ?? '';
        $email       = $_POST['email']       ?? '';
        $password    = $_POST['password']    ?? '';
        $phone       = $_POST['phone']       ?? '';
        $nationality = $_POST['nationality'] ?? '';

        $error = '';
        if (empty($name) || empty($email) || empty($password))
        {
            $error = "All fields are required";
        }
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $error = "Invalid email format";
        }
        else if (strlen($password) < 6)
        {
            $error = "Password must be at least 6 characters";
        }
        else
        {
            $check = getUserByEmail($connection, $email);
            if ($check->fetch_assoc())
            {
                $error = "Email already registered";
            }
        }

        if (empty($error))
        {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $result       = registerUser($connection, $name, $email, $passwordHash, $phone, $nationality);

            if ($result)
            {
                header('Location: index.php?action=login');
                exit;
            }
            else
            {
                $error = "Registration failed. Please try again.";
            }
        }
    }

    include 'views/register.php';
}


else if ($action == 'logout')
{
    if (isset($_COOKIE['remember_token']))
    {
        clearRememberToken($connection, $_SESSION['user_id']);
        setcookie('remember_token', '', time() - 3600, '/');
    }

    session_unset();
    session_destroy();
    header('Location: index.php?action=login');
    exit;
}