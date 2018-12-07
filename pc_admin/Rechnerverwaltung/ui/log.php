<!DOCTYPE html>
<?php
    $page = 'Aktivitätsprotokoll';
    include "../api/dbconnect.php";
    session_start();
    if ($_SESSION['user'] == null || $_SESSION['user'] == '' || ($_SESSION['timeout'] + 1200) < time()) {
        header("Location: nologin.php");
    } else {
        $_SESSION['timeout'] = time();
        include "menue.php";
    }
?>
<html lang="de">
<head>
    <title>Aktivitätsprotokoll - PhilleConnect Admin</title>
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
        <?php include "assets/timeout.php"; ?>
    </div>
    <div role="main" class="main">
        <a href="#nav" class="nav-toggle">Menu</a>
        <noscript>
            <p>Dein Browser unterstützt kein JavaScript oder JavaScript ist ausgeschaltet. Du musst JavaScript aktivieren, um diese Seite zu verwenden!</p>
        </noscript>
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>AKTIVITÄTS</b>PROTOKOLL</p>
        <p>Hier werden alle an einem Clienten durchgeführten Aktionen aufgelstet. Mit den Feldern im Tabllenkopf kann die Tabelle sortiert bzw. in ihr gesucht werden.</p>
        <br />
        <p>Aktivitäten:</p>
        <div class="datagrid" style="overflow: auto;">
            <table id="machines">
                <thead>
                    <tr>
                        <th>Rechner</th>
                        <th>Nutzer</th>
                        <th>Zeitpunkt</th>
                        <th>Ausgeführte Aktion</th>
                        <th>Ziel-User (nur PW-Reset)</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $request = "SELECT * FROM log";
                        $query = mysqli_query($database, $request);
                        $i = 0;
                        while ($result = mysqli_fetch_assoc($query)) {
                            if ($i == 0) {
                                echo '<tr>';
                                $i = 1;
                            } else {
                                echo '<tr class="alt">';
                                $i = 0;
                            }
                            switch($result['action']) {
                                case 100:
                                    $action = 'Am PC angemeldet';
                                    break;
                                case 101:
                                    $action = 'Ungültiger Nutzername';
                                    break;
                                case 102:
                                    $action = 'Falsches Passwort';
                                    break;
                                case 103:
                                    $action = 'Schüleraccount an Lehrer-PC';
                                    break;
                                case 200:
                                    $action = 'Passwort geändert';
                                    break;
                                case 201:
                                    $action = 'Passwort zurückgesetzt';
                                    break;
                            }
                            echo '<td>'.$result["machine"].'</td><td>'.$result["user"].'</td><td>'.date('d.m.Y, G:i:s', $result['timestamp']).'</td><td>'.$action.'</td><td>'.$result["target"].'</td><td><a href="#" onclick="deleteLog('.$result["id"].')">Löschen</a></td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <button onclick="clearLog()">Gesamten Log löschen</button>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        var table_Props = {
            col_3: "select",
            col_5: "none",
            display_all_text: "Alle anzeigen",
            sort_select: true
        };
        var tf2 = setFilterGrid("machines", table_Props);
        function changeMachine(id) {
            window.location.href = "changemachine.php?id="+id;
        }
        function deleteLog(id) {
            swal({
                title: 'Eintrag löschen?',
                text: 'Der gewählte Eintrag wird für immer verschwunden sein (eine lange Zeit)!',
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Löschen',
                cancelButtonText: 'Abbrechen',
                confirmButtonColor: '#D33',
                cancelButtonColor: "#3085d6",
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        request = getAjaxRequest();
                        var url = "../api/api.php";
                        var params = 'request=' + encodeURIComponent(JSON.stringify({
                            logging: {
                                mode: 'remove',
                                id: id,
                            }
                        }));
                        request.onreadystatechange=stateChangedSave;
                        request.open("POST",url,true);
                        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        request.send(params);
                        function stateChangedSave() {
                            if (request.readyState == 4) {
                                var response = JSON.parse(request.responseText);
                                if (response.logging == "SUCCESS") {
                                    swal({
                                        title: "Eintrag erfolgreich gelöscht!",
                                        type: "success",
                                    }).then(function() {
                                        window.location.href = 'log.php';
                                    })
                                } else {
                                    swal({
                                        title: "Es ist ein Fehler aufgetreten.",
                                        text: "Bitte erneut versuchen.",
                                        type: "error",
                                    })
                                }
                            }
                        }
                    })
                },
            });
        }
        function clearLog() {
            swal({
                title: 'Protokoll löschen?',
                text: 'Das Protokoll wird für immer verschwunden sein (eine lange Zeit)!',
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Löschen',
                cancelButtonText: 'Abbrechen',
                confirmButtonColor: '#D33',
                cancelButtonColor: "#3085d6",
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        request = getAjaxRequest();
                        var url = "../api/api.php";
                        var params = 'request=' + encodeURIComponent(JSON.stringify({
                            logging: {
                                mode: 'clear',
                            }
                        }));
                        request.onreadystatechange=stateChangedSave;
                        request.open("POST",url,true);
                        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        request.send(params);
                        function stateChangedSave() {
                            if (request.readyState == 4) {
                                var response = JSON.parse(request.responseText);
                                if (response.logging == "SUCCESS") {
                                    swal({
                                        title: "Protokoll erfolgreich gelöscht!",
                                        type: "success",
                                    }).then(function() {
                                        window.location.href = 'log.php';
                                    })
                                } else {
                                    swal({
                                        title: "Es ist ein Fehler aufgetreten.",
                                        text: "Bitte erneut versuchen.",
                                        type: "error",
                                    })
                                }
                            }
                        }
                    })
                },
            });
        }
    </script>
</body>
</html>
