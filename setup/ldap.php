<?php
    $config = unserialize(file_get_contents('../config/config.txt'));
    if ($config['ldap']['finish'] == 'true') {
        header('Location: ipfire.php');
    }
    if ($_POST['isReady'] == 'true') {
        if (@ldap_connect($_POST['url'])) {
            $ldapconn = ldap_connect($_POST['url']);
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
            $r=ldap_bind($ldapconn);
            if (@ldap_bind($ldapconn, $_POST['admindn'].','.$_POST['basedn'], $_POST['password'])) {
                include "../api/accessConfig.php";
                changeConfigValue('ldap', 'url', $_POST['url']);
                changeConfigValue('ldap', 'password', $_POST['password']);
                changeConfigValue('ldap', 'basedn', $_POST['basedn']);
                changeConfigValue('ldap', 'admindn', $_POST['admindn']);
                changeConfigValue('ldap', 'usersdn', $_POST['usersdn']);
                changeConfigValue('ldap', 'groupsdn', $_POST['groupsdn']);
                changeConfigValue('ldap', 'studentscn', $_POST['studentscn']);
                changeConfigValue('ldap', 'teacherscn', $_POST['teacherscn']);
                changeConfigValue('ldap', 'sambahostname', $_POST['hostname']);
                changeConfigValue('ldap', 'finish', 'true');
                header('Location: ipfire.php');
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
    <title>Setup - LDAP - PhilleConnect Admin</title>
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
            <li class="active">
                <a href="#">LDAP-Server</a>
            </li>
            <li>
                <a href="#">IPFire</a>
            </li>
            <li>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px;"><b>LDAP</b>SERVER</p>
        <?php
            if ($_POST['isReady'] == 'true') {
                if ($case == 1) {
                    ?>
                    <p style="color: red;">Fehler beim Verbinden mit dem LDAP-Server.</p>
                    <br />
                    <p style="color: red;">Bitte korrigiere deine Eingaben und probiere es erneut.</p>
                    <?php
                } elseif ($case == 2) {
                    ?>
                    <p style="color: red;">Fehler beim Anmelden am LDAP-Server.</p>
                    <br />
                    <p style="color: red;">Bitte korrigiere deine Admin-Zugangsdaten und probiere es erneut.</p>
                    <?php
                }
            }
        ?>
        <form action="ldap.php" method="post">
            <div class="datagrid">
                <table>
                    <thead>
                        <tr>
                            <th>LDAP-Konfiguration:</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>URL:</td>
                            <td><input type="text" value="ldap_url" name="url"/></td>
                        </tr>
                        <tr class="alt">
                            <td>LDAP Admin-Passwort:</td>
                            <td><input type="text" value="ldap_password" name="password"/></td>
                        </tr>
                        <tr>
                            <td>Basis-DN:</td>
                            <td><input type="text" value="ldap_basedn" name="basedn"/></td>
                        </tr>
                        <tr class="alt">
                            <td>CN des Admin-Users:</td>
                            <td><input type="text" value="ldap_admindn" name="admindn"/></td>
                        </tr>
                        <tr>
                            <td>OU der Useraccounts:</td>
                            <td><input type="text" value="ou=users" name="usersdn"/></td>
                        </tr>
                        <tr class="alt">
                            <td>OU der Gruppen:</td>
                            <td><input type="text" value="ou=groups" name="groupsdn"/></td>
                        </tr>
                        <tr>
                            <td>CN der Schülergruppe:</td>
                            <td><input type="text" value="cn=students" name="studentscn"/></td>
                        </tr>
                        <tr class="alt">
                            <td>CN der Lehrergruppe:</td>
                            <td><input type="text" value="cn=teachers" name="teacherscn"/></td>
                        </tr>
                        <tr>
                            <td>Hostname des Samba-Servers:</td>
                            <td><input type="text" value="samba_hostname" name="hostname"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <input type="submit" value=">> Weiter"/>
            <input type="hidden" value="true" name="isReady"/>
        </form>
        <button onclick="window.location.href = 'ipfire.php'">Überspringen</button>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
    </script>
</body>
</html>
