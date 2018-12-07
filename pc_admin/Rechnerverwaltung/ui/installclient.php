<!DOCTYPE html>
<?php
    $page = 'Clientinstallation';
    include "../api/dbconnect.php";
    session_start();
    if ($_SESSION['user'] == null || $_SESSION['user'] == '' || ($_SESSION['timeout'] + 1200) < time()) {
        header("Location: nologin.php");
    } elseif ($_SESSION['type'] != '1' && $_SESSION['type'] != '3') {
        header("Location: restricted.php");
    } else {
        $_SESSION['timeout'] = time();
        include "menue.php";
        include "../api/accessConfig.php";
    }
?>
<html lang="de">
<head>
    <title>Clientinstallation - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>CLIENT</b>INSTALLATION</p>
        <p>Führe folgende Schritte aus um PhilleConnect auf einem Clientcomputer zu installieren:</p>
        <p>1. Installationsprogramm herunterladen.</p>
        <p>2. Konfigurationsdatei erstellen und im selben Ordner wie das Installationsprogramm ablegen.</p>
        <p>3. PhilleConnect installieren.</p>
        <br />
        <p>Installationsprogramm herunterladen:</p>
        <div class="datagrid" style="overflow: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Windows</th>
                        <th>Linux</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><a href="https://github.com/philleconnect/ClientSetup-Windows/releases">https://github.com/philleconnect/ClientSetup-Windows/releases</a></td>
                        <td><a href="https://github.com/philleconnect/ClientSetup-Linux/releases">https://github.com/philleconnect/ClientSetup-Linux/releases</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>Konfigurationsdatei erzeugen:</p>
        <div class="datagrid" style="overflow: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Parameter:</th>
                        <th>Wert:</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Server-URL (ohne https://):</td>
                        <td><input type="text" id="server" value="<?php echo file_get_contents('../../host.txt'); ?>:447"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Globales Passwort:</td>
                        <td><?php echo loadConfig('globalPw', null); ?></td>
                    </tr>
                    <tr>
                        <td>Rechner offline freigeben:</td>
                        <td><input type="checkbox" checked id="allowOffline"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Verbindungsversuche bei fehlerhafter Netzwerkverbindung:</td>
                        <td><input type="text" value="30" id="badNetworkReconnect"/></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button onclick="createConfig()">Konfigurationsdatei erstellen</button>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        function createConfig() {
            if (document.getElementById('allowOffline').value) {
                var allowOffline = '1';
            } else {
                var allowOffline = '0';
            }
            var text = '#Konfigurationsdatei für PhilleConnect Drive\n#Syntax: key=value -> KEINE LEERZEICHEN!\n#Konfigurationsvariablen:\n#server: Vollständige URL zum PhilleConnect Webserver\n#global: Globales Passwort\n#allowOffline: Rechner ohne Serververbindung freigeben (1) oder herunterfahren (0)\n#badNetworkReconnect: Anzahl der Verbindungsversuche (1 pro Sekunde).\n#automatically created by PhilleConnect Admin backend\nserver='+document.getElementById('server').value+'\nglobal=<?php echo loadConfig('globalPw', null); ?>\nallowOffline='+allowOffline+'\nbadNetworkReconnect='+document.getElementById('badNetworkReconnect').value;
            var element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
            element.setAttribute('download', 'pcconfig.jkm');
            element.style.display = 'none';
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        }
    </script>
</body>
</html>
