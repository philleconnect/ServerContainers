<!DOCTYPE html>
<?php
    $page = 'Rechnerverwaltung';
    include "../api/dbconnect.php";
    session_start();
    if ($_SESSION['user'] == null || $_SESSION['user'] == '' || ($_SESSION['timeout'] + 1200) < time()) {
        header("Location: nologin.php");
    } elseif ($_SESSION['type'] != '1' && $_SESSION['type'] != '3') {
        header("Location: restricted.php");
    } else {
        $_SESSION['timeout'] = time();
        include "menue.php";
    }
?>
<html lang="de">
<head>
    <title>Rechnerverwaltung - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>RECHNER</b>VERWALTUNG</p>
        <p>Hier wird jeder PhilleConnect-Clientrechner aufgelistet. Jedem Rechner kann ein Raum und für jedes Betriebssystem ein Konfigurationsprofil zugewiesen werden, außerdem kann jeder Rechner einem individuellen Platznamen erhalten. <b>Hinweis:</b> Die Aufnahme neuer Rechner erfolgt einmalig mit dem Clientprogramm "Client Registration Tool".</p>
        <br />
        <p>Rechner:</p>
        <div class="datagrid" style="overflow: auto;">
            <table id="machines">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Raum</th>
                        <th>Konfigurationsprofil Windows</th>
                        <th>Konfigurationsprofil Linux</th>
                        <th>Hardware-ID</th>
                        <th>IP-Adresse</th>
                        <th>Lehrer-PC</th>
                        <th>Internet bei Boot</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $request = "SELECT * FROM machines";
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
                            if ($result['inet'] == '1') {
                                $inet = '<input type="checkbox" checked id="inet_'.$result['id'].'" onchange="saveCheckbox(\'inet\', \''.$result['id'].'\')"/><p style="display: none;">Ja</p>';
                            } else {
                                $inet = '<input type="checkbox" id="inet_'.$result['id'].'" onchange="saveCheckbox(\'inet\', \''.$result['id'].'\')"/><p style="display: none;">Nein</p>';
                            }
                            if ($result['teacher'] == '1') {
                                $teacher = '<input type="checkbox" checked id="teacher_'.$result['id'].'" onchange="saveCheckbox(\'teacherpc\', \''.$result['id'].'\')"/><p style="display: none;">Ja</p>';
                            } else {
                                $teacher = '<input type="checkbox" id="teacher_'.$result['id'].'" onchange="saveCheckbox(\'teacherpc\', \''.$result['id'].'\')"/><p style="display: none;">Nein</p>';
                            }
                            echo '<td>'.$result["machine"].'</td><td>'.$result["room"].'</td><td>'.$result["config_win"].'</td><td>'.$result["config_linux"].'</td><td>'.$result["hardwareid"].'</td><td>'.$result["ip"].'</td><td>'.$teacher.'</td><td>'.$inet.'</td><td><a href="#" onclick="changeMachine('.$result["id"].')">Bearbeiten</a></td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        var table_Props = {
            col_1: "select",
            col_2: "select",
            col_3: "select",
            col_6: "select",
            col_7: "select",
            col_8: "none",
            display_all_text: "Alle anzeigen",
            sort_select: true
        };
        var tf2 = setFilterGrid("machines", table_Props);
        function changeMachine(id) {
            window.location.href = "changemachine.php?id="+id;
        }
        function getAjaxRequest() {
            var ajax = null;
            ajax = new XMLHttpRequest;
            return ajax;
        }
        function saveCheckbox(option, id) {
            preloader.toggle();
            request = getAjaxRequest();
            var url = "../api/api.php";
            if (document.getElementById('inet_'+id).checked) {
                var inet = '1';
            } else {
                var inet = '0';
            }
            if (document.getElementById('teacher_'+id).checked) {
                var teacher = '1';
            } else {
                var teacher = '0';
            }
            var params = 'request=' + encodeURIComponent(JSON.stringify({
                savemachine: {
                    inet: inet,
                    teacher: teacher,
                    id: id,
                    mode: 'change',
                },
            }));
            request.onreadystatechange=stateChangedSave;
            request.open("POST",url,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(params);
            function stateChangedSave() {
                if (request.readyState == 4) {
                    var response = JSON.parse(request.responseText);
                    if (response.savemachine == "SUCCESS") {
                        preloader.toggle();
                    } else {
                        preloader.toggle();
                        document.getElementById(option+'_'+id).checked = !document.getElementById(option+'_'+id).checked;
                        swal({
                            title: "Es ist ein Fehler aufgetreten.",
                            text: "Bitte erneut versuchen.",
                            type: "error",
                        })
                    }
                }
            }
        }
    </script>
</body>
</html>
