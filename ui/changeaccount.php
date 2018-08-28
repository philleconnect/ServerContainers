<!DOCTYPE html>
<?php
    $page = 'Accounts';
    include "../api/dbconnect.php";
    session_start();
    if ($_SESSION['user'] == null || $_SESSION['user'] == '' || ($_SESSION['timeout'] + 1200) < time()) {
        header("Location: nologin.php");
    } elseif ($_SESSION['type'] != '1' && $_SESSION['type'] != '2') {
        header("Location: restricted.php");
    } else {
        $_SESSION['timeout'] = time();
        include "menue.php";
    }
?>
<html lang="de">
<head>
    <title>Account bearbeiten - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>ACCOUNT</b>BEARBEITEN</p>
        <?php
            include "../api/accessConfig.php";
            $ldapconn = ldap_connect(loadConfig('ldap', 'url'));
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
            $r = ldap_bind($ldapconn);
            $allusers = ldap_search($ldapconn, loadConfig('ldap', 'usersdn').','.loadConfig('ldap', 'basedn'), "cn=".$_GET['user']);
            $users = ldap_get_entries($ldapconn, $allusers);
            $teacherGroup = ldap_search($ldapconn, loadConfig('ldap', 'groupsdn').','.loadConfig('ldap', 'basedn'), loadConfig('ldap', 'teacherscn'));
            $studentGroup = ldap_search($ldapconn, loadConfig('ldap', 'groupsdn').','.loadConfig('ldap', 'basedn'), loadConfig('ldap', 'studentscn'));
            $teacherGroupcontent = ldap_get_entries($ldapconn, $teacherGroup);
            $studentGroupcontent = ldap_get_entries($ldapconn, $studentGroup);
            if (in_array($users[0]['cn'][0], $teacherGroupcontent[0]['memberuid'])) {
                $group = 'Lehrer';
                $deleteGroup = 'teachers';
            } elseif (in_array($users[0]['cn'][0], $studentGroupcontent[0]['memberuid'])) {
                $group = 'Schüler';
                $deleteGroup = 'students';
            }
            $gebdat = explode('.', $users[0]['description'][0]);
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
                        <td>Nutzername:</td>
                        <td><?php echo $users[0]['cn'][0]; ?></td>
                    </tr>
                    <tr class="alt">
                        <td>Vorname:</td>
                        <td><input type="text" id="givenname" value="<?php echo $users[0]['givenname'][0]; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Nachname:</td>
                        <td><input type="text" id="sn" value="<?php echo $users[0]['sn'][0]; ?>"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Home-Laufwerk:</td>
                        <td><input type="text" id="home" value="<?php echo $users[0]['homedirectory'][0]; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Gruppe:</td>
                        <td><?php echo $group; ?></td>
                    </tr>
                    <tr class="alt">
                        <td>Klasse:</td>
                        <td><input type="text" id="class" value="<?php echo $users[0]['businesscategory'][0]; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Geburtsdatum:</td>
                        <td>
                            <select id="day">
                                <?php
                                    for ($i = 1; $i < 32; $i++) {
                                        if ($i < 10) {
                                            echo '<option value="0'.$i.'">'.$i.'</option>';
                                        } else {
                                            echo '<option value="'.$i.'">'.$i.'</option>';
                                        }
                                    }
                                ?>
                            </select>
                            .
                            <select id="month">
                                <option value="01">Januar</option>
                                <option value="02">Februar</option>
                                <option value="03">März</option>
                                <option value="04">April</option>
                                <option value="05">Mai</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Dezember</option>
                            </select>
                            .
                            <select id="year">
                                <?php
                                    $now = date('Y');
                                    for ($i = $now; $i >= ($now - 110); $i--) {
                                        echo '<option value="'.$i.'">'.$i.'</option>';
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="alt">
                        <td>E-Mail Addresse:</td>
                        <td><input type="email" id="email" value="<?php echo $users[0]['mail'][0]; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Neues Passwort:</td>
                        <td><input type="password" id="pw1" oninput="checkPw()"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Neues Passwort bestätigen:</td>
                        <td><input type="password" id="pw2" oninput="checkPw()"/></td>
                    </tr>
                    <tr class="alt">
                        <td></td>
                        <td id="pwresult"></td>
                    </tr>
                    <tr>
                        <td>Aktion:</td>
                        <td><button onclick="goBack()">Abbrechen</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button onclick="deleteAccount()">Löschen</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button onclick="saveAccount()">Speichern</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        <?php
            echo 'document.getElementById(\'day\').value = "'.$gebdat[0].'";';
            echo 'document.getElementById(\'month\').value = "'.$gebdat[1].'";';
            echo 'document.getElementById(\'year\').value = "'.$gebdat[2].'";';
        ?>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        var passwordIsOk = true;
        function goBack() {
            window.location.href = 'index.php';
        }
        function getAjaxRequest() {
            var ajax = null;
            ajax = new XMLHttpRequest;
            return ajax;
        }
        function saveAccount() {
            if (!passwordIsOk) {
                swal({
                    title: "Die Passwörter stimmen nicht überein.",
                    text: "Bitte erneut versuchen.",
                    type: "warning",
                })
            } else {
                request = getAjaxRequest();
                var url = "../api/api.php";
                var params = 'request=' + encodeURIComponent(JSON.stringify({
                    saveaccount: {
                        givenname: document.getElementById("givenname").value,
                        sn: document.getElementById("sn").value,
                        home: document.getElementById("home").value,
                        userclass: document.getElementById("class").value,
                        user: '<?php echo $_GET['user'] ?>',
                        gebdat: document.getElementById("day").value+"."+document.getElementById("month").value+"."+document.getElementById("year").value,
                        email: document.getElementById("email").value,
                        pwd: document.getElementById("pw1").value,
                        pwd2: document.getElementById("pw2").value,
                    },
                }));
                request.onreadystatechange=stateChangedSave;
                request.open("POST",url,true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send(params);
                function stateChangedSave() {
                    if (request.readyState == 4) {
                        var response = JSON.parse(request.responseText);
                        if (response.saveaccount == "SUCCESS") {
                            swal({
                                title: "Änderungen erfolgreich gespeichert!",
                                type: "success",
                            }).then(function() {
                                window.location.href = 'index.php';
                            })
                        } else if (response.saveaccount == "ERR_PASSWORDS_DIFFERENT") {
                            swal({
                                title: "Die Passwörter stimmen nicht überein.",
                                text: "Bitte erneut versuchen.",
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
        }
        function deleteAccount() {
            swal({
                title: 'Account löschen?',
                text: 'Der Account wird für immer verloren sein (eine lange Zeit), es werden jedoch keine Daten aus dem Benutzerverzeichnis gelöscht.',
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
                            deleteaccount: {
                                user: '<?php echo $_GET['user'] ?>',
                                group: '<?php echo $deleteGroup; ?>',
                            },
                        }));
                        request.onreadystatechange=stateChangedDelete;
                        request.open("POST",url,true);
                        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        request.send(params);
                        function stateChangedDelete() {
                            if (request.readyState == 4) {
                                var response = JSON.parse(request.responseText);
                                if (response.deleteaccount == "SUCCESS") {
                                    swal({
                                        title: "Account erfolgreich gelöscht!",
                                        text: "Der Benutzerorder wurde nach /home/deleted verschoben und verbleibt dort, bis ein neuer gleichnamiger Benutzer gelöscht wird.",
                                        type: "success",
                                    }).then(function() {
                                        window.location.href = 'index.php';
                                    })
                                } else if (response.deleteaccount == "ERR_DELETE_OLD_FOLDER") {
                                    swal({
                                        title: "Es ist ein Fehler aufgetreten.",
                                        text: "Der Nutzer wurde gelöscht. Ein nicht löschbarer Ordner blockiert den Platz in /home/deleted, weshalb der Benutzerordner nicht verschoben werden konnte.",
                                        type: "error",
                                    })
                                } else if (response.deleteaccount == "ERR_MOVE_HOME") {
                                    swal({
                                        title: "Es ist ein Fehler aufgetreten.",
                                        text: "Der Nutzer wurde gelöscht, sein Ordner konnte jedoch nicht nach /home/deleted verschoben werden.",
                                        type: "error",
                                    })
                                } else if (response.deleteaccount == "ERR_REMOVE_FROM_GROUP") {
                                    swal({
                                        title: "Es ist ein Fehler aufgetreten.",
                                        text: "Der Nutzer wurde gelöscht, konnte jedoch nicht aus der Gruppe entfernt werden. Es wurden keine Benutzerdaten gelöscht.",
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
        function checkPw() {
            if (document.getElementById('pw1').value === '' && document.getElementById('pw2').value === '') {
                document.getElementById('pwresult').innerHTML = '';
                passwordIsOk = true;
            } else if (document.getElementById('pw1').value === document.getElementById('pw2').value) {
                document.getElementById('pwresult').innerHTML = '<p style="color: green;">Passwörter stimmen überein.</p>';
                passwordIsOk = true;
            } else {
                document.getElementById('pwresult').innerHTML = '<p style="color: red;">Passwörter stimmen nicht überein.</p>';
                passwordIsOk = false;
            }
        }
    </script>
</body>
</html>
