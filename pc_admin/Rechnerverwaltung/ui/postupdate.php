<!DOCTYPE html>
<?php
    $page = "postupdate";
    include "../api/accessConfig.php";
    include "../api/versioncode.php";
    if (loadConfig('config', null) != 'done') {
        header('Location: ../setup/index.php');
    } else {
        include "../api/dbconnect.php";
        include "menue.php";
    }
    $error = false;
    $descriptions = '';
    //Add new actions here:
    if (loadConfig('versioncode', null) < 2 || loadConfig('versioncode', null) == null) {
        $descriptions .= '<li>Anlegen der Datenbank-Tabelle für Gruppenlaufwerke</li>';
        if ($_GET['action'] == 'doupdate') {
            $tableExistsRequest = "DESCRIBE groupfolders";
            if (mysqli_query($database, $tableExistsRequest)) {
                changeConfigValue('versioncode', false, $versioncode);
                header('Location: nologin.php');
            } else {
                $groupfoldersTableRequest = "CREATE TABLE IF NOT EXISTS groupfolders (id int(11) NOT NULL, name text COLLATE utf8_bin NOT NULL, students int(11) NOT NULL, teachers int(11) NOT NULL, roomexchange int(11) NOT NULL, writeable int(11) NOT NULL, path text COLLATE utf8_bin NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
                $groupfoldersTableQuery = mysqli_query($database, $groupfoldersTableRequest);
                $alterRequest1 = "ALTER TABLE groupfolders ADD PRIMARY KEY (id);";
                $alterRequest2 = "ALTER TABLE groupfolders MODIFY id int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;";
                $alterQuery1 = mysqli_query($database, $alterRequest1);
                $alterQuery2 = mysqli_query($database, $alterRequest2);
                if ($groupfoldersTableQuery && $alterQuery1 && $alterQuery2) {
                    changeConfigValue('versioncode', false, $versioncode);
                    header('Location: nologin.php');
                } else {
                    $error = true;
                }
            }
        }
    }
?>
<html lang="de">
<head>
    <title>Aktualisierung abschließen - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>UPDATE</b>ABSCHLIEßEN</p>
        <p>Folgende Aktionen müssen ausgeführt werden:</p>
        <ul>
        <?php
            echo $descriptions;
        ?>
        </ul>
        <button onclick="update()">Update ausführen</button>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        function update() {
            window.location.href = 'postupdate.php?action=doupdate';
        }
        <?php
            if ($error) {
        ?>
        swal({
            title: 'Es ist ein Fehler aufgetreten',
            text: 'Bitte erneut versuchen.',
            type: 'error',
        })
        <?php
            }
        ?>
    </script>
</body>
</html>
