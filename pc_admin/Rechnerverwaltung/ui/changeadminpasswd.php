<!DOCTYPE html>
<?php
    $page = "Admin-Passwort ändern";
    include "../api/dbconnect.php";
    session_start();
    if ($_SESSION['user'] == null || $_SESSION['user'] == '' || ($_SESSION['timeout'] + 1200) < time()) {
        header("Location: nologin.php");
    } else {
        $_SESSION['timeout'] = time();
        include "menue.php";
    }
?>
<html lang="de">
<head>
    <title>Admin-Passwort ändern - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>ADMINPASSWORT</b>ÄNDERN</p>
        <div class="datagrid">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Mein Passwort ändern</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Altes Passwort:</td>
                        <td><input type="password" id="old_pwd" size="40"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Neues Passwort:</td>
                        <td><input type="password" id="my_pwd" size="40" oninput="checkPassword()"/></td>
                    </tr>
                    <tr>
                        <td>Neues Passwort bestätigen:</td>
                        <td><input type="password" id="my_pwd2" size="40" oninput="checkPassword()"/></td>
                    </tr>
                    <tr class="alt">
                        <td></td>
                        <td id="pwcheck"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button onclick="changePassword()">Passwort ändern</button>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        function checkPassword() {
            if (document.getElementById('my_pwd').value == '' && document.getElementById('my_pwd2').value == '') {
                document.getElementById('pwcheck').innerHTML = 'Keine Eingabe';
            } else if (document.getElementById('my_pwd').value == document.getElementById('my_pwd2').value) {
                document.getElementById('pwcheck').innerHTML = '<p style="color: green;">Passwörter identisch.</p>';
            } else {
                document.getElementById('pwcheck').innerHTML = '<p style="color: red;">Passwörter prüfen!</p>';
            }
        }
        function changePassword() {
            swal({
                title: 'Passwort ändern?',
                showCancelButton: true,
                cancelButtonText: 'Abbrechen',
                confirmButtonText: 'Ändern',
                closeOnConfirm: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                type: 'question',
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        swal.disableButtons();
                        request = getAjaxRequest();
                        var url = "../api/api.php";
                        var params = 'request=' + encodeURIComponent(JSON.stringify({
                            adminpwd: {
                                old: document.getElementById('old_pwd').value,
                                pwd: document.getElementById('my_pwd').value,
                                pwd2: document.getElementById('my_pwd2').value,
                            },
                        }));
                        request.onreadystatechange=stateChangedSave;
                        request.open("POST",url,true);
                        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        request.send(params);
                        function stateChangedSave() {
                            if (request.readyState == 4) {
                                var response = JSON.parse(request.responseText);
                                if (response.adminpwd == "SUCCESS") {
                                    swal({
                                        title: "Passwort erfolgreich geändert!",
                                        type: "success",
                                    }).then(function() {
                                        window.location.reload();
                                    });
                                } else if (response.adminpwd == "ERR_PASSWORDS_DIFFERENT") {
                                    swal({
                                        title: "Die Passwörter stimmen nicht überein.",
                                        text: "Bitte eingaben überprüfen.",
                                        type: "warning",
                                    })
                                } else if (response.adminpwd == "ERR_UPDATE_FAILED") {
                                    swal({
                                        title: "Es ist ein Datenbankfehler aufgetreten.",
                                        text: "Bitte erneut versuchen.",
                                        type: "error",
                                    })
                                } else if (response.adminpwd == "ERR_PASSWORD_EMPTY") {
                                    swal({
                                        title: "Die Eingabefelder sind leer.",
                                        text: "Bitte gib ein neues Passwort ein.",
                                        type: "error",
                                    })
                                } else if (response.adminpwd == "ERR_OLD_INCORRECT") {
                                    swal({
                                        title: 'Das alte Passwort ist nicht korrekt.',
                                        text: 'Bitte erneut versuchen.',
                                        type: 'error',
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
