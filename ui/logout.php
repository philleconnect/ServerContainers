<?php
    session_start();
    $_SESSION['user'] = '';
    $_SESSION['timeout'] = 0;
    header('Location: nologin.php');
?>