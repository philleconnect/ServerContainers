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
    <title>Admin-Accounts - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>ADMIN</b>ACCOUNTS</p>
        <div class="datagrid">
            <table>
                <thead>
                    <tr>
                        <th>Nutzername:</th>
                        <th>Berechtigungsstufe:</th>
                        <th>Aktion:</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $request = "SELECT id, type, username FROM userdata";
                        $query = mysqli_query($database, $request);
                        $style = true;
                        while ($response = mysqli_fetch_assoc($query)) {
                            if ($response['type'] == 1) {
                                $type = 'Administrator';
                            } else if ($response['type'] == 2) {
                                $type = 'Accountverwalter';
                            } else if ($response['type'] == 3) {
                                $type = 'Rechnerverwalter';
                            }
                            if ($style) {
                    ?>
                    <tr>
                    <?php
                            } else {
                    ?>
                    <tr class="alt">
                    <?php
                            }
                    ?>
                        <td><?php echo $response['username']; ?></td>
                        <td><?php echo $type; ?></td>
                        <td><a href="changeadmin.php?id=<?php echo $response['id']; ?>">Bearbeiten</a>&nbsp;|&nbsp;<a onclick="deleteAdmin(<?php echo $response['id']; ?>)">Löschen</a></td>
                    </tr>
                    <?php
                            $style = !$style;
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <p></p>
        <div class="datagrid">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Admin-Account hinzufügen</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Nutzername:</td>
                        <td><input type="text" id="new_user" size="40"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Berechtigungsstufe:</td>
                        <td>
                            <select id="new_rights">
                                <option value="1">Administrator</option>
                                <option value="2">Accountverwalter</option>
                                <option value="3">Rechnerverwalter</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Passwort:</td>
                        <td><input type="password" id="new_pwd" size="40" oninput="checkPassword()"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Passwort bestätigen:</td>
                        <td><input type="password" id="new_pwd2" size="40" oninput="checkPassword()"/></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td id="pwcheck"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button onclick="addAdmin()">Admin-Account hinzufügen</button>
    </div>
    <script>
        function checkPassword() {
            if (document.getElementById('new_pwd').value == '' && document.getElementById('new_pwd2').value == '') {
                document.getElementById('pwcheck').innerHTML = 'Keine Eingabe';
            } else if (document.getElementById('new_pwd').value == document.getElementById('new_pwd2').value) {
                document.getElementById('pwcheck').innerHTML = '<p style="color: green;">Passwörter identisch.</p>';
            } else {
                document.getElementById('pwcheck').innerHTML = '<p style="color: red;">Passwörter prüfen!</p>';
            }
        }
        checkPassword();
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        var table_Props = {
            col_1: "select",
            col_2: "none",
            display_all_text: "Alle anzeigen",
            sort_select: true
        };
        var tf2 = setFilterGrid("users", table_Props);
        function deleteAdmin(id) {
            swal({
                title: 'Admin wirklich löschen?',
                input: 'password',
                inputPlaceholder: 'Passwort zur Bestätigung eingeben',
                showCancelButton: true,
                cancelButtonText: 'Abbrechen',
                confirmButtonText: 'Löschen',
                allowOutsideClick: false,
                allowEscapeKey: false,
                type: 'question',
                inputAttributes: {
                    'autocapitalize': 'off',
                    'autocorrect': 'off'
                },
                preConfirm: function(password) {
                    return new Promise(function(resolve) {
                        request = getAjaxRequest();
                        var url = "../api/api.php";
                        var params = 'request=' + encodeURIComponent(JSON.stringify({
                            deleteadmin: {
                                id: id,
                                adminpw: password,
                            },
                        }));
                        request.onreadystatechange=stateChangedDelete;
                        request.open("POST",url,true);
                        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        request.send(params);
                        function stateChangedDelete() {
                            if (request.readyState == 4) {
                                var response = JSON.parse(request.responseText);
                                if (response.deleteadmin == 'SUCCESS') {
                                    swal({
                                        title: 'Admin-Account erfolgreich gelöscht!',
                                        type: 'success',
                                    }).then(function() {
                                        window.location.reload();
                                    });
                                } else if (response.deleteadmin == 'ERR_UNAUTHORIZED') {
                                    swal.showValidationError('Passwort falsch!');
                                    swal.enableButtons();
                                } else if (response.deleteadmin == 'ERR_IS_LAST') {
                                    swal({
                                        title: 'Löschen fehlgeschlagen',
                                        text: 'Es muss mindestens einen Admin-Account geben. Bitte legen Sie zunächst einen neuen Admin-Account an.',
                                        type: 'error',
                                    })
                                } else {
                                    swal({
                                        title: 'Es ist ein Fehler aufgetreten!',
                                        text: "Bitte erneut versuchen.",
                                        type: 'error',
                                    });
                                }
                            }
                        }
                    })
                }
            })
        }
        function addAdmin() {
            swal({
                title: 'Admin hinzufügen?',
                showCancelButton: true,
                cancelButtonText: 'Abbrechen',
                confirmButtonText: 'Hinzufügen',
                closeOnConfirm: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                type: 'question',
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        request = getAjaxRequest();
                        var url = "../api/api.php";
                        var params = 'request=' + encodeURIComponent(JSON.stringify({
                            addadmin: {
                                username: document.getElementById('new_user').value,
                                pwd: document.getElementById('new_pwd').value,
                                pwd2: document.getElementById('new_pwd2').value,
                                type: document.getElementById('new_rights').value,
                            },
                        }));
                        request.onreadystatechange=stateChangedSave;
                        request.open("POST",url,true);
                        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        request.send(params);
                        function stateChangedSave() {
                            if (request.readyState == 4) {
                                var response = JSON.parse(request.responseText);
                                if (response.addadmin == "SUCCESS") {
                                    swal({
                                        title: "Admin erfolgreich hinzugefügt!",
                                        type: "success",
                                    }).then(function() {
                                        window.location.reload();
                                    });
                                } else if (response.addadmin == "ERR_ACCESS_DENIED") {
                                    swal({
                                        title: "Zugriffsfehler.",
                                        text: "Du besitzt nicht die nötigen Rechte für diese Aktion.",
                                        type: "warning",
                                    })
                                } else if (response.addadmin == "ERR_PASSWORDS_DIFFERENT") {
                                    swal({
                                        title: "Die Passwörter stimmen nicht überein.",
                                        text: "Bitte eingaben überprüfen.",
                                        type: "warning",
                                    })
                                } else if (response.addadmin == "ERR_UPDATE_FAILED") {
                                    swal({
                                        title: "Es ist ein Datenbankfehler aufgetreten.",
                                        text: "Bitte erneut versuchen.",
                                        type: "error",
                                    })
                                } else if (response.addadmin == "ERR_PASSWORD_EMPTY") {
                                    swal({
                                        title: "Die Eingabefelder sind leer.",
                                        text: "Bitte gib ein neues Passwort ein.",
                                        type: "error",
                                    })
                                } else if (response.addadmin == "ERR_USER_EXISTS") {
                                    swal({
                                        title: "Dieser Nutzer existiert bereits.",
                                        text: "Bitte wähle einen anderen Nutzernamen.",
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
                }
            });
        }
    </script>
</body>
</html>
