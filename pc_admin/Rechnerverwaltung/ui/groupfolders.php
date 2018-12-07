<!DOCTYPE html>
<?php
    $page = "Gruppenlaufwerke";
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
    <title>Gruppenlaufwerke - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>GRUPPEN</b>LAUFWERKE</p>
        <div class="datagrid">
            <table id="groupfolders">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Pfad</th>
                        <th>Zugriff Schüler</th>
                        <th>Zugriff Lehrer</th>
                        <th>Schreibzugriff</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $request = "SELECT * FROM groupfolders";
                        $query = mysqli_query($database, $request);
                        $style = false;
                        while ($response = mysqli_fetch_assoc($query)) {
                            if ($style) {
                    ?>
                    <tr class="alt">
                    <?php
                                $style = false;
                            } else {
                    ?>
                    <tr>
                    <?php
                                $style = true;
                            }
                    ?>
                        <td><?php echo $response['name']; ?></td>
                        <td><?php echo $response['path']; ?></td>
                    <?php
                            if ($response['students'] == 1) {
                    ?>
                        <td><input type="checkbox" id="students-cb-<?php echo $response['id'] ?>" checked onclick="updateGroupFolder(<?php echo $response['id'] ?>, '<?php echo $response['name']; ?>')"/></td>
                    <?php
                            } else {
                    ?>
                        <td><input type="checkbox" id="students-cb-<?php echo $response['id'] ?>" onclick="updateGroupFolder(<?php echo $response['id'] ?>, '<?php echo $response['name']; ?>')"/></td>
                    <?php
                            }
                    ?>
                    <?php
                            if ($response['teachers'] == 1) {
                    ?>
                        <td><input type="checkbox" id="teachers-cb-<?php echo $response['id'] ?>" checked onclick="updateGroupFolder(<?php echo $response['id'] ?>, '<?php echo $response['name']; ?>')"/></td>
                    <?php
                            } else {
                    ?>
                        <td><input type="checkbox" id="teachers-cb-<?php echo $response['id'] ?>" onclick="updateGroupFolder(<?php echo $response['id'] ?>, '<?php echo $response['name']; ?>')"/></td>
                    <?php
                            }
                    ?>
                    <?php
                            if ($response['writeable'] == 1) {
                    ?>
                        <td><input type="checkbox" id="writeable-cb-<?php echo $response['id'] ?>" checked onclick="updateGroupFolder(<?php echo $response['id'] ?>, '<?php echo $response['name']; ?>')"/></td>
                    <?php
                            } else {
                    ?>
                        <td><input type="checkbox" id="writeable-cb-<?php echo $response['id'] ?>" onclick="updateGroupFolder(<?php echo $response['id'] ?>, '<?php echo $response['name']; ?>')"/></td>
                    <?php
                            }
                    ?>
                        <td><a onclick="changeGroupFolder(<?php echo $response['id']; ?>)">Bearbeiten</a></td>
                    </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <button onclick="addGroupFolder()">Neues Gruppenlaufwerk anlegen</button>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        var table_Props = {
            col_2: "none",
            col_3: "none",
            col_4: "none",
            col_5: "none",
            display_all_text: "Alle anzeigen",
            sort_select: true
        };
        var tf2 = setFilterGrid("configs", table_Props);
        function addGroupFolder() {
            window.location.href = 'newgroupfolder.php';
        }
        function changeGroupFolder(id) {
            window.location.href = 'changegroupfolder.php?id='+id;
        }
        function updateGroupFolder(id, name) {
            preloader.toggle('SPEICHERN');
            request = getAjaxRequest();
            var url = "../api/api.php";
            if (document.getElementById("writeable-cb-"+id).checked) {
                var writeable = 1;
            } else {
                var writeable = 0;
            }
            if (document.getElementById("students-cb-"+id).checked) {
                var students = 1;
            } else {
                var students = 0;
            }
            if (document.getElementById("teachers-cb-"+id).checked) {
                var teachers = 1;
            } else {
                var teachers = 0;
            }
            var params = 'request=' + encodeURIComponent(JSON.stringify({
                changegroupfolder: {
                    id: id,
                    name: name,
                    writeable: writeable,
                    students: students,
                    teachers: teachers,
                },
            }));
            request.onreadystatechange=stateChangedSave;
            request.open("POST",url,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(params);
            function stateChangedSave() {
                if (request.readyState == 4) {
                    var response = JSON.parse(request.responseText);
                    preloader.toggle();
                    if (response.changegroupfolder == "ERR_DATABASE_ERROR") {
                        swal({
                            title: "Es ist ein Datenbankfehler aufgetreten.",
                            text: "Bitte erneut versuchen.",
                            type: "error",
                        })
                    } else if (response.changegroupfolder == "ERR_UPDATE_SAMBA") {
                        swal({
                            title: "Die Aktualisierung des Samba-Servers ist fehlgeschlagen.",
                            text: "Die Änderungen sind nicht verfügbar. Bitte überprüfen Sie den Samba-Container.",
                            type: "error",
                        })
                    } else if (response.changegroupfolder != "SUCCESS") {
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
