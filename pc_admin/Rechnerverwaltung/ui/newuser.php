<!DOCTYPE html>
<?php
    $page = 'Nutzer hinzufügen';
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
    <title>Account anlegen - PhilleConnect Admin</title>
    <?php include "includes.php"; ?>
</head>
<body>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>ACCOUNT</b>ANLEGEN</p>
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
                        <td id="cn"></td>
                    </tr>
                    <tr class="alt">
                        <td>Vorname:*</td>
                        <td><input type="text" id="givenname" oninput="parseUserName()"/></td>
                    </tr>
                    <tr>
                        <td>Nachname:*</td>
                        <td><input type="text" id="sn" oninput="parseUserName()"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Home-Laufwerk:</td>
                        <td><input type="text" id="home"/></td>
                    </tr>
                    <tr>
                        <td>Gruppe:*</td>
                        <td><input type="radio" name="group" id="teachers" onclick="parseUserName()"/>&nbsp;Lehrer&nbsp;&nbsp;<input type="radio" name="group" id="students" onclick="parseUserName()"/>&nbsp;Schüler</td>
                    </tr>
                    <tr class="alt">
                        <td>Klasse/Kürzel:*</td>
                        <td><input type="text" id="class"/></td>
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
                                    for ($i = $now; $i >= ($now-110); $i--) {
                                        echo '<option value="'.$i.'">'.$i.'</option>';
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="alt">
                        <td>E-Mail Addresse:</td>
                        <td><input type="email" id="email"/></td>
                    </tr>
                    <tr>
                        <td></td>
                        <?php
                            if (is_writeable('/home/students') && is_writeable('/home/teachers')) {
                        ?>
                        <td><input type="checkbox" id="createhome" checked/>&nbsp;Home-Verzeichnis erstellen</td>
                        <?php
                            } else {
                        ?>
                        <td><input type="checkbox" id="createhome" onclick="return false;"/>&nbsp;<s>Home-Verzeichnis erstellen</s><p style="color: red;">Keine Schreibrechte in /home/students und / oder /home/teachers!</p></td>
                        <?php
                            }
                        ?>
                    </tr>
                    <tr class="alt">
                        <td>Passwort:</td>
                        <td><input type="checkbox" checked id="customPassword" onclick="changeCustomPassword()"/>&nbsp;Geburtsdatum als Passwort verwenden</td>
                    </tr>
                    <tr class="alt">
                        <td></td>
                        <td><input type="password" id="customPassword1" disabled/></td>
                    </tr>
                    <tr class="alt">
                        <td></td>
                        <td id="pwsame"></td>
                    </tr>
                    <tr>
                        <td>Aktion:</td>
                        <td><button onclick="goBack()">Abbrechen</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button onclick="addAccount()">Nutzer anlegen</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        function goBack() {
            window.location.href = 'index.php';
        }
        function getAjaxRequest() {
            var ajax = null;
            ajax = new XMLHttpRequest;
            return ajax;
        }
        function addAccount() {
            if (document.getElementById('teachers').checked) {
                var group = 'teachers';
            } else {
                var group = 'students';
            }
            if (document.getElementById('createhome').checked) {
                var createHome = '1';
            } else {
                var createHome = '0';
            }
            if (document.getElementById('customPassword').checked) {
                var password = document.getElementById("day").value+""+document.getElementById("month").value+""+document.getElementById("year").value;
            } else {
                var password = document.getElementById('customPassword1').value;
            }
            request = getAjaxRequest();
            var url = "../api/api.php";
            var params = "request=" + encodeURIComponent(JSON.stringify({
                addaccount: {
                    givenname: document.getElementById("givenname").value,
                    sn: document.getElementById("sn").value,
                    home: document.getElementById("home").value,
                    userclass: document.getElementById("class").value,
                    cn: document.getElementById("cn").innerHTML,
                    group: group,
                    gebdat: document.getElementById("day").value+"."+document.getElementById("month").value+"."+document.getElementById("year").value,
                    email: document.getElementById("email").value,
                    createhome: createHome,
                    passwd: password,
                },
            }));
            request.onreadystatechange=stateChangedSave;
            request.open("POST",url,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(params);
            function stateChangedSave() {
                if (request.readyState == 4) {
                    var response = JSON.parse(request.responseText);
                    if (response.addaccount == "SUCCESS") {
                        swal({
                            title: "Änderungen erfolgreich gespeichert!",
                            type: "success",
                        }).then(function() {
                            window.location.href = 'index.php';
                        })
                    } else if (response.addaccount == "ERR_ADD_OBJECT") {
                        swal({
                            title: "Es ist ein Fehler aufgetreten.",
                            text: "Der Nutzer konnte nicht hinzugefügt werden.",
                            type: "error",
                        })
                    } else if (response.addaccount == "ERR_ADD_TO_GROUP") {
                        swal({
                            title: "Es ist ein Fehler aufgetreten.",
                            text: "Der Nutzer wurde hinzugrfügt, konnte jedoch keiner Gruppe zugeordnet werden.",
                            type: "warning",
                        })
                    } else if (response.addaccount == "ERR_UPDATE_UID") {
                        swal({
                            title: "Es ist ein schwerwiegender Fehler aufgetreten.",
                            text: "WARNUNG: Der Nutzer wurde hinzugrfügt, jedoch konnte die User-ID nicht erhöht werden. Dies wird zu Sicherheitsproblemen führen, sollten Sie einen weiteren Nutzer hinzufügen!",
                            type: "error",
                        })
                    } else if (response.addaccount == "ERR_CREATE_HOME") {
                        swal({
                            title: "Es konnte kein Home-Ordner angelegt werden.",
                            text: "Anscheinend kann PHP nicht in /home/teachers und /home/students schreiben. Bitte lege den Ordner manuell an.",
                            type: "error",
                        })
                    } else if (response.addaccount == "ERR_HOME_USER") {
                        swal({
                            title: "Dem Home-Ordner konnte kein Besitzer zugewiesen werden.",
                            text: "Bitte korrigiere die Ordnerrechte manuell.",
                            type: "error",
                        })
                    } else if (response.addaccount == "ERR_HOME_GROUP") {
                        swal({
                            title: "Dem Home-Ordner konnte keine Gruppe zugewiesen werden.",
                            text: "Bitte korrigiere die Ordnerrechte manuell.",
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
        function parseUserName() {
            var combi = document.getElementById('givenname').value + '.' + document.getElementById('sn').value;
            var username = combi.replace(/ /g, '_').toLowerCase().replace(/ü/g, 'ue').replace(/ö/g, 'oe').replace(/ä/g, 'ae').replace(/ß/g, 'ss');
            username = removeDiacritics(username);
            document.getElementById('cn').innerHTML = username;
            if (document.getElementById('teachers').checked) {
                document.getElementById('home').value = '/home/teachers/' + username;
            } else {
                document.getElementById('home').value = '/home/students/' + username;
            }
        }
        function changeCustomPassword() {
            if (document.getElementById('customPassword').checked) {
                document.getElementById('customPassword1').disabled = true;
            } else {
                document.getElementById('customPassword1').disabled = false;
            }
        }
    </script>
</body>
</html>
