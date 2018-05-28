<!DOCTYPE html>
<?php
    $page = "Grundkonfiguration";
    include "../api/dbconnect.php";
    session_start();
    if ($_SESSION['user'] == null || $_SESSION['user'] == '' || ($_SESSION['timeout'] + 1200) < time()) {
        header("Location: nologin.php");
    } elseif ($_SESSION['type'] != '1') {
        header("Location: restricted.php");
    } else {
        $_SESSION['timeout'] = time();
        include "menue.php";
        include "../api/accessConfig.php";
    }
?>
<html lang="de">
<head>
    <title>Grundkonfiguration - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px;"><b>GRUND</b>KONFIGURATION</p>
        <br />
        <p>Einstellung globaler Konfigurationsparameter</p>
        <br />
        <p><b>Warnung:</b> Änderungen am globalen Passwort können die gesamte Installation blockieren.</p>
        <br />
        <div class="datagrid">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">LDAP-Server</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>URL:</td>
                        <td><input type="url" value="<?php echo loadConfig('ldap', 'url'); ?>" id="ldap_url" size="40"/></td>
                    </tr>
                    <tr class="alt">
                        <td>LDAP Admin-Passwort:</td>
                        <td><input type="text" value="<?php echo loadConfig('ldap', 'password'); ?>" id="ldap_password" size="40"/></td>
                    </tr>
                    <tr>
                        <td>Basis-DN:</td>
                        <td><input type="text" value="<?php echo loadConfig('ldap', 'basedn'); ?>" id="ldap_basedn" size="40"/></td>
                    </tr>
                    <tr class="alt">
                        <td>DN des Admin-Users:</td>
                        <td><input type="text" value="<?php echo loadConfig('ldap', 'admindn'); ?>" id="ldap_admindn" size="40"/></td>
                    </tr>
                    <tr>
                        <td>DN der Useraccounts:</td>
                        <td><input type="text" value="<?php echo loadConfig('ldap', 'usersdn'); ?>" id="ldap_usersdn" size="40"/></td>
                    </tr>
                    <tr class="alt">
                        <td>DN der Gruppen:</td>
                        <td><input type="text" value="<?php echo loadConfig('ldap', 'groupsdn'); ?>" id="ldap_groupsdn" size="40"/></td>
                    </tr>
                    <tr>
                        <td>CN der Schülergruppe:</td>
                        <td><input type="text" value="<?php echo loadConfig('ldap', 'studentscn'); ?>" id="ldap_studentscn" size="40"/></td>
                    </tr>
                    <tr class="alt">
                        <td>CN der Lehrergruppe:</td>
                        <td><input type="text" value="<?php echo loadConfig('ldap', 'teacherscn'); ?>" id="ldap_teacherscn" size="40"/></td>
                    </tr>
                    <tr>
                        <td>Samba-Hostname:</td>
                        <td><input type="text" value="<?php echo loadConfig('ldap', 'sambahostname'); ?>" id="ldap_sambahostname" size="40"/></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button onclick="saveObject('LDAP-Server Einstellungen', 'ldap')">LDAP-Einstellungen speichern</button>
        <div class="datagrid">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">IPFire</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>URL:</td>
                        <td><input type="url" value="<?php echo loadConfig('ipfire', 'url'); ?>" id="ipfire_url" size="40"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Port:</td>
                        <td><input type="text" value="<?php echo loadConfig('ipfire', 'port'); ?>" id="ipfire_port" size="40"/></td>
                    </tr>
                    <tr>
                        <td>Public Key file:</td>
                        <td><input type="text" value="<?php echo loadConfig('ipfire', 'pubkey'); ?>" id="ipfire_pubkey" size="40"/></td>
                    </tr>
                    <tr class="alt">
                        <td>RSA file:</td>
                        <td><input type="text" value="<?php echo loadConfig('ipfire', 'rsafile'); ?>" id="ipfire_rsafile" size="40"/></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button onclick="saveObject('IPFire Einstellungen', 'ipfire')">IPFire-Einstellungen speichern</button>
        <div class="datagrid">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Installation</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Globales Passwort:</td>
                        <td><input type="text" value="<?php echo loadConfig('globalPw', null); ?>" id="globalPw" size="40"/></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button onclick="saveObject('Installationseinstellungen', 'globalPw')">Installationseinstellungen speichern</button>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        function saveObject(title, prefix) {
            swal({
                title: title+' speichern?',
                showCancelButton: true,
                cancelButtonText: 'Abbrechen',
                confirmButtonText: 'Speichern',
                closeOnConfirm: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                type: 'question',
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        swal.disableButtons();
                        setParameter(prefix);
                    })
                }
            });
        }
        function getAjaxRequest() {
            var ajax = null;
            ajax = new XMLHttpRequest;
            return ajax;
        }
        function setParameter(prefix) {
            request = getAjaxRequest();
            var url = "../api/api.php";
            if (prefix == 'ldap') {
                var params = 'request=' + encodeURIComponent(JSON.stringify({
                    saveglobal: {
                        prefix: 'ldap',
                        url: document.getElementById('ldap_url').value,
                        password: document.getElementById('ldap_password').value,
                        basedn: document.getElementById('ldap_basedn').value,
                        admindn: document.getElementById('ldap_admindn').value,
                        usersdn: document.getElementById('ldap_usersdn').value,
                        groupsdn: document.getElementById('ldap_groupsdn').value,
                        studentscn: document.getElementById('ldap_studentscn').value,
                        teacherscn: document.getElementById('ldap_teacherscn').value,
                        sambahostname: document.getElementById('ldap_sambahostname').value,
                    }
                }));
            } else if (prefix == 'ipfire') {
                var params = 'request=' + encodeURIComponent(JSON.stringify({
                    saveglobal: {
                        prefix: 'ipfire',
                        url: document.getElementById('ipfire_url').value,
                        port: document.getElementById('ipfire_port').value,
                        pubkey: document.getElementById('ipfire_pubkey').value,
                        rsafile: document.getElementById('ipfire_rsafile').value,
                    }
                }));
            } else if (prefix == 'globalPw') {
                var params = 'request=' + encodeURIComponent(JSON.stringify({
                    saveglobal: {
                        prefix: 'globalPw',
                        globalPw: document.getElementById('globalPw').value,
                    }
                }));
            }
            request.onreadystatechange=stateChangedSave;
            request.open("POST",url,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(params);
            function stateChangedSave() {
                if (request.readyState == 4) {
                    var response = JSON.parse(request.responseText);
                    if (response.saveglobal == "SUCCESS") {
                        swal({
                            title: "Änderungen erfolgreich gespeichert!",
                            type: "success",
                        }).then(function() {
                            window.location.reload();
                        })
                    } else if (response.saveglobal == "ERR_ACCESS_DENIED") {
                        swal({
                            title: "Zugriffsfehler.",
                            text: "Du besitzt nicht die nötigen Rechte für diese Aktion.",
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
    </script>
</body>
</html>
