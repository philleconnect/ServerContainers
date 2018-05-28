<!DOCTYPE html>
<?php
    $page = "Konfigurationsprofile";
    include "../api/dbconnect.php";
    session_start();
    if ($_SESSION['user'] == null || $_SESSION['user'] == '' || ($_SESSION['timeout'] + 1200) < time()) {
        header("Location: nologin.php");
    } elseif ($_SESSION['type'] != '1' && $_SESSION['type'] != '3') {
        header("Location: restricted.php");
    } else {
        $_SESSION['timeout'] = time();
        include "menue.php";
    }
?>
<html lang="de">
<head>
    <title>Konfigurationsprofile - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px;"><b>KONFIGURATIONS</b>PROFILE</p>
        <br />
        <p>Hier lassen sich für verschiedene Rechnertypen verschiedene Konfigurationsprofile anlegen. Jedem Rechner wird eine Konfiguration pro Betriebssystem zugewiesen.</p>
        <br />
        <table>
            <tr>
                <td>Konfigurationsprofil anlegen:</td>
                <td>
                    <select id="fill">
                        <option value="0">Keine Vorlage</option>
                        <?php
                            $request = "SELECT * FROM configs";
                            $query = mysqli_query($database, $request);
                            while ($result = mysqli_fetch_assoc($query)) {
                                echo '<option value="'.$result['id'].'">'.$result['name'].'</option>';
                            }
                        ?>
                    </select>
                </td>
                <td><button onclick="newConfig()">Anlegen</button></td>
            </tr>
        </table>
        <br />
        <p>Derzeit vorhandene Konfigurationsprofile:</p>
        <div class="datagrid" style="overflow: auto;">
            <table id="configs">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>OS</th>
                        <th>Samba-URL</th>
                        <th>Laufwerke</th>
                        <th>Gruppenlaufwerke</th>
                        <th>Herunterfahren nach</th>
                        <th>Infotexte</th>
                        <th>Servicemode</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $request = "SELECT * FROM configs";
                        $query = mysqli_query($database, $request);
                        $i = 0;
                        while ($result = mysqli_fetch_assoc($query)) {
                            if ($i == 0) {
                                ?>
                    <tr>
                                <?php
                                $i = 1;
                            } else {
                                ?>
                    <tr class="alt">
                                <?php
                                $i = 0;
                            }
                            $drives = $result['driveone'].' ('.$result['pathone'].'); '.$result['drivetwo'].' ('.$result['pathtwo'].'); '.$result['drivethree'].' ('.$result['paththree'].')';
                            $decodedData = json_decode($result['groupfolders']);
                            $c = 0;
                            $groupfolders = '';
                            $k = 0;
                            while ($decodedData[$c] != null) {
                                $namerequest = "SELECT name FROM groupfolders WHERE id = ".mysqli_real_escape_string($database, $decodedData[$c][1]);
                                $namequery = mysqli_query($database, $namerequest);
                                $nameresult = mysqli_fetch_assoc($namequery);
                                if ($k == 0) {
                                    $groupfolders = $groupfolders."<tr><td>".$decodedData[$c][0]."</td><td>".$nameresult['name']."</td></tr>";
                                    $k = 1;
                                } else {
                                    $groupfolders = $groupfolders."<tr class=&quot;alt&quot;><td>".$decodedData[$c][0]."</td><td>".$nameresult['name']."</td></tr>";
                                    $k = 0;
                                }
                                $c++;
                            }
                            ?>
                        <td><?php echo $result["name"]; ?></td>
                        <td><?php echo $result["os"]; ?></td>
                        <td><?php echo $result["smbserver"]; ?></td>
                        <td><?php echo $drives; ?></td>
                        <td><a href="#" onclick="showGroupFolders('<?php echo $result["name"]; ?>', '<div class=&quot;datagrid&quot;><table><thead><tr><th>Mountpfad</th><th>Laufwerk</th></tr></thead><tbody><?php echo $groupfolders; ?></tbody></table></div>')">Zeigen</a></td>
                        <td><?php echo $result["shutdown"]; ?></td>
                        <td><a href="#" onclick="showInfos('<?php echo $result["name"]; ?>', '<div class=&quot;datagrid&quot;><table><thead><tr><th>Text</th><th>Inhalt</th></tr></thead><tbody><tr><td>Bitte anmelden:</td><td><?php echo $result['dologin']; ?></td></tr><tr class=&quot;alt&quot;><td>Login läuft:</td><td><?php echo $result['loginpending']; ?></td></tr><tr><td>Login fehlgeschlagen:</td><td><?php echo $result['loginfailed']; ?></td></tr><tr class=&quot;alt&quot;><td>Nutzerdaten falsch:</td><td><?php echo $result['wrongcredentials']; ?></td></tr><tr><td>Netzwerkfehler:</td><td><?php echo $result['networkfailed']; ?></td></tr><tr class=&quot;alt&quot;><td>Erfolg:</td><td><?php echo $result['success']; ?></td></tr><tr><td>Hinweisbox:</td><td><?php echo preg_replace('/(?:[ \t]*(?:\n|\r\n?)){2,}/', "<br />", str_replace("\n", "<br />", $result['infotext'])); ?></td></tr></tbody></table></div>')">Zeigen</a></td>
                        <?php if ($result["servicemode"] == '1') { ?>
                            <td><input type="checkbox" checked onchange="saveCheckbox('<?php echo $result["id"]; ?>')" id="servicemode_<?php echo $result["id"]; ?>"/><p style="display: none;">Ja</p></td>
                        <?php } else { ?>
                            <td><input type="checkbox" onchange="saveCheckbox('<?php echo $result["id"]; ?>')" id="servicemode_<?php echo $result["id"]; ?>"/><p style="display: none;">Nein</p></td>
                        <?php } ?>
                        <td><a href="#" onclick="changeConfig(<?php echo $result["id"]; ?>)">Bearbeiten</a></td>
                            <?php
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        var table_Props = {
            col_1: "select",
            col_2: "select",
            col_4: "none",
            col_5: "select",
            col_6: "none",
            col_7: "select",
            display_all_text: "Alle anzeigen",
            sort_select: true
        };
        var tf2 = setFilterGrid("configs", table_Props);
        function newConfig() {
            window.location.href = "changeconfig.php?mode=new&id="+document.getElementById("fill").value;
        }
        function changeConfig(id) {
            window.location.href = "changeconfig.php?mode=change&id="+id;
        }
        function showGroupFolders(name, content) {
            swal({
                title: 'Gruppenlaufwerke von ' + name,
                html: content,
            })
        }
        function showInfos(name, content) {
            swal({
                title: 'Infotexte von ' + name,
                html: content,
            })
        }
        function getAjaxRequest() {
            var ajax = null;
            ajax = new XMLHttpRequest;
            return ajax;
        }
        function saveCheckbox(id) {
            preloader.toggle();
            request = getAjaxRequest();
            var url = "../api/api.php";
            if (document.getElementById('servicemode_'+id).checked) {
                var servicemode = '1';
            } else {
                var servicemode = '0';
            }
            var params = 'request=' + encodeURIComponent(JSON.stringify({
                saveconfig: {
                    servicemode: servicemode,
                    id: id,
                    mode: 'change',
                },
            }));
            request.onreadystatechange=stateChangedSave;
            request.open("POST",url,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(params);
            function stateChangedSave() {
                if (request.readyState == 4) {
                    var response = JSON.parse(request.responseText);
                    if (response.saveconfig == "SUCCESS") {
                        preloader.toggle();
                    } else {
                        preloader.toggle();
                        document.getElementById('servicemode_'+id).checked = !document.getElementById('servicemode_'+id).checked;
                        swal({
                            title: "Es ist ein Fehler aufgetreten.",
                            text: "Bitte erneut versuchen.",
                            type: "error",
                        })
                    }
                }
            }
        }
    </script>
</body>
</html>
