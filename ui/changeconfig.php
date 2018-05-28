<!DOCTYPE html>
<?php
    $page = "Konfigurationsprofile";
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
    <title>Konfigurationsprofil bearbeiten - PhilleConnect Admin</title>
    <?php include "includes.php"; ?>
    <style>
        table {
            width: 100%;
        }
    </style>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px;"><b>KONFIGURATIONSPROFIL</b>BEARBEITEN</p>
        <br />
        <?php
            $request = "SELECT * FROM configs WHERE id = ".mysqli_real_escape_string($database, $_GET['id']);
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
                        <td>
                            <?php
                                if ($_GET['mode'] == 'new') {
                                    ?>
                                    <input type="text" id="name" value="<?php echo $response['name']; ?>"/>
                                    <?php
                                } else {
                                    ?>
                                    <input type="text" readonly id="name" value="<?php echo $response['name']; ?>"/>
                                    <?php
                                }
                            ?>
                        </td>
                    </tr>
                    <tr class="alt">
                        <td>SMB Server URL:</td>
                        <td><input type="text" id="smb" value="<?php echo $response['smbserver']; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Betriebssystem:</td>
                        <td>
                            <?php
                                if ($_GET['mode'] == 'new') {
                                    if ($response['os'] == 'win') {
                                        ?>
                                        <input type="radio" name="os" id="linux"/> Linux
                                        <br />
                                        <input type="radio" name="os" id="win" checked/> Windows
                                        <?php
                                    } else {
                                        ?>
                                        <input type="radio" name="os" id="linux" checked/> Linux
                                        <br />
                                        <input type="radio" name="os" id="win"/> Windows
                                        <?php
                                    }
                                } else {
                                    echo $response['os'];
                                    ?>
                                    <input type="hidden" id="os" value="<?php echo $response['os']; ?>"/>
                                    <?php
                                }
                            ?>
                        </td>
                    </tr>
                    <tr class="alt">
                        <td>Herunterfahren nach ... Sekunden:</td>
                        <td><input type="text" id="shutdown" onkeypress='return event.charCode >= 48 && event.charCode <= 57' value="<?php echo $response['shutdown']; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Gruppenlaufwerke:</td>
                        <td>
                            <table id="group-table">
                            </table>
                        </td>
                    </tr>
                    <tr class="alt">
                        <td>Userlaufwerke:</td>
                        <td>
                            <table>
                                <tr>
                                    <td>Laufwerk 1:</td>
                                    <td><input type="text" id="driveone" value="<?php echo $response['driveone']; ?>"/></td>
                                    <td><input type="text" id="pathone" value="<?php echo $response['pathone']; ?>"/></td>
                                </tr>
                                <tr>
                                    <td>Laufwerk 2:</td>
                                    <td><input type="text" id="drivetwo" value="<?php echo $response['drivetwo']; ?>"/></td>
                                    <td><input type="text" id="pathtwo" value="<?php echo $response['pathtwo']; ?>"/></td>
                                </tr>
                                <tr>
                                    <td>Laufwerk 3:</td>
                                    <td><input type="text" id="drivethree" value="<?php echo $response['drivethree']; ?>"/></td>
                                    <td><input type="text" id="paththree" value="<?php echo $response['paththree']; ?>"/></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>Infotexte:</td>
                        <td>
                            <table>
                                <tr>
                                    <td>Bitte anmelden:</td>
                                    <td><textarea class="infotext" id="dologin"><?php echo $response['dologin']; ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>Anmeldung läuft:</td>
                                    <td><textarea class="infotext" id="loginpending"><?php echo $response['loginpending']; ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>Anmeldung fehlgeschlagen:</td>
                                    <td><textarea class="infotext" id="loginfailed"><?php echo $response['loginfailed']; ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>Zugangsdaten falsch:</td>
                                    <td><textarea class="infotext" id="wrongcredentials"><?php echo $response['wrongcredentials']; ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>Netzwerkfehler:</td>
                                    <td><textarea class="infotext" id="networkfailed"><?php echo $response['networkfailed']; ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>Anmeldung erfolgreich:</td>
                                    <td><textarea class="infotext" id="success"><?php echo $response['success']; ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>Hinweisfenster:</td>
                                    <td><textarea class="infotext" id="infotext"><?php echo $response['infotext']; ?></textarea></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="alt">
                        <td>Servicemode:</td>
                        <td><input type="checkbox" id="servicemode"/></td>
                    </tr>
                    <tr>
                        <td>Aktion:</td>
                        <td><button onclick="goBack()">Abbrechen</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button onclick="saveConfig()">Speichern</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button onclick="deleteConfig()">Löschen</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        <?php
            $gfrequest = "SELECT id, name FROM groupfolders";
            $gfquery = mysqli_query($database, $gfrequest);
            $array = array();
            while ($gfresponse = mysqli_fetch_assoc($gfquery)) {
                $entry = array($gfresponse['id'], $gfresponse['name']);
                array_push($array, $entry);
            }
        ?>
        var availableGroupfolders = JSON.parse('<?php echo json_encode($array); ?>');
        if ('<?php echo $response['groupfolders']; ?>' != '') {
            var groupfolders = JSON.parse('<?php echo $response['groupfolders']; ?>');
        } else {
            var groupfolders = [];
        }
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        writeGroupFolders();
        <?php
            if ($response['servicemode'] == '1') {
        ?>
		    document.getElementById('servicemode').checked = true;
		    <?php
            }
        ?>
        function writeGroupFolders() {
            document.getElementById("group-table").innerHTML = '';
            for (var c = 0; c < groupfolders.length; c++) {
                for (var i = 0; i < availableGroupfolders.length; i++) {
                    if (availableGroupfolders[i][0] == groupfolders[c][1]) {
                        var name = availableGroupfolders[i][1];
                    }
                }
                document.getElementById("group-table").innerHTML += '<tr><td>'+groupfolders[c][0]+' | '+name+' | <a href="#" onclick="changeGroupFolder('+c+')">Bearbeiten</a> | <a href="#" onclick="deleteGroupFolder('+c+')">Löschen</a></td></tr>';
            }
            document.getElementById("group-table").innerHTML += '<tr><td><button onclick="addGroupFolder()">Laufwerk hinzufügen</button></td></tr>';
        }
        function changeGroupFolder(index) {
            swal({
                title: 'Gruppenlaufwerk bearbeiten',
                html: '<b>Windows:</b> Laufwerksbuchstabe (z.B. \'J:\')<br /><b>Linux:</b> Pfad (z.B. \'/media/\')<br />Pfad am Rechner: <input type="text" id="machinepath" value="'+groupfolders[index][0]+'"/>',
            }).then(function() {
                swal.disableButtons();
                groupfolders[index][0] = document.getElementById("machinepath").value;
                writeGroupFolders();
            })
        }
        function deleteGroupFolder(id) {
            groupfolders.splice(id, 1);
            writeGroupFolders();
        }
        function addGroupFolder() {
            var table = '<div class="datagrid"><table><thead><tr><th>Wählen:</th><th>Gruppenlaufwerk:</th></tr></thead><tbody>';
            for (var i = 0; i < availableGroupfolders.length; i++) {
                if ((i % 2) == 0) {
                    table += '<tr>';
                } else {
                    table += '<tr class="alt">';
                }
                table += '<td><input name="groupradio" type="radio" value="'+availableGroupfolders[i][0]+'"/></td><td>'+availableGroupfolders[i][1]+'</td></tr>';
            }
            table += '</tbody></table></div>';
            swal({
                title: 'Gruppenlaufwerk hinzufügen',
                html: '<b>Windows:</b> Laufwerksbuchstabe (z.B. \'J:\')<br /><b>Linux:</b> Pfad (z.B. \'/media/\')<br />Pfad am Rechner: <input type="text" id="machinepath" placeholder="Mountpunkt"/><br />'+table,
                closeOnConfirm: false,
            }).then(function() {
                swal.disableButtons();
                var radval = '';
                var radios = document.getElementsByName('groupradio');
                for (var i = 0, length = radios.length; i < length; i++) {
                    if (radios[i].checked) {
                        radval = radios[i].value;
                        break;
                    }
                }
                if (document.getElementById("machinepath").value == '' || radval == '') {
                    swal({
                        title: 'Bitte alle Felder ausfüllen.',
                        text: 'Laufwerk nicht hinzugrfügt.',
                        type: 'warning',
                    })
                } else {
                    var newfolder = [document.getElementById("machinepath").value, radval];
                    groupfolders.push(newfolder);
                    writeGroupFolders();
                    swal({title:"", timer:1});
                }
            })
        }
        function goBack() {
            window.location.href = 'config.php';
        }
        function getAjaxRequest() {
            var ajax = null;
            ajax = new XMLHttpRequest;
            return ajax;
        }
        function saveConfig() {
            <?php
                if ($_GET['mode'] == 'new') {
            ?>
            if (document.getElementById("linux").checked) {
                var os = 'linux';
            } else {
                var os = 'win';
            }
            <?php
                } else {
            ?>
            var os = document.getElementById('os').value;
            <?php
                }
            ?>
            var mode = '<?php echo $_GET['mode']; ?>';
            request = getAjaxRequest();
            var url = "../api/api.php";
            if (document.getElementById('servicemode').checked) {
                var servicemode = '1';
            } else {
                var servicemode = '0';
            }
            var params = 'request=' + encodeURIComponent(JSON.stringify({
                saveconfig: {
                    name: document.getElementById("name").value,
                    os: os,
                    smbserver: document.getElementById("smb").value,
                    driveone: document.getElementById("driveone").value,
                    drivetwo: document.getElementById("drivetwo").value,
                    drivethree: document.getElementById("drivethree").value,
                    pathone: document.getElementById("pathone").value,
                    pathtwo: document.getElementById("pathtwo").value,
                    paththree: document.getElementById("paththree").value,
                    shutdown: document.getElementById("shutdown").value,
                    dologin: document.getElementById("dologin").value,
                    loginpending: document.getElementById("loginpending").value,
                    loginfailed: document.getElementById("loginfailed").value,
                    wrongcredentials: document.getElementById("wrongcredentials").value,
                    networkfailed: document.getElementById("networkfailed").value,
                    success: document.getElementById("success").value,
                    groupfolders: groupfolders,
                    infotext: document.getElementById("infotext").value,
                    mode: mode,
                    id: '<?php echo $_GET['id'] ?>',
                    servicemode: servicemode,
                },
            }));
            request.onreadystatechange=stateChangedSave;
            request.open("POST",url,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(params);
            function stateChangedSave() {
                if (request.readyState == 4) {
                    var response = JSON.parse(request.responseText);
                    if (response.saveconfig == "SUCCESS") {
                        swal({
                            title: "Änderungen erfolgreich gespeichert!",
                            type: "success",
                        }).then(function() {
                            swal.disableButtons();
                            window.location.href = 'config.php';
                        })
                    } else if (response.saveconfig == "ERR_NAME_EXISTS") {
                        swal({
                            title: "Es existiert bereits ein Konfigurationsprofil mit diesem Namen.",
                            text: "Bitte wähle einen anderen Namen und erneut versuchen.",
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
        function deleteConfig() {
            swal({
                title: 'Konfigurationsprofil löschen?',
                text: 'Das Konfigurationsprofil wird für immer verschwunden sein (eine lange Zeit)!',
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
                            saveconfig: {
                                name: document.getElementById("name").value,
                                os: document.getElementById("os").value,
                                mode: 'delete',
                                id: '<?php echo $_GET['id'] ?>',
                            }
                        }));
                        request.onreadystatechange=stateChangedSave;
                        request.open("POST",url,true);
                        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        request.send(params);
                        function stateChangedSave() {
                            if (request.readyState == 4) {
                                var response = JSON.parse(request.responseText);
                                if (response.saveconfig == "SUCCESS") {
                                    swal({
                                        title: "Konfigurationsprofil erfolgreich gelöscht!",
                                        type: "success",
                                    }).then(function() {
                                        window.location.href = 'config.php';
                                    })
                                } else if (response.saveconfig == "ERR_IS_USED") {
                                    swal({
                                        title: "Profil in Verwendung.",
                                        text: "Dieses Konfigurationsprofil ist derzeit in Verwendung. Bitte weisen Sie zuerst allen Rechnern, die dieses Profil verwenden, ein anderes Profil zu.",
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
                    })
                },
            });
        }
    </script>
</body>
</html>
