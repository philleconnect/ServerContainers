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
    <title>Gruppenlaufwerk erstellen - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>GRUPPENLAUFWERK</b>ERSTELLEN</p>
        <b>Hinweis:</b><br />
        Die Option 'Raumtausch-Laufwerk' legt das neue Gruppenlaufwerk als Unterordner von 'roomExchange' an. So bleibt die Übersicht erhalten, außerdem ist es möglich, bestimmten Rechnern (z.B. Lehrerzimmer) mit einem Eintrag alle Raumtausch-Laufwerke zuzuweisen. Die Berechtigung für Raumtausch-Laufwerke ist immer 'Lesen und Schreiben'.
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
                        <td><input type="name" id="name"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Schreibzugriff:</td>
                        <td><input type="checkbox" id="writeable"/></td>
                    </tr>
                    <tr>
                        <td>Schülerzugriff:</td>
                        <td><input type="checkbox" id="students"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Lehrerzugriff:</td>
                        <td><input type="checkbox" id="teachers"/></td>
                    </tr>
                    <tr>
                        <td>Raumtausch-Laufwerk:</td>
                        <td><input type="checkbox" id="roomexchange" onclick="changeFolderType()"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Ordnertyp:</td>
                        <td><input type="radio" id="type-old" name="type" onclick="changeFolderType()" checked/> Vorhandenen Ordner verwenden</td>
                    </tr>
                    <tr class="alt">
                        <td></td>
                        <td><input type="radio" id="type-new" name="type" onclick="changeFolderType()"/> Neuen Ordner anlegen</td>
                    </tr>
                    <tr id="type-old-choose-re" style="display: none;">
                        <td>Ordner wählen:</td>
                        <td id="old-room-exchange-folders"></td>
                    </tr>
                    <tr id="type-old-choose">
                        <td>Ordner wählen:</td>
                        <td id="old-folders"></td>
                    </tr>
                    <tr id="type-new-choose-re" style="display: none;">
                        <td>Ordnername:</td>
                        <td><input type="text" id="foldername-re"/></td>
                    </tr>
                    <tr id="type-new-choose" style="display: none;">
                        <td>Ordnername:</td>
                        <td><input type="text" id="foldername"/></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button onclick="createGroupfolder()">Speichern</button>
    </div>
    <script>
        <?php
            $existingFolders = array();
            $existingRoomExchangeFolders = array();
            $homedir = scandir('/home/');
            foreach ($homedir as $folder) {
                if (is_dir('/home/'.$folder) && $folder != 'roomExchange' && $folder != 'students' && $folder != 'teachers' && $folder != 'deleted' && $folder != '.' && $folder != '..') {
                    array_push($existingFolders, $folder);
                }
            }
            $roomexchangedir = scandir('/home/roomExchange/');
            foreach ($roomexchangedir as $folder) {
                if (is_dir('/home/roomExchange/'.$folder) && $folder != '.' && $folder != '..') {
                    array_push($existingRoomExchangeFolders, $folder);
                }
            }
        ?>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        var existingFolders = JSON.parse('<?php echo json_encode($existingFolders); ?>');
        var existingRoomExchangeFolders = JSON.parse('<?php echo json_encode($existingRoomExchangeFolders); ?>');
        var first = true;
        for (var i = 0; i < existingFolders.length; i++) {
            if (!first) {
                document.getElementById('old-folders').innerHTML += '<br />';
            } else {
                first = false;
            }
            document.getElementById('old-folders').innerHTML += '<input type="radio" name="old-folders" value="'+existingFolders[i]+'"/> '+existingFolders[i];
        }
        var first = true;
        for (var i = 0; i < existingRoomExchangeFolders.length; i++) {
            if (!first) {
                document.getElementById('old-room-exchange-folders').innerHTML += '<br />';
            } else {
                first = false;
            }
            document.getElementById('old-room-exchange-folders').innerHTML += '<input type="radio" name="old-room-exchange-folders" value="'+existingRoomExchangeFolders[i]+'"/> '+existingRoomExchangeFolders[i];
        }
        function createGroupfolder() {
            var path = '/home/';
            if (document.getElementById('roomexchange').checked) {
                path += 'roomExchange/';
            }
            if (document.getElementById('type-new').checked) {
                var createfolder = true;
                if (document.getElementById('roomexchange').checked) {
                    path += document.getElementById('foldername-re').value;
                } else {
                    path += document.getElementById('foldername').value;
                }
            } else {
                var createfolder = false;
                if (document.getElementById('roomexchange').checked) {
                    var folders = document.getElementsByName('old-room-exchange-folders');
                } else {
                    var folders = document.getElementsByName('old-folders');
                }
                for (var i = 0; i < folders.length; i++) {
                    if (folders[i].checked) {
                        path += folders[i].value;
                    }
                }
            }
            swal({
                title: 'Gruppenlaufwerk speichern?',
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                cancelButtonText: 'Abbrechen',
                confirmButtonColor: '#D33',
                cancelButtonColor: "#3085d6",
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        request = getAjaxRequest();
                        var url = "../api/api.php";
                        var params = 'request=' + encodeURIComponent(JSON.stringify({
                            addgroupfolder: {
                                name: document.getElementById('name').value,
                                writeable: document.getElementById('writeable').checked,
                                students: document.getElementById('students').checked,
                                teachers: document.getElementById('teachers').checked,
                                roomexchange: document.getElementById('roomexchange').checked,
                                createfolder: createfolder,
                                path: path,
                            },
                        }));
                        request.onreadystatechange=stateChangedSave;
                        request.open("POST",url,true);
                        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        request.send(params);
                        function stateChangedSave() {
                            if (request.readyState == 4) {
                                var response = JSON.parse(request.responseText);
                                if (response.addgroupfolder == "SUCCESS") {
                                    swal({
                                        title: "Grupenlaufwerk erfolgreich angelegt!",
                                        type: "success",
                                    }).then(function() {
                                        window.location.href = 'groupfolders.php';
                                    })
                                } else if (response.addgroupfolder == "ERR_DATABASE_ERROR") {
                                    swal({
                                        title: "Es ist ein Datenbankfehler aufgetreten.",
                                        text: "Bitte erneut versuchen."+response.addgroupfolder,
                                        type: "error",
                                    })
                                } else if (response.addgroupfolder == "ERR_UPDATE_SAMBA") {
                                    swal({
                                        title: "Die Aktualisierung des Samba-Servers ist fehlgeschlagen.",
                                        text: "Die Änderungen sind nicht verfügbar. Bitte überprüfen Sie den Samba-Container.",
                                        type: "error",
                                    })
                                } else if (response.addgroupfolder == "ERR_CREATE_FOLDER") {
                                    swal({
                                        title: "Der Serverordner konnte nicht erstellt werden.",
                                        text: "Es wird nicht möglich sein, auf den Ordner zuzugreifen. Bitte überprüfen Sie die Schreibrechte auf /home/.",
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
                    })
                },
            });
        }
        function changeFolderType() {
            if (document.getElementById('type-old').checked) {
                if (document.getElementById('roomexchange').checked) {
                    document.getElementById('type-old-choose').style.display = 'none';
                    document.getElementById('type-old-choose-re').style.display = 'block';
                } else {
                    document.getElementById('type-old-choose').style.display = 'block';
                    document.getElementById('type-old-choose-re').style.display = 'none';
                }
                document.getElementById('type-new-choose').style.display = 'none';
                document.getElementById('type-new-choose-re').style.display = 'none';
            } else {
                if (document.getElementById('roomexchange').checked) {
                    document.getElementById('type-new-choose').style.display = 'none';
                    document.getElementById('type-new-choose-re').style.display = 'block';
                } else {
                    document.getElementById('type-new-choose').style.display = 'block';
                    document.getElementById('type-new-choose-re').style.display = 'none';
                }
                document.getElementById('type-old-choose').style.display = 'none';
                document.getElementById('type-old-choose-re').style.display = 'none';
            }
        }
    </script>
</body>
</html>
