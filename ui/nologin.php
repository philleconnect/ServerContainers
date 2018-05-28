<!DOCTYPE html>
<?php
    $page = "nologin";
    include "../api/accessConfig.php";
    include "../api/versioncode.php";
    if (loadConfig('config', null) != 'done') {
        header('Location: ../setup/index.php');
    } elseif (loadConfig('versioncode', null) < $versioncode || loadConfig('versioncode', null) == null) {
        header('Location: postupdate.php');
    } else {
        include "../api/dbconnect.php";
        include "menue.php";
    }
?>
<html lang="de">
<head>
    <title>Login - PhilleConnect Admin</title>
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
    </div>
    <div role="main" class="main">
        <a href="#nav" class="nav-toggle">Menu</a>
        <noscript>
            <p>Dein Browser unterstützt kein JavaScript oder JavaScript ist ausgeschaltet. Du musst JavaScript aktivieren, um diese Seite zu verwenden!</p>
        </noscript>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        function getAjaxRequest() {
            var ajax = null;
            ajax = new XMLHttpRequest;
            return ajax;
        }
        function showLoginModal() {
            swal({
                title: 'Bitte Nutzerdaten eingeben.',
                html: '<input type="text" id="swal-input1" placeholder="Nutzername" class="swal2-input"/><input type="password" id="swal-input2" placeholder="Passwort" class="swal2-input"/>',
                showCancelButton: false,
                confirmButtonText: 'Anmelden',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                preConfirm: function() {
                    return new Promise(function (resolve) {
                        var url = '../api/api.php';
                        var params = 'request=' + encodeURIComponent(JSON.stringify({
                            login: {
                                uname: $('#swal-input1').val(),
                                passwd: $('#swal-input2').val(),
                            },
                        }));
                        ajax = getAjaxRequest();
                        ajax.onreadystatechange=stateChangedLogin;
                        ajax.open("POST",url,true);
                        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        ajax.send(params);
                        function stateChangedLogin() {
                            if (ajax.readyState == 4) {
                                var response = JSON.parse(ajax.responseText);
                                if (response.login == 'SUCCESS') {
                                    if (response.type == '3') {
                                        window.location.href = 'machines.php';
                                    } else {
                                        window.location.href = 'index.php';
                                    }
                                } else if (response.login == 'ERR_WRONG_CREDENTIALS') {
                                    swal.showValidationError('Das Passwort ist falsch.');
                                    resolve();
                                } else {
                                    swal({
                                        title: 'Es ist ein Fehler aufgetreten.',
                                        showCancelButton: false,
                                        closeOnConfirm: false,
                                        type: 'error'
                                    }).then(function() {
                                        showLoginModal();
                                    });
                                }
                            }
                        }
                    })
                }
            });
        }
        showLoginModal();
    </script>
</body>
</html>
