<!DOCTYPE html>
<?php
    $page = "Jahrgangsübergang";
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
    <title>Jahrgangsübergang - PhilleConnect Admin</title>
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
        <p style="font-family: Arial, sans-serif; font-size: 45px; margin: 0;"><b>JAHRGANGS</b>ÜBERGANG</p>
        <div id="view-1" class="viewport-container">
            <p>Schritt 1: CSV-Datei einlesen<br />
            Anleitung:<br />
            1: Inhalt der CSV Datei in die Textbox kopieren.<br />
            2: CSV Datei konfigurieren. Dazu das verwendete Trennzeichen angeben und die Spaltennummern (1-X) der Daten angeben. Für nicht vorhandene Daten '-' eingeben.<br />
            3: Einträge importieren. <b>Erst fortfahren, wenn die Vorschau korrekt ist!</b></p>
            <div class="csv-input">
                <textarea id="csv" cols="50" rows="18" placeholder="Hier CSV-Text einfügen." oninput="csv.load()"></textarea>
            </div>
            <div class="datagrid">
                <table>
                    <thead>
                        <tr>
                            <th>Parameter:</th>
                            <th>CSV-Position:</th>
                            <th>Vorschau (erster CSV-Eintrag):</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CSV-Trennzeichen:</td>
                            <td><input type="text" value="," id="spacer" size="10" oninput="csv.preview()"/></td>
                            <td><p style="color: gray;">keine Vorschau</p></td>
                        </tr>
                        <tr class="alt">
                            <td>Nutzerame:</td>
                            <td>wird automatisch erzeugt</td>
                            <td id="cn-pre"></td>
                        </tr>
                        <tr>
                            <td>Vorname:</td>
                            <td><input type="text" value="-" id="givenname" size="10" oninput="csv.preview()"/></td>
                            <td id="givenname-pre"></td>
                        </tr>
                        <tr class="alt">
                            <td>Name:</td>
                            <td><input type="text" value="-" id="sn" size="10" oninput="csv.preview()"/></td>
                            <td id="sn-pre"></td>
                        </tr>
                        <tr>
                            <td>E-Mail:</td>
                            <td><input type="text" value="-" id="mail" size="10" oninput="csv.preview()"/></td>
                            <td id="mail-pre"></td>
                        </tr>
                        <tr class="alt">
                            <td>Klasse / Kürzel:</td>
                            <td><input type="text" value="-" id="class" size="10" oninput="csv.preview()"/></td>
                            <td id="class-pre"></td>
                        </tr>
                        <tr>
                            <td>Gruppe:</td>
                            <td><input type="radio" name="group" value="teachers" id="teachers" onclick="csv.preview()"/> Lehrer</td>
                            <td id="group-pre"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="radio" name="group" value="students" id="students" onclick="csv.preview()"/> Schüler</td>
                            <td></td>
                        </tr>
                        <tr class="alt">
                            <td>Home-Verzeichnis (nur Neu-Importe):</td>
                            <?php
                                if (is_writeable('/home/students') && is_writeable('/home/teachers')) {
                            ?>
                            <td><input type="checkbox" id="createhome" checked/>&nbsp;Home-Verzeichnisse erstellen</td>
                            <?php
                                } else {
                            ?>
                            <td><input type="checkbox" id="createhome" onclick="return false;"/>&nbsp;<s>Home-Verzeichnisse erstellen</s><br /><p style="color: red;">Keine Schreibrechte in /home/students und/oder /home/teachers!</p></td>
                            <?php
                                }
                            ?>
                            <td id="home-pre"></td>
                        </tr>
                        <tr>
                            <td>Geburtsdatum:</td>
                            <td><input type="text" value="-" id="gebdat" size="10" oninput="csv.preview()"/></td>
                            <td id="gebdat-pre"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <select id="one" oninput="csv.preview()">
                                    <option value="1" selected>DD</option>
                                    <option value="2">MM</option>
                                    <option value="3">YYYY</option>
                                </select>
                                <input type="text" value="." id="dspacer-1" size="2"/>
                                <select id="two" oninput="csv.preview()">
                                    <option value="1">DD</option>
                                    <option value="2" selected>MM</option>
                                    <option value="3">YYYY</option>
                                </select>
                                <input type="text" value="." id="dspacer-2" size="2"/>
                                <select id="three" oninput="csv.preview()">
                                    <option value="1">DD</option>
                                    <option value="2">MM</option>
                                    <option value="3" selected>YYYY</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr class="alt">
                            <td>Passwort (nur Neu-Importe):</td>
                            <td><input type="checkbox" id="password-checkbox" onclick="csv.preview()" checked/>&nbsp;Geburtsdatum als Passwort verwenden</td>
                            <td id="password-pre"></td>
                        </tr>
                        <tr class="alt">
                            <td></td>
                            <td><input type="text" value="-" id="password" size="10" oninput="csv.preview()" disabled/></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <table>
                <tr>
                    <td><button onclick="goBack()">Abbrechen</button></td>
                    <td><button onclick="readCSV()">Weiter</button></td>
                </tr>
            </table>
        </div>
        <div id="view-2"  class="viewport-container nodisplay">
            <p>Schritt 2: Berechnete Übereinstimmungen überprüfen.<br />
            Alle Nutzer werden in der Tabelle aufgelistet.<br />
            1: Bitte überprüfen Sie nochmals die Nutzerdaten.<br />
            2: Im Abschnitt 'Zwei Übereinstimmungen' müssen Sie entscheiden, ob es sich um den selben Nutzer handelt!<br />
            3: Übernehmen. Die Einträge werden dann auf den Server kopiert.</p>
            <br />
            <p>Legende:</p>
            <table>
                <tr>
                    <td><i class="f7-icons" style="color: gray;">time</i></td>
                    <td>Ausstehend</td>
                </tr>
                <tr>
                    <td><i class="f7-icons" style="color: green;">check_round</i></td>
                    <td>Aktion erfolgreich</td>
                </tr>
                <tr>
                    <td><i class="f7-icons" style="color: red;">bolt_round</i></td>
                    <td>Fehler im Account</td>
                </tr>
                <tr>
                    <td><i class="f7-icons" style="color: red;">persons_fill</i></td>
                    <td>Fehler im Zusammenhang mit der Gruppenzuordnung</td>
                </tr>
                <tr>
                    <td><i class="f7-icons" style="color: red;">folder_fill</i></td>
                    <td>Fehler im Zusammenhang mit dem Benutzerordner</td>
                </tr>
            </table>
            <div class="spoiler-control-container">
                <p class="spoiler-control-title">Drei Übereinstimmungen</p>
                <i class="f7-icons size-29 spoiler-control-checkbox" id="spoiler-three-matches">chevron_down</i>
            </div>
            <div class="spoiler spoiler-invisible" id="spoiler-three-matches-container">
                <div class="datagrid">
                    <table id="users">
                        <thead>
                            <tr>
                                <th>Status:</th>
                                <th>Name:</th>
                                <th>Vorname:</th>
                                <th>Geburtsdatum:</th>
                                <th>Klasse / Kürzel alt:</th>
                                <th>Klasse / Kürzel neu:</th>
                                <th>E-Mail alt:</th>
                                <th>E-Mail neu:</th>
                            </tr>
                        </thead>
                        <tbody id="three-matches-table-content"></tbody>
                    </table>
                </div>
            </div>
            <div class="spoiler-control-container">
                <p class="spoiler-control-title">Zwei Übereinstimmungen - Entscheidung erforderlich</p>
                <i class="f7-icons size-29 spoiler-control-checkbox" id="spoiler-two-matches">chevron_down</i>
            </div>
            <div class="spoiler spoiler-invisible" id="spoiler-two-matches-container">
                <div class="datagrid">
                    <table id="users">
                        <thead>
                            <tr>
                                <th>Status:</th>
                                <th>Name alt:</th>
                                <th>Name neu:</th>
                                <th>Vorname alt:</th>
                                <th>Vorname neu:</th>
                                <th>Geburtsdatum alt:</th>
                                <th>Geburtsdatum neu:</th>
                                <th>Klasse / Kürzel alt:</th>
                                <th>Klasse / Kürzel neu:</th>
                                <th>E-Mail alt:</th>
                                <th>E-Mail neu:</th>
                                <th>Aktion:</th>
                            </tr>
                        </thead>
                        <tbody id="two-matches-table-content"></tbody>
                    </table>
                </div>
            </div>
            <div class="spoiler-control-container">
                <p class="spoiler-control-title">Neue Accounts</p>
                <i class="f7-icons size-29 spoiler-control-checkbox" id="spoiler-new-accounts">chevron_down</i>
            </div>
            <div class="spoiler spoiler-invisible" id="spoiler-new-accounts-container">
                <div class="datagrid">
                    <table id="users">
                        <thead>
                            <tr>
                                <th>Status:</th>
                                <th>Name:</th>
                                <th>Vorname:</th>
                                <th>Nutzername:</th>
                                <th>Home-Laufwerk:</th>
                                <th>E-Mail:</th>
                                <th>Klasse / Kürzel:</th>
                                <th>Gruppe:</th>
                                <th>Geburtsdatum:</th>
                                <th>Passwort:</th>
                                <th>Aktion:</th>
                            </tr>
                        </thead>
                        <tbody id="new-accounts-table-content"></tbody>
                    </table>
                </div>
            </div>
            <div class="spoiler-control-container">
                <p class="spoiler-control-title">Alte Accounts (diese werden gelöscht)</p>
                <i class="f7-icons size-29 spoiler-control-checkbox" id="spoiler-old-accounts">chevron_down</i>
            </div>
            <div class="spoiler spoiler-invisible" id="spoiler-old-accounts-container">
                <div class="datagrid">
                    <table id="users">
                        <thead>
                            <tr>
                                <th>Status:</th>
                                <th>Name:</th>
                                <th>Vorname:</th>
                                <th>Nutzername:</th>
                                <th>Home-Laufwerk:</th>
                                <th>E-Mail:</th>
                                <th>Klasse / Kürzel:</th>
                                <th>Gruppe:</th>
                                <th>Geburtsdatum:</th>
                                <th>Aktion:</th>
                            </tr>
                        </thead>
                        <tbody id="old-accounts-table-content"></tbody>
                    </table>
                </div>
            </div>
            <table>
                <tr>
                    <td><button onclick="goBack()">Abbrechen</button></td>
                    <td><button onclick="doConvert()">Änderungen übernehmen</button></td>
                </tr>
            </table>
        </div>
    </div>
    <script>
        var navigation = responsiveNav("foo", {customToggle: ".nav-toggle"});
        var spoilerThreeMatches = new spoiler(document.getElementById('spoiler-three-matches'), document.getElementById('spoiler-three-matches-container'));
        var spoilerTwoMatches = new spoiler(document.getElementById('spoiler-two-matches'), document.getElementById('spoiler-two-matches-container'));
        var newAccounts = new spoiler(document.getElementById('spoiler-new-accounts'), document.getElementById('spoiler-new-accounts-container'));
        var oldAccounts = new spoiler(document.getElementById('spoiler-old-accounts'), document.getElementById('spoiler-old-accounts-container'));
        function goBack() {
            window.location.href = 'index.php';
        }
        function inputIsInteger(input) {
            var regex = /^-?[0-9]*[1-9][0-9]*$/;
            return regex.test(input);
        }
        function readCSV() {
            if (document.getElementById('students').checked || document.getElementById('teachers').checked) {
                if (inputIsInteger(document.getElementById('sn').value) && inputIsInteger(document.getElementById('givenname').value) && inputIsInteger(document.getElementById('class').value) && inputIsInteger(document.getElementById('gebdat').value)) {
                    if (!document.getElementById('password-checkbox').checked) {
                        if (inputIsInteger(document.getElementById('password').value)) {
                            preloader.toggle('LADEN');
                            csv.transit.isTransit = true;
                            csv.parse.toMultiArray();
                            csv.transit.load();
                            pauseClock();
                        } else {
                            swal({
                                title: 'CSV fertig konfigurieren',
                                text: 'Bitte geben Sie mindestens für die Felder "Name", "Vorname", "Klasse / Kürzel", "Geburtsdatum" und, falls nicht das Geburtsdatum als Passwort verwendet werden soll, "Passwort" eine Position in der CSV-Datei an.',
                                type: 'warning',
                            });
                        }
                    } else {
                        preloader.toggle('LADEN');
                        csv.transit.isTransit = true;
                        csv.parse.toMultiArray();
                        csv.transit.load();
                        pauseClock();
                    }
                } else {
                    swal({
                        title: 'CSV fertig konfigurieren',
                        text: 'Bitte geben Sie mindestens für die Felder "Name", "Vorname", "Klasse / Kürzel", "Geburtsdatum" und, falls nicht das Geburtsdatum als Passwort verwendet werden soll, "Passwort" eine Position in der CSV-Datei an.',
                        type: 'warning',
                    });
                }
            } else {
                swal({
                    title: 'Gruppe auswählen',
                    text: 'Bitte wähle eine Gruppe aus.',
                    type: 'warning',
                });
            }
        }
        function doConvert() {
            csv.transit.doConvert();
        }
    </script>
</body>
</html>
