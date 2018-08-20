<!DOCTYPE html>
<?php
    $page = "Integrität prüfen";
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
    <title>Benutzerintegrität prüfen - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; margin: 0;"><b>INTEGRITÄT</b>PRÜFEN</p>
        <p>Diese Seite prüft die folgenden Punkte:</p>
        <ul>
            <li>Schreibrechte im Homelaufwerk</li>
            <li>Zugehörigkeit zu einer Gruppe</li>
            <li>Doppelt vorhandene Nutzer (Name, Vorname und Geburtsdatum gleich - Problem für den Jahresübergang)</li>
        </ul>
        <p>Unstimmigkeiten werden in <span style="color:red;">rot</span> angezeigt und müssen bedarfsweise manuell behoben werden.</p>
        <div class="datagrid" style="overflow: auto;">
            <table id="users">
                <thead>
                    <tr>
                        <th>Nutzername</th>
                        <th>Name</th>
                        <th>Home-Laufwerk</th>
                        <th>Klasse</th>
                        <th>Geburtsdatum</th>
                        <th>Gruppe</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        include "../api/accessConfig.php";
                        $ldapconn = ldap_connect(loadConfig('ldap', 'url'));
                        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
                        $r = ldap_bind($ldapconn);
                        $allusers = ldap_search($ldapconn, loadConfig('ldap', 'usersdn').','.loadConfig('ldap', 'basedn'), "uid=*");
                        $users = ldap_get_entries($ldapconn, $allusers);
                        $teacherGroup = ldap_search($ldapconn, loadConfig('ldap', 'groupsdn').','.loadConfig('ldap', 'basedn'), loadConfig('ldap', 'teacherscn'));
                        $studentGroup = ldap_search($ldapconn, loadConfig('ldap', 'groupsdn').','.loadConfig('ldap', 'basedn'), loadConfig('ldap', 'studentscn'));
                        $teacherGroupcontent = ldap_get_entries($ldapconn, $teacherGroup);
                        $studentGroupcontent = ldap_get_entries($ldapconn, $studentGroup);
                        for ($i=0; $i<$users['count']; $i++) {
                            if ($users[$i]['cn'][0] != 'root' && $users[$i]['cn'][0] != 'nobody') {
                                if (in_array($users[$i]['cn'][0], $teacherGroupcontent[0]['memberuid'])) {
                                    $thisGroup = 'Lehrer';
                                } elseif (in_array($users[$i]['cn'][0], $studentGroupcontent[0]['memberuid'])) {
                                    $thisGroup = 'Schüler';
                                } else {
                                    $thisGroup = '<span style="color:red;">Keine Gruppe</span>';
                                }
                                if (is_writeable($users[$i]['homedirectory'][0])) {
                                    $thisHomedir = $users[$i]['homedirectory'][0];
                                } else {
                                    $thisHomedir = '<span style="color:red;">'.$users[$i]['homedirectory'][0].'</span>';
                                }
                                $thisUsercn = $users[$i]['cn'][0];
                                for ($j=$i+1; $j<$users['count']; $j++) {
                                    if (($users[$i]['sn'][0] == $users[$j]['sn'][0]) &&
                                            ($users[$i]['givenname'][0] == $users[$j]['givenname'][0]) &&
                                            ($users[$i]['description'][0] == $users[$i]['description'][0])) {
                                        $thisUsercn = '<span style="color:red;">'.$users[$i]['cn'][0].'<br />! ACHTUNG !<br /> Gleicher Nutzer (Vorname, Nachname, Geburtsdatum)<br />mit anderem Nutzernamen noch mal vorhanden!<br />(weiter unten, nicht noch mal markiert)</span>';
                                    }
                                }
                                if (($i % 2) == 0) {
                                    $start = '<tr>';
                                } else {
                                    $start = '<tr class="alt">';
                                }
                                echo $start.'<td>'.$thisUsercn.'</td>
										<td>'.$users[$i]['givenname'][0].' '.$users[$i]['sn'][0].'</td>
										<td>'.$thisHomedir.'</td>
                                        <td>'.$users[$i]['businesscategory'][0].'</td>
										<td>'.$users[$i]['description'][0].'</td>
										<td>'.$thisGroup.'</td>
										<td><a onclick="workOnUser(\''.$users[$i]['cn'][0].'\')">Bearbeiten</a></td>
										</tr>';
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function workOnUser(username) {
            window.location.href = "changeaccount.php?user="+username;
        }
    </script>
</body>
</html>
