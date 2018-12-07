<?php
    $config = unserialize(file_get_contents('../config/config.txt'));
    if ($config['ipfire']['finish'] == 'true') {
        header('Location: admin.php');
    }
    if ($_POST['isReady'] == 'true') {
        $connection = ssh2_connect($_POST['url'], $_POST['port']);
        if (ssh2_auth_password($connection, 'root', $_POST['rootpw'])) {
            if ($_POST['newpw1'] === $_POST['newpw2']) {
                if (ssh2_exec($connection, 'useradd philleconnect') != false) {
                    if (ssh2_exec($connection, 'echo philleconnect:'.$_POST['newpw1'].' | chpasswd') != false) {
                        if (ssh2_exec($connection, 'chmod =4755 /usr/local/bin/firewallctrl') != false) {
                            if (ssh2_exec($connection, 'chmod =666 /var/ipfire/fwhosts/customhosts') != false) {
                                if (ssh2_exec($connection, 'chmod =666 /var/ipfire/fwhosts/customgroups') != false) {
                                    if (ssh2_exec($connection, 'mkdir /home/philleconnect') != false) {
                                        if (ssh2_exec($connection, 'mkdir /home/philleconnect/.ssh') != false) {
                                            if (ssh2_exec($connection, 'chown -R philleconnect /home/philleconnect/') != false) {
                                                if (ssh2_scp_send($connection, 'config', '/var/ipfire/firewall/input', 0644)) {
                                                    $userconnection = ssh2_connect($_POST['url'], $_POST['port']);
                                                    if (ssh2_auth_password($userconnection, 'philleconnect', $_POST['newpw1'])) {
                                                        if (ssh2_scp_send($userconnection, '/var/www/html/config/id_rsa.pub', '/home/philleconnect/.ssh/authorized_keys')) {
                                                            if (ssh2_exec($userconnection, '/usr/local/bin/firewallctrl')) {
                                                                ssh2_exec($connection, 'exit');
                                                                ssh2_exec($userconnection, 'exit');
                                                                include "../api/accessConfig.php";
                                                                changeConfigValue('ipfire', 'url', $_POST['url']);
                                                                changeConfigValue('ipfire', 'port', $_POST['port']);
                                                                changeConfigValue('ipfire', 'password', $_POST['newpw1']);
                                                                changeConfigValue('ipfire', 'finish', 'true');
                                                                header('Location: admin.php');
                                                            } else {
                                                                $case = 13;
                                                            }
                                                        } else {
                                                            $case = 12;
                                                        }
                                                    } else {
                                                        $case = 11;
                                                    }
                                                } else {
                                                    $case = 14;
                                                }
                                            } else {
                                                $case = 10;
                                            }
                                        } else {
                                            $case = 9;
                                        }
                                    } else {
                                        $case = 8;
                                    }
                                } else {
                                    $case = 7;
                                }
                            } else {
                                $case = 6;
                            }
                        } else {
                            $case = 5;
                        }
                    } else {
                        $case = 4;
                    }
                } else {
                    $case = 3;
                }
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
    <title>Setup - IPFire - PhilleConnect Admin</title>
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
            <li class="active">
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
        <p style="font-family: Arial, sans-serif; font-size: 45px;"><b>IP</b>FIRE</p>
        <?php
            if ($_POST['isReady'] == 'true' && $case == 1) {
                ?>
                <p style="color: red;">Fehler beim Verbinden mit der IPFire: Bitte SSH-Zugriff, Port und Root-Passwort überprüfen.</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 2) {
                ?>
                <p style="color: red;">Fehler: Die Passwörter für den IPFire-Benutzer 'philleconnect' stimmen nicht überein.</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 3) {
                ?>
                <p style="color: red;">Fehler: Der IPFire-Benutzer 'philleconnect' konnte nicht hinzugefügt werden.</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 4) {
                ?>
                <p style="color: red;">Fehler: Dem IPFire-Benutzer 'philleconnect' konnte kein Passwort zugewiesen werden.</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 5) {
                ?>
                <p style="color: red;">Fehler beim Setzen der Dateirechte (/usr/local/bin/firewallctrl).</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 6) {
                ?>
                <p style="color: red;">Fehler beim Setzen der Dateirechte (/var/ipfire/fwhosts/customhosts).</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 7) {
                ?>
                <p style="color: red;">Fehler beim Setzen der Dateirechte (/var/ipfire/fwhosts/customgroups).</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 8) {
                ?>
                <p style="color: red;">Fehler beim Erstellen des Home-Ordners für den IPFire-Benutzer 'philleconnect'.</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 9) {
                ?>
                <p style="color: red;">Fehler beim Erstellen des Ordners '/home/philleconnect/.ssh'.</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 10) {
                ?>
                <p style="color: red;">Fehler beim Setzen der Dateirechte (/home/philleconnect/).</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 11) {
                ?>
                <p style="color: red;">Fehler beim Anmelden als 'philleconnect' auf der IPFire.</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 12) {
                ?>
                <p style="color: red;">Fehler beim Kopieren des SSH-Schlüssels.</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 13) {
                ?>
                <p style="color: red;">Fehler beim Testen der Verbindung zur IPFire: Test fehlgeschlagen.</p>
                <?php
            } elseif ($_POST['isReady'] == 'true' && $case == 14) {
                ?>
                <p style="color: red;">Fehler beim Übertragen der Blockierregeln auf die IPFire.</p>
                <?php
            }
        ?>
        <p>Um PhilleConnect mit einem IPFire-Server zu verbinden muss der SSH-Zugriff aktiviert werden. Dazu bitte auf der Weboberflächer der IPFire auf <b>System</b> -> <b>SSH-Zugriff</b> gehen, <b>Passwortbasierte Authentifizierung zulassen</b> aktivieren und auf <b>Speichern</b> klicken. <b>Achtung:</b> Bereits eingerichtete Firewall-Regeln werden überschrieben!</p>
        <img src="../ui/ressources/img/ipfire_ssh.png" style="max-width: 700px;"/>
        <form action="ipfire.php" method="post">
            <div class="datagrid">
                <table>
                    <thead>
                        <tr>
                            <th>IPFire-Zugangsdaten:</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>URL:</td>
                            <td><input type="text" placeholder="ipfire.local" name="url"/></td>
                        </tr>
                        <tr class="alt">
                            <td>Port (IPFire-Default: 222):</td>
                            <td><input type="text" placeholder="222" name="port"/></td>
                        </tr>
                        <tr>
                            <td>Passwort des IPFire-Root-Benutzers (wird nicht gespeichert):</td>
                            <td><input type="password" placeholder="streng geheim" name="rootpw"/></td>
                        </tr>
                        <tr class="alt">
                            <td>Gewünschtes Passwort des IPFire-Benutzers 'philleconnect':</td>
                            <td><input type="password" placeholder="geheim" name="newpw1"/></td>
                        </tr>
                        <tr>
                            <td>Passwort des IPFire-Benutzers 'philleconnect' wiederholen:</td>
                            <td><input type="password" placeholder="geheim" name="newpw2"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <input type="submit" value=">> Weiter"/>
            <input type="hidden" value="true" name="isReady"/>
        </form>
        <button onclick="window.location.href = 'admin.php'">Überspringen</button>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
    </script>
</body>
</html>
