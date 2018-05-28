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
    <title>Gruppenlaufwerk bearbeiten - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>GRUPPENLAUFWERK</b>BEARBEITEN</p>
        <?php
            $request = "SELECT name, students, teachers, writeable, path FROM groupfolders WHERE id = ".$_GET['id'];
            $query = mysqli_query($database, $request);
            $response = mysqli_fetch_assoc($query);
        ?>
        <div class="datagrid">
            <table>
                <thead>
                    <tr>
                        <th>Einstellung:</th>
                        <th>Parameter:</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Name:</td>
                        <td><input type="text" id="name" value="<?php echo $response['name'] ?>"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Berechtigungen:</td>
                        <?php
                            if ($response['writeable'] == 1) {
                        ?>
                        <td><input type="checkbox" id="writeable" checked/> Schreibzugriff</td>
                        <?php
                            } else {
                        ?>
                        <td><input type="checkbox" id="writeable"/> Schreibzugriff</td>
                        <?php
                            }
                        ?>
                    </tr>
                    <tr>
                        <td></td>
                        <?php
                            if ($response['students'] == 1) {
                        ?>
                        <td><input type="checkbox" id="students" checked/> Zugriff für Schüler</td>
                        <?php
                            } else {
                        ?>
                        <td><input type="checkbox" id="students"/> Zugriff für Schüler</td>
                        <?php
                            }
                        ?>
                    </tr>
                    <tr class="alt">
                        <td></td>
                        <?php
                            if ($response['teachers'] == 1) {
                        ?>
                        <td><input type="checkbox" id="teachers" checked/> Zugriff für Lehrer</td>
                        <?php
                            } else {
                        ?>
                        <td><input type="checkbox" id="teachers"/> Zugriff für Lehrer</td>
                        <?php
                            }
                        ?>
                    </tr>
                    <tr>
                        <td>Aktion:</td>
                        <td><button onclick="goBack()">Abbrechen</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button onclick="deleteGroupFolder()">Löschen</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button onclick="saveGroupFolder()">Speichern</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        function saveGroupFolder() {
            request = getAjaxRequest();
            var url = "../api/api.php";
            if (document.getElementById("writeable").checked) {
                var writeable = 1;
            } else {
                var writeable = 0;
            }
            if (document.getElementById("students").checked) {
                var students = 1;
            } else {
                var students = 0;
            }
            if (document.getElementById("teachers").checked) {
                var teachers = 1;
            } else {
                var teachers = 0;
            }
            var params = 'request=' + encodeURIComponent(JSON.stringify({
                changegroupfolder: {
                    id: '<?php echo $_GET['id'] ?>',
                    name: document.getElementById("name").value,
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
                    if (response.changegroupfolder == "SUCCESS") {
                        swal({
                            title: "Änderungen erfolgreich gespeichert!",
                            type: "success",
                        }).then(function() {
                            window.location.href = 'groupfolders.php';
                        })
                    } else if (response.changegroupfolder == "ERR_DATABASE_ERROR") {
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
                    } else {
                        swal({
                            title: "Es ist ein Fehler aufgetreten.",
                            text: "Bitte erneut versuchen.",
                            type: "error",
                        })
                    }
                }
            }
        }
        function deleteGroupFolder() {
            <?php
                $deleterequest = "SELECT COUNT(*) AS anzahl FROM groupfolders WHERE path = '".$response['path']."'";
                $deletequery = mysqli_query($database, $deleterequest);
                $deleteresponse = mysqli_fetch_assoc($deletequery);
                if ($deleteresponse['anzahl'] > 1) {
            ?>
            var deleteFolder = false;
            <?php
                } else {
            ?>
            var deleteFolder = true;
            <?php
                }
            ?>
            request = getAjaxRequest();
            var url = "../api/api.php";
            var params = 'request=' + encodeURIComponent(JSON.stringify({
                deletegroupfolder: {
                    id: '<?php echo $_GET['id'] ?>',
                    deletefolder: deleteFolder,
                },
            }));
            request.onreadystatechange=stateChangedDelete;
            request.open("POST",url,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(params);
            function stateChangedDelete() {
                if (request.readyState == 4) {
                    var response = JSON.parse(request.responseText);
                    if (response.deletegroupfolder == "SUCCESS") {
                        swal({
                            title: "Änderungen erfolgreich gespeichert!",
                            type: "success",
                        }).then(function() {
                            window.location.href = 'groupfolders.php';
                        })
                    } else if (response.deletegroupfolder == "ERR_DELETE_FOLDER") {
                        swal({
                            title: "Der Serverordner konnte nicht gelöscht werden.",
                            text: "Es wird nicht möglich sein, einen gleichnamigen neuen Ordner anzulegen. Bitte überprüfen Sie die Rechte. Die Änderungen wurden nicht übernommen.",
                            type: "error",
                        })
                    } else if (response.deletegroupfolder == "ERR_UPDATE_SAMBA") {
                        swal({
                            title: "Die Aktualisierung des Samba-Servers ist fehlgeschlagen.",
                            text: "Die Änderungen sind nicht verfügbar. Bitte überprüfen Sie den Samba-Container.",
                            type: "error",
                        })
                    } else if (response.deletegroupfolder == "ERR_DATABASE_ERROR") {
                        swal({
                            title: "Es ist ein Datenbankfehler aufgetreten.",
                            text: "Bitte erneut versuchen.",
                            type: "error",
                        })
                    } else if (response.deletegroupfolder == "ERR_IS_USED") {
                        swal({
                            title: "Das Gruppenlaufwerk ist derzeit einem Konfigurationsprofil zugewiesen.",
                            text: "Bitte entfernen Sie zunächst das Gruppenlaufwerk aus diesem Profil, bevor Sie es löschen.",
                            type: "warning",
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
        }
        function goBack() {
            window.location.href = 'groupfolders.php';
        }
    </script>
</body>
</html>
