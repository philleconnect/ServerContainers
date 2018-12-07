<!DOCTYPE html>
<?php
    session_start();
    $page = "nologin";
    include "../api/dbconnect.php";
    include "menue.php";
?>
<html lang="de">
<head>
    <title>Nicht berechtigt - PhilleConnect Admin</title>
    <?php include "includes.php"; ?>
</head>
<body>
    <?php include "assets/preloader.php"; ?>
    <div role="navigation" id="foo" class="nav-collapse">
        <div class="top">
            <img src="ressources/img/logo.png">
            <li><b>PHILLE</b>CONNECT</li>
        </div>
        <ul>
            <?php
                echo $menu;
            ?>
        </ul>
    </div>
    <div role="main" class="main">
        <a href="#nav" class="nav-toggle">Menu</a>
        <noscript>
            <p>Dein Browser unterstützt kein JavaScript oder JavaScript ist ausgeschaltet. Du musst JavaScript aktivieren, um diese Seite zu verwenden!</p>
        </noscript>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        function showLoginModal() {
            swal({
                title: 'Sie haben keine Berechtigung für diesen Bereich.',
                showCancelButton: false,
                confirmButtonText: 'OK',
                closeOnConfirm: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                type: 'error'
            }).then(function() {
                <?php if ($_SESSION['type'] == '3') { ?>
                window.location.href = 'machines.php';
                <?php } else { ?>
                window.location.href = 'index.php';
                <?php } ?>
            });
        }
        showLoginModal();
    </script>
</body>
</html>
