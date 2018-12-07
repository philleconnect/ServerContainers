<!DOCTYPE html>
<html>
<head>
    <title>Setup - PhilleConnect Admin</title>
    <?php include "includes.php"; ?>
</head>
<body>
    <div role="navigation" id="foo" class="nav-collapse">
        <div class="top">
            <img src="../ui/ressources/img/logo.png">
            <li><b>PHILLE</b>CONNECT</li>
        </div>
        <ul>
            <li class="active">
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
            <li>
                <a href="#">Fertigstellen</a>
            </li>
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
        <?php
            if (is_writeable('../config/config.txt')) {
                ?>
                var hasWritePermission = true;
                <?php
            } else {
                ?>
                var hasWritePermission = false;
                <?php
            }
        ?>
        if (hasWritePermission) {
            swal({
                title: 'Willkommen bei PhilleConnect!',
                html: 'Der Server ist eingerichtet und bereit für die Konfiguration. Fangen wir an!<br /><b>Hinweis:</b> Bei Verwendung der offiziellen Docker-Container werden die Schritte "Datenbank" und "LDAP-Server" automatisch durchgeführt.',
                confirmButtonText: '>> Weiter',
                type: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then(function() {
                window.location.href = 'database.php';
            });
        } else {
            swal({
                title: 'Willkommen bei PhilleConnect!',
                text: 'PHP hat keine Schreibrechte im Ordner \'config\'. Bitte stelle sicher, dass PHP in diesem Ordner schreiben kann und lade diese Seite erneut!',
                type: 'warning',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
            });
        }
    </script>
</body>
</html>
