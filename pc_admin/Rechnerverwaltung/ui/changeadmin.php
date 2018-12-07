<!DOCTYPE html>
<?php
    $page = "Admin-Accounts";
    include "../api/dbconnect.php";
    session_start();
    if ($_SESSION['user'] == null || $_SESSION['user'] == '' || ($_SESSION['timeout'] + 1200) < time()) {
        header("Location: nologin.php");
    } elseif ($_SESSION['type'] != '1') {
        header("Location: restricted.php");
    } else {
        $_SESSION['timeout'] = time();
        include "menue.php";
    }
?>
<html lang="de">
<head>
    <title>Admin-Account bearbeiten - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>ADMIN</b>BEARBEITEN</p>
        <p>Soll das Passwort nicht geändert werden, bitte die entsprechenden Felder leer lassen!</p>
        <div class="datagrid">
            <table>
                <thead>
                    <tr>
                        <th>Einstellung:</th>
                        <th>Parameter:</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $request = "SELECT username, type FROM userdata WHERE id = ".mysqli_real_escape_string($database, $_GET['id']);
                        $query = mysqli_query($database, $request);
                        $response = mysqli_fetch_assoc($query);
                        if ($response['type'] == 1) {
                            $options = '<option value="1" selected>Administrator</option><option value="2">Accountverwalter</option><option value="3">Rechnerverwalter</option>';
                        } else if ($response['type'] == 2) {
                            $options = '<option value="1">Administrator</option><option value="2" selected>Accountverwalter</option><option value="3">Rechnerverwalter</option>';
                        } else if ($response['type'] == 3) {
                            $options = '<option value="1">Administrator</option><option value="2">Accountverwalter</option><option value="3" selected>Rechnerverwalter</option>';
                        }
                    ?>
                    <tr>
                        <td>Nutzername</td>
                        <td><input type="text" id="username" value="<?php echo $response['username']; ?>"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Neues Passwort (leer lassen um altes Passwort zu behalten)</td>
                        <td><input type="password" id="new_pwd" oninput="checkPassword()"/></td>
                    </tr>
                    <tr>
                        <td>Neues Passwort bestätigen</td>
                        <td><input type="password" id="new_pwd2" oninput="checkPassword()"/></td>
                    </tr>
                    <tr class="alt">
                        <td></td>
                        <td id="pwcheck"></td>
                    </tr>
                    <tr>
                        <td>Berechtigungsstufe</td>
                        <td>
                            <select id="rights">
                                <?php echo $options; ?>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button onclick="goBack()">Abbrechen</button>
        <button onclick="modifySettings()">Speichern</button>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        function goBack() {
            window.location.href = 'adminaccounts.php';
        }
        function checkPassword() {
            if (document.getElementById('new_pwd').value == '' && document.getElementById('new_pwd2').value == '') {
                document.getElementById('pwcheck').innerHTML = 'Keine Eingabe';
            } else if (document.getElementById('new_pwd').value == document.getElementById('new_pwd2').value) {
                document.getElementById('pwcheck').innerHTML = '<p style="color: green;">Passwörter identisch.</p>';
            } else {
                document.getElementById('pwcheck').innerHTML = '<p style="color: red;">Passwörter prüfen!</p>';
            }
        }
        function modifySettings() {
            swal({
                title: 'Änderungen speichern?',
                showCancelButton: true,
                cancelButtonText: 'Abbrechen',
                confirmButtonText: 'Speichern',
                closeOnConfirm: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                type: 'question',
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        request = getAjaxRequest();
                        var url = "../api/api.php";
                        var params = 'request=' + encodeURIComponent(JSON.stringify({
                            changeadmin: {
                                id: '<?php echo $_GET['id']; ?>',
                                username: document.getElementById('username').value,
                                pwd: document.getElementById('new_pwd').value,
                                pwd2: document.getElementById('new_pwd2').value,
                                type: document.getElementById('rights').value,
                            },
                        }));
                        request.onreadystatechange=stateChangedSave;
                        request.open("POST",url,true);
                        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        request.send(params);
                        function stateChangedSave() {
                            if (request.readyState == 4) {
                                var response = JSON.parse(request.responseText);
                                if (response.changeadmin == "SUCCESS") {
                                    swal({
                                        title: "Erfolgreich gespeichert!",
                                        type: "success",
                                    }).then(function() {
                                        window.location.href = 'adminaccounts.php';
                                    });
                                } else if (response.changeadmin == "ERR_ACCESS_DENIED") {
                                    swal({
                                        title: "Keine Berechtigung.",
                                        type: "error",
                                    })
                                } else if (response.changeadmin == "ERR_PASSWORDS_DIFFERENT") {
                                    swal({
                                        title: "Die Passwörter stimmen nicht überein.",
                                        text: "Bitte eingaben überprüfen.",
                                        type: "warning",
                                    })
                                } else if (response.changeadmin == "ERR_UPDATE_FAILED") {
                                    swal({
                                        title: "Es ist ein Datenbankfehler aufgetreten.",
                                        text: "Bitte erneut versuchen.",
                                        type: "error",
                                    })
                                } else if (response.changeadmin == "ERR_USER_EXISTS") {
                                    swal({
                                        title: "Dieser Nutzer existiert bereits.",
                                        text: "Bitte wähle einen anderen Nutzernamen.",
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
                }
            });
        }
        checkPassword();
    </script>
</body>
</html>
