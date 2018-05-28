<?php
    $menu = "";
    $entrys = [["Accounts","index.php",[["Nutzer hinzufügen","newuser.php"],["CSV importieren","csvimport.php"],["Jahrgangsübergang","convert.php"]]],["Konfigurationsprofile","config.php"],["Rechnerverwaltung","machines.php"],["Aktivitätsprotokoll","log.php"],["Clientinstallation","installclient.php"],["Gruppenlaufwerke","groupfolders.php"],["Grundkonfiguration","maincfg.php",[["Admin-Passwort ändern","changeadminpasswd.php"],["Admin-Accounts","adminaccounts.php"]]],["Logout","logout.php"]];
    for ($i = 0; $i < sizeof($entrys); $i++) {
        if ($page == $entrys[$i][0]) {
            $menu = $menu.'<li class="active"><a href="'.$entrys[$i][1].'">'.$entrys[$i][0].'</a></li>';
        } else {
            $menu = $menu.'<li><a href="'.$entrys[$i][1].'">'.$entrys[$i][0].'</a></li>';
        }
        if (sizeof($entrys[$i]) > 2) {
            for ($d = 0; $d < sizeof($entrys[$i][2]); $d++) {
                if ($page == $entrys[$i][2][$d][0]) {
                    $menu = $menu.'<li class="active"><a href="'.$entrys[$i][2][$d][1].'" class="subnav">'.$entrys[$i][2][$d][0].'</a></li>';
                } else {
                    $menu = $menu.'<li><a href="'.$entrys[$i][2][$d][1].'" class="subnav">'.$entrys[$i][2][$d][0].'</a></li>';
                }
            }
        }
    }
?>
