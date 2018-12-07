<!DOCTYPE html>
<?php
    $page = 'Rechnerverwaltung';
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
    <title>Rechner bearbeiten - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; text-transform: uppercase;"><b>RECHNER</b>BEARBEITEN</p>
        <p><b>Achtung:</b> Bei Änderung des Raums auf korrekte Schreibweise achten! Es wird zwischen Groß- und Kleinschreibung unterschieden!</p>
        <?php
            $request = "SELECT room, machine, config_win, config_linux, teacher, inet, hardwareid FROM machines WHERE id = ".$_GET['id'];
            $query = mysqli_query($database, $request);
            $response = mysqli_fetch_assoc($query);
        ?>
        <div class="datagrid">
            <table>
                <thead>
                    <tr>
                        <th>Einstellung:</th>
                        <th>Parameter:</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Rechnername:</td>
                        <td><input type="text" id="name" value="<?php echo $response['machine']; ?>"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Raum:</td>
                        <td><input type="text" id="room" value="<?php echo $response['room']; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Konfigurationsprofil Windows:</td>
                        <td>
                            <select id="config-win">
                                <?php
                                    $request = "SELECT name FROM configs WHERE os = 'win'";
                                    $query = mysqli_query($database, $request);
                                    while ($result = mysqli_fetch_assoc($query)) {
                                        if ($result['name'] == $response['config_win']) {
                                            echo '<option value="'.$result['name'].'" selected>'.$result['name'].'</option>';
                                        } else {
                                            echo '<option value="'.$result['name'].'">'.$result['name'].'</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="alt">
                        <td>Konfigurationsprofil Linux:</td>
                        <td>
                            <select id="config-linux">
                                <?php
                                    $request = "SELECT name FROM configs WHERE os = 'linux'";
                                    $query = mysqli_query($database, $request);
                                    while ($result = mysqli_fetch_assoc($query)) {
                                        if ($result['name'] == $response['config_linux']) {
                                            echo '<option value="'.$result['name'].'" selected>'.$result['name'].'</option>';
                                        } else {
                                            echo '<option value="'.$result['name'].'">'.$result['name'].'</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Lehrer-PC</td>
                        <td><input type="checkbox" id="teacherpc"/></td>
                    </tr>
                    <tr class="alt">
                        <td>Internet bei Boot</td>
                        <td><input type="checkbox" id="inet"/></td>
                    </tr>
                    <tr>
                        <td>Aktion:</td>
                        <td><button onclick="goBack()">Abbrechen</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button onclick="saveMachine()">Speichern</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button onclick="deleteMachine()">Löschen</button></td>
                    <tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        <?php
            if ($response['teacher'] == '1') {
                echo "document.getElementById('teacherpc').checked = true;";
            } else {
                echo "document.getElementById('teacherpc').checked = false;";
            }
            if ($response['inet'] == '1') {
                echo "document.getElementById('inet').checked = true;";
            } else {
                echo "document.getElementById('inet').checked = false;";
            }
            include "link.php";
        ?>
        function goBack() {
            window.location.href = 'machines.php';
        }
        function getAjaxRequest() {
            var ajax = null;
            ajax = new XMLHttpRequest;
            return ajax;
        }
        function saveMachine() {
            if (document.getElementById('teacherpc').checked == true) {
                var teacherpc = '1';
            } else {
                var teacherpc = '0';
            }
            if (document.getElementById('inet').checked == true) {
                var inet = '1';
            } else {
                var inet = '0';
            }
            request = getAjaxRequest();
            var url = "../api/api.php";
            var params = 'request=' + encodeURIComponent(JSON.stringify({
                savemachine: {
                    room: document.getElementById("room").value,
                    machine: document.getElementById("name").value,
                    config_win: document.getElementById("config-win").value,
                    config_linux: document.getElementById("config-linux").value,
                    id: '<?php echo $_GET['id'] ?>',
                    inet: inet,
                    teacher: teacherpc,
                },
            }));
            request.onreadystatechange=stateChangedSave;
            request.open("POST",url,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(params);
            function stateChangedSave() {
                if (request.readyState == 4) {
                    var response = JSON.parse(request.responseText);
                    if (response.savemachine == "SUCCESS") {
                        swal({
                            title: "Änderungen erfolgreich gespeichert!",
                            type: "success",
                        }).then(function() {
                            window.location.href = 'machines.php';
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
        }
        function deleteMachine() {
            swal({
                title: 'Rechner löschen?',
                text: 'An nicht registrierten Rechnern ist keine Anmeldung möglich!',
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Löschen',
                cancelButtonText: 'Abbrechen',
                confirmButtonColor: '#D33',
                cancelButtonColor: "#3085d6",
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        request = getAjaxRequest();
                        var url = "../api/api.php";
                        var params = 'request=' + encodeURIComponent(JSON.stringify({
                            deletemachine: {
                                hardwareid: '<?php echo $response['hardwareid']; ?>',
                            },
                        }));
                        request.onreadystatechange=stateChangedSave;
                        request.open("POST",url,true);
                        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        request.send(params);
                        function stateChangedSave() {
                            if (request.readyState == 4) {
                                var response = JSON.parse(request.responseText);
                                if (response.deletemachine == "SUCCESS") {
                                    swal({
                                        title: "Rechner erfolgreich gelöscht!",
                                        type: "success",
                                    }).then(function() {
                                        window.location.href = 'machines.php';
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
                },
            });
		}
    </script>
</body>
</html>
