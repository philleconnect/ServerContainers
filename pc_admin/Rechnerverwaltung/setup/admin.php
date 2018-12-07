<?php
    $config = unserialize(file_get_contents('../config/config.txt'));
    if ($config['config'] == 'done') {
        header('Location: finish.php');
    }
    if ($_POST['isReady'] == 'true') {
        if ($_POST['password'] == $_POST['password2']) {
            include "../api/dbconnect.php";
            $request = "INSERT INTO userdata (username, password, type) VALUES ('".mysqli_real_escape_string($database, $_POST['user'])."', '".mysqli_real_escape_string($database, hash('sha512', $_POST['password']))."', 1)";
            $query = mysqli_query($database, $request);
            if ($query) {
                include "../api/accessConfig.php";
                changeConfigValue('globalPw', false, $_POST['globalpw']);
                header('Location: finish.php');
            } else {
                $case = 2;
            }
        } else {
            $case = 1;
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup - Admin - PhilleConnect Admin</title>
    <?php include "includes.php"; ?>
</head>
<body>
    <div role="navigation" id="foo" class="nav-collapse">
        <div class="top">
            <img src="../ui/ressources/img/logo.png">
            <li><b>PHILLE</b>CONNECT</li>
        </div>
        <ul>
            <li>
                <a href="#">Willkommen</a>
            </li>
            <li>
                <a href="#">Datenbank</a>
            </li>
            <li>
                <a href="#">LDAP-Server</a>
            </li>
            <li>
                <a href="#">IPFire</a>
            </li>
            <li class="active">
                <a href="#">Administratorkonto</a>
            </li>
            <li>
                <a href="#">Fertigstellen</a>
            </li>
        </ul>
    </div>
    <div role="main" class="main">
        <a href="#nav" class="nav-toggle">Menu</a>
        <noscript>
            <p>Dein Browser unterstützt kein JavaScript oder JavaScript ist ausgeschaltet. Du musst JavaScript aktivieren, um diese Seite zu verwenden!</p>
        </noscript>
        <p style="font-family: Arial, sans-serif; font-size: 45px;"><b>ADMIN</b>MODUL</p>
        <?php
            if ($_POST['isReady'] == 'true') {
                if ($case == 1) {
                    ?>
                    <p style="color: red;">Der Benutzer konnte nicht angelegt werden: Passwörter stimmen nicht überein.</p>
                    <?php
                } elseif ($case == 2) {
                    ?>
                    <p style="color: red;">Der Benutzer konnte nicht angelegt werden: Datenbankfehler.</p>
                    <?php
                }
            }
        ?>
        <form action="admin.php" method="post">
            <div class="datagrid">
                <table>
                    <thead>
                        <tr>
                            <th>Administratorkonto:</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Nutzername:</td>
                            <td><input type="text" placeholder="admin" name="user" required/></td>
                        </tr>
                        <tr class="alt">
                            <td>Passwort:</td>
                            <td><input type="password" name="password" id="pwd" oninput="verifyPasswords()" required/></td>
                        </tr>
                        <tr>
                            <td>Passwort wiederholen:</td>
                            <td><input type="password" name="password2" id="pwd2" oninput="verifyPasswords()" required/></td>
                        </tr>
                        <tr class="alt">
                            <td></td>
                            <td id="passwordCheck"></td>
                        </tr>
                        <tr>
                            <td>Installationskennung (globales Passwort):</td>
                            <td><input type="text" readonly name="globalpw" id="globalpw"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <input type="submit" value=">> Weiter"/>
            <input type="hidden" value="true" name="isReady"/>
        </form>
        <p>Dieser Schritt kann nicht übersprungen werden.</p>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        function verifyPasswords() {
            if (document.getElementById('pwd').value === document.getElementById('pwd2').value) {
                if (document.getElementById('pwd').value == '') {
                    document.getElementById('passwordCheck').innerHTML = '<p style="color: red;">Passwörter prüfen!</p>';
                } else {
                    document.getElementById('passwordCheck').innerHTML = '<p style="color: green;">Passwörter identisch.</p>';
                }
            } else {
                document.getElementById('passwordCheck').innerHTML = '<p style="color: red;">Passwörter prüfen!</p>';
            }
        }
        function random_string_generator(len) {
            var len = len || 10;
            var str = "";
            var i = 0;
            for(i=0; i<len; i++) {
                switch(Math.floor(Math.random()*3+1)) {
                    case 1: //digit
                        str +=(Math.floor(Math.random()*9)).toString();
                        break;
                    case 2: //small letter
                        str += String.fromCharCode(Math.floor(Math.random()*26) + 97);
                        break;
                    case 3: //big letter
                        str += String.fromCharCode(Math.floor(Math.random()*26) + 65);
                        break;
                    default:
                        break;
                }
            }
            return str;
        }
        document.getElementById('globalpw').value = random_string_generator(30);
        verifyPasswords();
    </script>
</body>
</html>
