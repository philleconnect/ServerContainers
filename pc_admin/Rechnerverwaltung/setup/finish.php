<?php
    $config = unserialize(file_get_contents('../config/config.txt'));
    if ($config['config'] == 'done') {
        header('Location: ../ui/nologin.php');
    }
    if ($_POST['isReady'] == 'true') {
        include "../api/accessConfig.php";
        changeConfigValue('config', null, 'done');
        header('Location: ../ui/nologin.php');
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup - Fertigstellen - PhilleConnect Admin</title>
    <?php include "includes.php"; ?>
</head>
<body>
    <div role="navigation" id="foo" class="nav-collapse">
        <div class="top">
            <img src="../ui/ressources/img/logo.png">
            <li><b>PHILLE</b>CONNECT</li>
        </div>
        <ul>
            <li>
                <a href="#">Willkommen</a>
            </li>
            <li>
                <a href="#">Datenbank</a>
            </li>
            <li>
                <a href="#">LDAP-Server</a>
            </li>
            <li>
                <a href="#">IPFire</a>
            </li>
            <li>
                <a href="#">Administratorkonto</a>
            </li>
            <li class="active">
                <a href="#">Fertigstellen</a>
            </li>
        </ul>
    </div>
    <div role="main" class="main">
        <a href="#nav" class="nav-toggle">Menu</a>
        <noscript>
            <p>Dein Browser unterstützt kein JavaScript oder JavaScript ist ausgeschaltet. Du musst JavaScript aktivieren, um diese Seite zu verwenden!</p>
        </noscript>
        <p style="font-family: Arial, sans-serif; font-size: 45px;"><b>FERTIG</b>STELLEN</p>
        <p>PhilleConnect wurde erfolgreich konfiguriert. Mit 'Fertigstellen' wird die Konfiguration abgeschlossen.</p>
        <form action="finish.php" method="post">
            <input type="submit" value="Fertigstellen"/>
            <input type="hidden" value="true" name="isReady"/>
        </form>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
    </script>
</body>
</html>
