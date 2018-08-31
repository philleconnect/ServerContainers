/*
 CSV JavaScript Engine for PhilleConnect Admin Backend
 Written by Johannes Kreutz
 */
function getAjaxRequest() {
    var ajax = null;
    ajax = new XMLHttpRequest;
    return ajax;
}
var csv = {
    //Array with the CSV text, one line for each object
    inputArray: [],
    /*Multidimensional array, configured CSV:
    [
        [
            'surname', 'first name', 'username', 'homefolder', 'mail', 'class', 'group', 'birthday', 'password'
        ],
        [
            'surname', 'first name', ...
        ]
    ]
    */
    multiArray: [],
    //Creates a preview of the CSV configuration
    preview: function() {
        if (document.getElementById('password-checkbox').checked) {
            document.getElementById('password').disabled = true;
        } else {
            document.getElementById('password').disabled = false;
        }
        var cache = this.inputArray[0].split(document.getElementById('spacer').value);
        if (document.getElementById('teachers').checked) {
            var group = 'Lehrer';
        } else if (document.getElementById('students').checked) {
            var group = 'Schüler';
        }
        var cn = cache[(parseInt(document.getElementById('givenname').value) - 1)] + '.' + cache[(parseInt(document.getElementById('sn').value) - 1)];
        cn = this.parse.umlaute(cn);
        cn = removeDiacritics(cn);
        document.getElementById('cn-pre').innerHTML = cn;
        document.getElementById('givenname-pre').innerHTML = cache[(parseInt(document.getElementById('givenname').value) - 1)] || '';
        document.getElementById('sn-pre').innerHTML = cache[(parseInt(document.getElementById('sn').value) - 1)] || '';
        if (document.getElementById('teachers').checked) {
            document.getElementById('home-pre').innerHTML = '/home/teachers/' + cn;
        } else {
            document.getElementById('home-pre').innerHTML = '/home/students/' + cn;
        }
        document.getElementById('group-pre').innerHTML = group || '';
        document.getElementById('class-pre').innerHTML = cache[(parseInt(document.getElementById('class').value) - 1)] || '';
        document.getElementById('mail-pre').innerHTML = cache[(parseInt(document.getElementById('mail').value) - 1)] || '';
        document.getElementById('gebdat-pre').innerHTML = this.parse.convertDate(cache[(parseInt(document.getElementById('gebdat').value) - 1)], false) || '';
        if (document.getElementById('password-checkbox').checked) {
            document.getElementById('password-pre').innerHTML = this.parse.convertDate(cache[(parseInt(document.getElementById('gebdat').value) - 1)], true) || '';
        } else {
            document.getElementById('password-pre').innerHTML = cache[(parseInt(document.getElementById('password').value) - 1)] || '';
        }
    },
    //General parsing functions
    parse: {
        //Converts the configured date to the wished format
        convertDate: function(input, passwd) {
            if (document.getElementById('dspacer-1').value == document.getElementById('dspacer-2').value) {
                var inputDateOne = input.split(document.getElementById('dspacer-1').value);
                if (document.getElementById('two').value == '1') {
                    var day = inputDateOne[1];
                } else if (document.getElementById('two').value == '2') {
                    var month = inputDateOne[1];
                } else if (document.getElementById('two').value == '3') {
                    var year = inputDateOne[1];
                }
                if (document.getElementById('three').value == '1') {
                    var day = inputDateOne[2];
                } else if (document.getElementById('three').value == '2') {
                    var month = inputDateOne[2];
                } else if (document.getElementById('three').value == '3') {
                    var year = inputDateOne[2];
                }
            } else {
                var inputDateOne = input.split(document.getElementById('dspacer-1').value);
                var inputDateTwo = inputDateOne[1].split(document.getElementById('dspacer-2').value);
                if (document.getElementById('two').value == '1') {
                    var day = inputDateTwo[0];
                } else if (document.getElementById('two').value == '2') {
                    var month = inputDateTwo[0];
                } else if (document.getElementById('two').value == '3') {
                    var year = inputDateTwo[0];
                }
                if (document.getElementById('three').value == '1') {
                    var day = inputDateTwo[1];
                } else if (document.getElementById('three').value == '2') {
                    var month = inputDateTwo[1];
                } else if (document.getElementById('three').value == '3') {
                    var year = inputDateTwo[1];
                }
            }
            if (document.getElementById('one').value == '1') {
                var day = inputDateOne[0];
            } else if (document.getElementById('one').value == '2') {
                var month = inputDateOne[0];
            } else if (document.getElementById('one').value == '3') {
                var year = inputDateOne[0];
            }
            if (day.length == 1) {
                var zeroedDay = '0' + day;
            } else {
                var zeroedDay = day;
            }
            if (month.length == 1) {
                var zeroedMonth = '0' + month;
            } else {
                var zeroedMonth = month;
            }
            if (passwd) {
				        return zeroedDay + '' + zeroedMonth + '' + year;
			      } else {
				        return zeroedDay + '.' + zeroedMonth + '.' + year;
			      }
        },
        //Converts Umlaute to normal characters
        umlaute: function(input) {
            return input.replace(/ /g, '_').toLowerCase().replace(/ü/g, 'ue').replace(/ö/g, 'oe').replace(/ä/g, 'ae').replace(/ß/g, 'ss')
        },
        //Creates the multidimensional array after the CSV configuration
        toMultiArray: function() {
            for (var i = 0; i < csv.inputArray.length; i++) {
                if (csv.inputArray[i] != '') {
                    var cache = csv.inputArray[i].split(document.getElementById('spacer').value);
                    var thisLine = [];
                    thisLine[0] = cache[(parseInt(document.getElementById('sn').value) - 1)] || '';
                    thisLine[1] = cache[(parseInt(document.getElementById('givenname').value) - 1)] || '';
                    var cn = cache[(parseInt(document.getElementById('givenname').value) - 1)] + '.' + cache[(parseInt(document.getElementById('sn').value) - 1)];
                    cn = this.umlaute(cn);
                    cn = removeDiacritics(cn);
                    thisLine[2] = cn;
                    if (document.getElementById('teachers').checked) {
                        thisLine[3] = '/home/teachers/' + cn;
                    } else {
                        thisLine[3] = '/home/students/' + cn;
                    }
                    thisLine[4] = cache[(parseInt(document.getElementById('mail').value) - 1)] || '';
                    thisLine[5] = cache[(parseInt(document.getElementById('class').value) - 1)] || '';
                    if (document.getElementById('teachers').checked) {
                        thisLine[6] = 'teachers';
                    } else if (document.getElementById('students').checked) {
                        thisLine[6] = 'students';
                    }
                    thisLine[7] = this.convertDate(cache[(parseInt(document.getElementById('gebdat').value) - 1)], false) || '';
                    if (document.getElementById('password-checkbox').checked) {
                        thisLine[8] = this.convertDate(cache[(parseInt(document.getElementById('gebdat').value) - 1)], true) || '';
                    } else {
                        thisLine[8] = cache[(parseInt(document.getElementById('password').value) - 1)] || '';
                    }
                    csv.multiArray.push(thisLine);
                }
            }
        },
        //Creates a editable table with all users
        createTable: function(target) {
            var table = '';
            for (var c = 0; c < csv.multiArray.length; c++) {
                if ((c%2) == 0) {
                    var cache = '<tr>';
                } else {
                    var cache = '<tr class="alt">';
                }
                for (var d = -1; d < 9; d++) {
                    if (d == 6) {
                        if (csv.multiArray[c][d] == 'teachers') {
                            cache += '<td>Lehrer</td>';
                        } else if (csv.multiArray[c][d] == 'students') {
                            cache += '<td>Schüler</td>';
                        }
                    } else if (d == 2 || d == 3) {
                        cache += '<td>'+csv.multiArray[c][d]+'</td>';
                    } else if (d == -1) {
                        if (csv.multiArray[c][9] == 'SUCCESS') {
                            cache += '<td id="icon-'+c+'"><i class="f7-icons" style="color: green;">check_round</i></td>';
                        } else if (csv.multiArray[c][9] == 'BOLT') {
                            cache += '<td id="icon-'+c+'"><i class="f7-icons" style="color: red;">bolt_round</i></td>';
                        } else if (csv.multiArray[c][9] == 'PERSONS') {
                            cache += '<td id="icon-'+c+'"><i class="f7-icons" style="color: red;">persons_fill</i></td>';
                        } else if (csv.multiArray[c][9] == 'FOLDER') {
                            cache += '<td id="icon-'+c+'"><i class="f7-icons" style="color: red;">folder_fill</i></td>';
                        } else {
                            cache += '<td id="icon-'+c+'"><i class="f7-icons" style="color: gray;">time</i></td>';
                        }
                    } else {
                        cache += '<td id="'+c+'-'+d+'"><p onclick="csv.inline.change('+c+', '+d+', \''+target+'\')">'+csv.multiArray[c][d]+'</p></td>';
                    }
                }
                cache += '</tr>';
                table += cache;
            }
            document.getElementById(target).innerHTML = table;
        },
    },
    //Inline editing functions
    inline: {
        //Creates edit box
        change: function(c, d, target) {
            document.getElementById(c+'-'+d).innerHTML = '<input id="'+c+'.'+d+'" type="text" value="'+csv.multiArray[c][d]+'" onchange="csv.inline.save('+c+', '+d+')" onblur="csv.parse.createTable(\''+target+'\')"/>';
        },
        //Saves changed value
        save: function(c, d) {
            csv.multiArray[c][d] = document.getElementById(c+'.'+d).value;
            var cn = csv.multiArray[c][1] + '.' + csv.multiArray[c][0];
            cn = csv.parse.umlaute(cn);
            cn = removeDiacritics(cn);
            csv.multiArray[c][2] = cn;
            if (csv.multiArray[c][6] == 'teachers') {
                csv.multiArray[c][3] = '/home/teachers/' + cn;
            } else if (csv.multiArray[c][6] == 'students') {
                csv.multiArray[c][3] = '/home/students/' + cn;
            }
        }
    },
    //Import (add user) functions
    import: {
        request: null,
        counter: 0,
        error: 0,
        callback: null,
        createHomeFolder: false,
        //Import the multidimensional array
        doMulti: function(home) {
            swal({
                title: 'CSV importieren?',
                showCancelButton: true,
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Importieren",
                cancelButtonText: "Abbrechen",
                allowOutsideClick: false,
                allowEscapeKey: false,
                type: 'question',
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        csv.import.callback = csv.import.multiCallback;
                        csv.import.counter = 0;
                        csv.import.error = 0;
                        csv.import.createHomeFolder = home;
                        csv.import.send(csv.multiArray[csv.import.counter]);
                    })
                }
            })
        },
        //Callback
        multiCallback: function(result) {
            var response = JSON.parse(result);
            var harderror = false;
            if (response.addaccount == "SUCCESS") {
                document.getElementById('icon-'+this.counter).innerHTML = '<i class="f7-icons" style="color: green;">check_round</i>';
                csv.multiArray[this.counter][9] = 'SUCCESS';
            } else if (response.addaccount == "ERR_ADD_OBJECT") {
                this.error++;
                document.getElementById('icon-'+this.counter).innerHTML = '<i class="f7-icons" style="color: red;">bolt_round</i>';
                csv.multiArray[this.counter][9] = 'BOLT';
            } else if (response.addaccount == "ERR_USER_EXISTS") {
                this.error++;
                document.getElementById('icon-'+this.counter).innerHTML = '<i class="f7-icons" style="color: red;">bolt_round</i>';
                csv.multiArray[this.counter][9] = 'BOLT';
            } else if (response.addaccount == "ERR_ADD_TO_GROUP") {
                this.error++;
                document.getElementById('icon-'+this.counter).innerHTML = '<i class="f7-icons" style="color: red;">persons_fill</i>';
                csv.multiArray[this.counter][9] = 'PERSONS';
                importCounter++;
            } else if (response.addaccount == "ERR_UPDATE_UID") {
                this.error++;
                this.counter++;
                harderror = true;
                document.getElementById('icon-'+this.counter).innerHTML = '<i class="f7-icons" style="color: red;">bolt_round</i>';
                csv.multiArray[this.counter][9] = 'BOLT';
                swal({
                    title: "Es ist ein schwerwiegender Fehler aufgetreten.",
                    text: "WARNUNG: " + this.counter + " Nutzer wurde hinzugefügt, jedoch konnte die User-ID nicht erhöht werden. Dies wird zu Sicherheitslücken führen, sollten Sie einen weiteren Nutzer hinzufügen! Der CSV-Import wurde abgebrochen.",
                    type: "error",
                })
            } else if (response.addaccount == "ERR_CREATE_HOME" || response.addaccount == "ERR_HOME_GROUP" || response.addaccount == "ERR_HOME_USER") {
                this.error++;
                document.getElementById('icon-'+this.counter).innerHTML = '<i class="f7-icons" style="color: red;">folder_fill</i>';
                csv.multiArray[this.counter][9] = 'FOLDER';
            } else {
                this.error++;
                document.getElementById('icon-'+this.counter).innerHTML = '<i class="f7-icons" style="color: red;">bolt_round</i>';
                csv.multiArray[this.counter][9] = 'BOLT';
            }
            if (!harderror) {
                if (!csv.transit.isTransit) {
                    if (this.counter < (csv.multiArray.length - 1)) {
                        this.counter++;
                        this.send(csv.multiArray[this.counter]);
                    } else {
                        if (this.error <= 0) {
                            swal({
                                title: 'Import erfolgreich.',
                                text: 'Alle Nutzer wurden erfolgreich importiert.',
                                type: 'success',
                            }).then(function() {
                                window.location.href = 'index.php';
                            });
                        } else {
                            swal({
                                title: 'Achtung.',
                                text: 'Es sind bei '+this.error+' Nutzern Fehler aufgetreten. Bitte überprüfen Sie diese Fehler, bevor Sie fortfahren.',
                                type: 'error',
                            });
                        }
                    }
                } else {
                    if (this.counter < (csv.transit.newData.new.length - 1)) {
                        this.counter++;
                        this.send(csv.transit.newData.new[this.counter]);
                    } else {
                        csv.transit.taskFinishCallback();
                    }
                }
            }
        },
        //Send request
        send: function(data) {
            if (data == null || data.length < 9 || data[9] == 'SUCCESS') {
                if (this.counter < (csv.multiArray.length - 1)) {
                    this.counter++;
                    this.send(csv.multiArray[this.counter]);
                } else {
                    if (!csv.transit.isTransit) {
                        if (this.error <= 0) {
                            swal({
                                title: 'Import erfolgreich.',
                                text: 'Alle Nutzer wurden erfolgreich importiert.',
                                type: 'success',
                            }).then(function() {
                                window.location.href = 'index.php';
                            })
                        } else {
                            swal({
                                title: 'Achtung.',
                                text: 'Es sind bei '+this.error+' Nutzern Fehler aufgetreten. Bitte überprüfen Sie diese Fehler, bevor Sie fortfahren.',
                                type: 'error',
                            })
                        }
                    } else {
                        if (this.counter < (csv.transit.newData.new.length - 1)) {
                            this.counter++;
                            this.send(csv.transit.newData.new[this.counter]);
                        } else {
                            csv.transit.taskFinishCallback();
                        }
                    }
                }
            } else {
                if (this.createHomeFolder) {
                    var createHome = '1';
                } else {
                    var createHome = '0';
                }
                this.request = getAjaxRequest();
                var url = "../api/api.php";
                var params = 'request=' + encodeURIComponent(JSON.stringify({
                    addaccount: {
                        givenname: data[1],
                        sn: data[0],
                        home: data[3],
                        userclass: data[5],
                        cn: data[2],
                        group: data[6],
                        gebdat: data[7],
                        email: data[4],
                        createhome: createHome,
                        passwd: data[8],
                    },
                }));
                this.request.onreadystatechange = this.got.bind(this);
                this.request.open("POST", url, true);
                this.request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                this.request.send(params);
            }
        },
        //Request response
        got: function() {
            if (this.request.readyState == 4) {
                this.callback(this.request.responseText);
            }
        }
    },
    load: function() {
        this.inputArray = document.getElementById('csv').value.replace(/\r\n/g, '\n').split('\n');
        this.preview();
    },
    transit: {
        isTransit: false,
        loader: null,
        serverMultiArray: null,
        madeDecisions: 0,
        decisionsToDo: 0,
        newData: {
            convert: [], //Convert users, because first name, surname and date of birth are equal
            decide: [], //Only one or two values are equal, so let the admin decide
            new: [], //New users to import
            delete: [], //Old users to delete
        },
        load: function() {
            if (document.getElementById('students').checked) {
                var group = 'students';
            } else {
                var group = 'teachers';
            }
            this.loader = getAjaxRequest();
            var url = "../api/api.php";
            var params = 'request=' + encodeURIComponent(JSON.stringify({
                transitload: {
                    group: group,
                }
            }));
            this.loader.onreadystatechange = this.gotLoad.bind(this);
            this.loader.open("POST", url, true);
            this.loader.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            this.loader.send(params);
        },
        gotLoad: function() {
            if (this.loader.readyState == 4) {
                this.serverMultiArray = JSON.parse(JSON.parse(this.loader.responseText).transitload);
                this.compareId = 0;
                this.compare(this.compareId);
            }
        },
        compareId: 0,
        compare: function(i) { //Had to move to recursive programming because sweet alert doens't hold loop
            var foundThree = [];
            var foundTwo = [];
            for (var c = 0; c < this.serverMultiArray.length; c++) { //Check all existing users for confirmitys
                var conformitys = this.checkConformitys(csv.multiArray[i], this.serverMultiArray[c]);
                if (conformitys == 3) {
                    foundThree.push(c);
                } else if (conformitys == 2) {
                    foundTwo.push(c);
                }
            }
            if (foundThree.length == 1) { //Found one with all three propertys matching
                this.newData.convert.push([csv.multiArray[i], this.serverMultiArray[foundThree[0]]]); //Put old and new users to convert array, and delete from source
                csv.multiArray[i] = null; //Cant splice this here, because then it will skip some users
                this.serverMultiArray.splice(foundThree[0], 1);
                this.compareNext();
            } else if (foundTwo.length == 1) {
                this.newData.decide.push([csv.multiArray[i], this.serverMultiArray[foundTwo[0]]]); //Put old and new users to decide array, and delete from source
                csv.multiArray[i] = null;
                this.serverMultiArray.splice(foundTwo[0], 1);
                this.compareNext();
            } else {
                if (foundThree.length > 0) {
                    var content = '';
                    var style = 0;
                    for (var c = 0; c < foundThree.length; c++) {
                        if ((style % 2) == 0) {
                            content += '<tr><td>' + csv.transit.serverMultiArray[foundThree[c]][0] + '</td><td>' + csv.transit.serverMultiArray[foundThree[c]][1] + '</td><td>' + csv.transit.serverMultiArray[foundThree[c]][7] + '</td></tr>';
                        } else {
                            content += '<tr class="alt"><td>' + csv.transit.serverMultiArray[foundThree[c]][0] + '</td><td>' + csv.transit.serverMultiArray[foundThree[c]][1] + '</td><td>' + csv.transit.serverMultiArray[foundThree[c]][7] + '</td></tr>';
                        }
                        style++;
                    }
                    swal({
                        title: 'Zwei komplett übereinstimmende Nutzer gefunden',
                        html: '<p>Der neue Nutzer</p><div class="datagrid"><table><thead><tr><th>Name:</th><th>Vorname:</th><th>Geburtsdatum:</th></tr></thead><tbody><tr><td>' + csv.multiArray[i][0] + '</td><td>' + csv.multiArray[i][1] + '</td><td>' + csv.multiArray[i][7] + '</td></tr></tbody></table></div><p>stimmt mit den alten Nutzern</p><div class="datagrid"><table><thead><tr><th>Name:</th><th>Vorname:</th><th>Geburtsdatum:</th></tr></thead><tbody>' + content + '</tbody></table></div><p>in Name, Vorname und Geburtsdatum überein. Bitte überprüfen Sie die vorhandenen Nutzer und korrigieren diesen Fehler manuell, bevor Sie fortfahren.</p>',
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        preConfirm: function() {
                            return new Promise(function(resolve) {
                                resolve();
                            })
                        }
                    }).then(function() {
                        window.location.href = 'index.php';
                    })
                } else if (foundTwo.length > 0) {
                    var content = '';
                    var style = 0;
                    for (var c = 0; c < foundTwo.length; c++) {
                        if ((style % 2) == 0) {
                            content += '<tr><td>' + csv.transit.serverMultiArray[foundTwo[c]][0] + '</td><td>' + csv.transit.serverMultiArray[foundTwo[c]][1] + '</td><td>' + csv.transit.serverMultiArray[foundTwo[c]][7] + '</td><td><a onclick="csv.transit.chooseTwo(' + i + ', ' + foundTwo[c] + ')">Wählen</a></td></tr>';
                        } else {
                            content += '<tr class="alt"><td>' + csv.transit.serverMultiArray[foundTwo[c]][0] + '</td><td>' + csv.transit.serverMultiArray[foundTwo[c]][1] + '</td><td>' + csv.transit.serverMultiArray[foundTwo[c]][7] + '</td><td><a onclick="csv.transit.chooseTwo(' + i + ', ' + foundTwo[c] + ')">Wählen</a></td></tr>';
                        }
                        style++;
                    }
                    swal({
                        title: 'Mehrere möglicherweise übereinstimmende Nutzer gefunden',
                        html: '<p>Der neue Nutzer</p><div class="datagrid"><table><thead><tr><th>Name:</th><th>Vorname:</th><th>Geburtsdatum:</th></tr></thead><tbody><tr><td>' + csv.multiArray[i][0] + '</td><td>' + csv.multiArray[i][1] + '</td><td>' + csv.multiArray[i][7] + '</td></tr></tbody></table></div><p>stimmt mit den alten Nutzern</p><div class="datagrid"><table><thead><tr><th>Name:</th><th>Vorname:</th><th>Geburtsdatum:</th><th>Aktion:</th></tr></thead><tbody>' + content + '</tbody></table></div><p>in 2 von 3 Werten überein. Bitte wählen Sie, welche Nutzer tatsächlich zusammen gehören.</p>',
                        showConfirmButton: false,
                        cancelButtonText: 'Abbrechen',
                    })
                } else {
                    for (var d = 0; d < this.newData.decide.length; d++) { //Check if there is a perfect match who got to decide array earlier, if yes move it to convert array (and re-add the old match to the source!)
                        var confirmitys = this.checkConformitys(csv.multiArray[i], this.newData.decide[d][1]);
                        if (confirmitys == 3) {
                            this.newData.convert.push([csv.multiArray[i], this.newData.decide[d][1]]);
                            csv.multiArray[i] = null;
                            csv.multiArray.push(this.newData.decide[d][0]);
                            this.newData.decide.splice(d, 1);
                            break;
                        }
                    }
                    this.compareNext();
                }
            }
        },
        compareNext: function() {
            if (this.compareId < (csv.multiArray.length - 1)) {
                this.compareId++;
                this.compare(this.compareId);
            } else {
                this.finishCompare();
            }
        },
        finishCompare: function() {
            this.newData.new = csv.multiArray;
            this.newData.delete = this.serverMultiArray;
            this.decisionsToDo = this.newData.decide.length;
            this.writeTables();
            preloader.toggle();
        },
        chooseTwo: function(newId, oldId) {
            this.newData.decide.push([csv.multiArray[newId], this.serverMultiArray[oldId]]);
            csv.multiArray[newId] = null;
            this.serverMultiArray.splice(oldId, 1);
            swal.close();
            this.compareId++;
            this.compare(this.compareId);
        },
        colorify: function(a, b) {
            if (a == b) {
                return '<td>' + a + '</td>';
            } else {
                return '<td style="color: red;">' + a + '</td>';
            }
        },
        writeTables: function() {
            var content = ['', '', '', ''];
            var styleCounter = 0;
            for (var i = 0; i < this.newData.convert.length; i++) {
                var klassekuerzel = this.colorify(this.newData.convert[i][0][5], this.newData.convert[i][1][5]);
                var mail = this.colorify(this.newData.convert[i][0][4], this.newData.convert[i][1][4]);
                if ((styleCounter % 2) == 0) {
                    content[0] += '<tr><td id="icon-convert-'+i+'"><i class="f7-icons" style="color: gray;">time</i></td><td>' + this.newData.convert[i][0][0] + '</td><td>' + this.newData.convert[i][0][1] + '</td><td>' + this.newData.convert[i][0][7] + '</td><td>' + this.newData.convert[i][1][5] + '</td>' + klassekuerzel + '<td>' + this.newData.convert[i][1][4] + '</td>' + mail + '</tr>';
                } else {
                    content[0] += '<tr class="alt"><td id="icon-convert-'+i+'"><i class="f7-icons" style="color: gray;">time</i></td><td>' + this.newData.convert[i][0][0] + '</td><td>' + this.newData.convert[i][0][1] + '</td><td>' + this.newData.convert[i][0][7] + '</td><td>' + this.newData.convert[i][1][5] + '</td>' + klassekuerzel + '<td>' + this.newData.convert[i][1][4] + '</td>' + mail + '</tr>';
                }
                styleCounter++;
            }
            var styleCounter = 0;
            for (var i = 0; i < this.newData.decide.length; i++) {
                var klassekuerzel = this.colorify(this.newData.decide[i][0][5], this.newData.decide[i][1][5]);
                var mail = this.colorify(this.newData.decide[i][0][4], this.newData.decide[i][1][4]);
                var name = this.colorify(this.newData.decide[i][0][0], this.newData.decide[i][1][0]);
                var vorname = this.colorify(this.newData.decide[i][0][1], this.newData.decide[i][1][1]);
                var gebdat = this.colorify(this.newData.decide[i][0][7], this.newData.decide[i][1][7]);
                if ((styleCounter % 2) == 0) {
                    content[1] += '<tr><td id="icon-decide-'+i+'"><i class="f7-icons" style="color: gray;">time</i></td><td>' + this.newData.decide[i][1][0] + '</td>' + name + '<td>' + this.newData.decide[i][1][1] + '</td>' + vorname + '<td>' + this.newData.decide[i][1][7] + '</td>' + gebdat + '<td>' + this.newData.decide[i][1][5] + '</td>' + klassekuerzel + '<td>' + this.newData.decide[i][1][4] + '</td>' + mail + '<td style="vertical-align: top;"><a onclick="csv.transit.merge(' + i + ')">Zusammenführen</a>; <a onclick="csv.transit.separate(' + i + ')">Trennen</a></td></tr>';
                } else {
                    content[1] += '<tr class="alt"><td id="icon-decide-'+i+'"><i class="f7-icons" style="color: gray;">time</i></td><td>' + this.newData.decide[i][1][0] + '</td>' + name + '<td>' + this.newData.decide[i][1][1] + '</td>' + vorname + '<td>' + this.newData.decide[i][1][7] + '</td>' + gebdat + '<td>' + this.newData.decide[i][1][5] + '</td>' + klassekuerzel + '<td>' + this.newData.decide[i][1][4] + '</td>' + mail + '<td style="vertical-align: top;"><a onclick="csv.transit.merge(' + i + ')">Zusammenführen</a>; <a onclick="csv.transit.separate(' + i + ')">Trennen</a></td></tr>';
                }
                styleCounter++;
            }
            var styleCounter = 0;
            for (var i = 0; i < this.newData.new.length; i++) {
                if (this.newData.new[i] != null) {
                    if ((styleCounter % 2) == 0) {
                        content[2] += '<tr><td id="icon-'+i+'"><i class="f7-icons" style="color: gray;">time</i></td><td>' + this.newData.new[i][0] + '</td><td>' + this.newData.new[i][1] + '</td><td>' + this.newData.new[i][2] + '</td><td>' + this.newData.new[i][3] + '</td><td>' + this.newData.new[i][4] + '</td><td>' + this.newData.new[i][5] + '</td><td>' + this.newData.new[i][6] + '</td><td>' + this.newData.new[i][7] + '</td><td>' + this.newData.new[i][8] + '</td><td style="vertical-align: top;"><a onclick="csv.transit.mergeNew(' + i + ')">Zusammenführen</a></td></tr>';
                    } else {
                        content[2] += '<tr class="alt"><td id="icon-'+i+'"><i class="f7-icons" style="color: gray;">time</i></td><td>' + this.newData.new[i][0] + '</td><td>' + this.newData.new[i][1] + '</td><td>' + this.newData.new[i][2] + '</td><td>' + this.newData.new[i][3] + '</td><td>' + this.newData.new[i][4] + '</td><td>' + this.newData.new[i][5] + '</td><td>' + this.newData.new[i][6] + '</td><td>' + this.newData.new[i][7] + '</td><td>' + this.newData.new[i][8] + '</td><td style="vertical-align: top;"><a onclick="csv.transit.mergeNew(' + i + ')">Zusammenführen</a></td></tr>';
                    }
                    styleCounter++;
                }
            }
            var styleCounter = 0;
            for (var i = 0; i < this.newData.delete.length; i++) {
                if ((styleCounter % 2) == 0) {
                    content[3] += '<tr><td id="icon-delete-'+i+'"><i class="f7-icons" style="color: gray;">time</i></td><td>' + this.newData.delete[i][0] + '</td><td>' + this.newData.delete[i][1] + '</td><td>' + this.newData.delete[i][2] + '</td><td>' + this.newData.delete[i][3] + '</td><td>' + this.newData.delete[i][4] + '</td><td>' + this.newData.delete[i][5] + '</td><td>' + this.newData.delete[i][6] + '</td><td>' + this.newData.delete[i][7] + '</td><td style="vertical-align: top;"><a onclick="csv.transit.keep(' + i + ')">Behalten</a></td></tr>';
                } else {
                    content[3] += '<tr class="alt"><td id="icon-delete-'+i+'"><i class="f7-icons" style="color: gray;">time</i></td><td>' + this.newData.delete[i][0] + '</td><td>' + this.newData.delete[i][1] + '</td><td>' + this.newData.delete[i][2] + '</td><td>' + this.newData.delete[i][3] + '</td><td>' + this.newData.delete[i][4] + '</td><td>' + this.newData.delete[i][5] + '</td><td>' + this.newData.delete[i][6] + '</td><td>' + this.newData.delete[i][7] + '</td><td style="vertical-align: top;"><a onclick="csv.transit.keep(' + i + ')">Behalten</a></td></tr>';
                }
                styleCounter++;
            }
            document.getElementById('three-matches-table-content').innerHTML = content[0];
            document.getElementById('two-matches-table-content').innerHTML = content[1];
            document.getElementById('new-accounts-table-content').innerHTML = content[2];
            document.getElementById('old-accounts-table-content').innerHTML = content[3];
            document.getElementById('view-1').classList.add('nodisplay');
            document.getElementById('view-2').classList.remove('nodisplay');
        },
        checkConformitys: function(first, second) {
            var result = 0;
            if (csv.parse.umlaute(first[0]) == csv.parse.umlaute(second[0])) {
                result++;
            }
            if (csv.parse.umlaute(first[1]) == csv.parse.umlaute(second[1])) {
                result++;
            }
            if (csv.parse.umlaute(first[7]) == csv.parse.umlaute(second[7])) {
                result++;
            }
            return result;
        },
        merge: function(id) {
            this.newData.convert.push(this.newData.decide[id]);
            this.newData.decide.splice(id, 1);
            this.madeDecisions++;
            this.writeTables();
        },
        separateRequest: null,
        separateId: 0,
        separate: function(id) {
            this.separateId = id;
            this.separateRequest = getAjaxRequest();
            var url = '../api/api.php';
            var params = "request=" + encodeURIComponent(JSON.stringify({
                accountexists: {
                    user: this.newData.decide[id][0][2],
                }
            }));
            this.separateRequest.onreadystatechange = this.separateGot.bind(this);
            this.separateRequest.open("POST", url, true);
            this.separateRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            this.separateRequest.send(params);
        },
        separateGot: function() {
            if (this.separateRequest.readyState == 4) {
                var response = JSON.parse(this.separateRequest.responseText);
                if (response.accountexists == 'EXISTS') {
                    swal({
                        title: 'Nutzername bereits vergeben',
                        text: 'Bitte ändern Sie den Nutzernamen ab. Achtung: Der Nutzername darf keine Leerzeichen, Umlaute und Großbuchstaben enthalten!',
                        input: 'text',
                        inputValue: this.newData.decide[this.separateId][0][2],
                        type: 'warning',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showLoaderOnConfirm: true,
                        preConfirm: function(text) {
                            return new Promise(function(resolve) {
                                if (csv.parse.umlaute(text) != text) {
                                    swal.showValidationError('Der Nutzername darf keine Leerzeichen, Umlaute und Großbuchstaben enthalten!');
                                    swal.enableButtons();
                                } else {
                                    csv.transit.newData.decide[csv.transit.separateId][0][2] = text;
                                    var homefolder = csv.transit.newData.decide[csv.transit.separateId][0][3].split('/');
                                    csv.transit.newData.decide[csv.transit.separateId][0][3] = '/' + homefolder[1] + '/' + homefolder[2] + '/' + text;
                                    resolve();
                                }
                            })
                        }
                    }).then(function() {
                        csv.transit.separate(csv.transit.separateId);
                    });
                } else {
                    this.separateConfirm(this.separateId);
                }
            }
        },
        separateConfirm: function(id) {
            this.newData.new.push(this.newData.decide[id][0]);
            this.newData.decide.splice(id, 1);
            this.madeDecisions++;
            this.writeTables();
        },
        keep: function(id) {
            this.newData.delete.splice(id, 1);
            this.writeTables();
        },
        mergeNew: function(id) {
            var content = '';
            var style = 0;
            for (var i = 0; i < this.newData.delete.length; i++) {
                if ((style % 2) == 0) {
                    content += '<tr><td>' + this.newData.delete[i][0] + '</td><td>' + this.newData.delete[i][1] + '</td><td>' + this.newData.delete[i][7] + '</td><td><a onclick="csv.transit.mergeNewConfirm(' + id + ', ' + i + ')">Wählen</a></td></tr>';
                } else {
                    content += '<tr class="alt"><td>' + this.newData.delete[i][0] + '</td><td>' + this.newData.delete[i][1] + '</td><td>' + this.newData.delete[i][7] + '</td><td><a onclick="csv.transit.mergeNewConfirm(' + id + ', ' + i + ')">Wählen</a></td></tr>';
                }
                style++;
            }
            swal({
                title: 'Neuen Account zusammenführen',
                html: '<div class="datagrid"><table><thead><tr><th>Name:</th><th>Vorname:</th><th>Geburtsdatum:</th><th>Aktion:</th></tr></thead><tbody>' + content + '</tbody></table></div>',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Abbrechen',
            });
        },
        mergeNewConfirm: function(newId, oldId) {
            this.newData.convert.push([this.newData.new[newId], this.newData.delete[oldId]]);
            this.newData.new.splice(newId, 1);
            this.newData.delete.splice(oldId, 1);
            this.writeTables();
        },
        doConvert: function() {
            if (this.decisionsToDo != this.madeDecisions) {
                swal({
                    title: 'Es sind noch ' + (this.decisionsToDo - this.madeDecisions) +' von ' + this.decisionsToDo + ' Entscheidungen zu treffen.',
                    text: 'Bitte wählen Sie, was aus den Accounts mit zwei Übereinstimmungen geschehen soll.',
                    type: 'warning',
                });
            } else {
                swal({
                    title: 'Änderungen übernehmen?',
                    showCancelButton: true,
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Übernehmen",
                    cancelButtonText: "Abbrechen",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    type: 'question',
                    showLoaderOnConfirm: true,
                    preConfirm: function() {
                        return new Promise(function(resolve) {
                            csv.import.callback = csv.import.multiCallback;
                            if (csv.transit.newData.convert.length > 0) {
                                csv.transit.convert(csv.transit.newData.convert[csv.transit.convertCounter]);
                            } else {
                                csv.transit.taskFinishCallback();
                            }
                            if (csv.transit.newData.new.length > 0) {
                                csv.import.send(csv.transit.newData.new[csv.import.counter], document.getElementById('createhome').checked);
                            } else {
                                csv.transit.taskFinishCallback();
                            }
                            if (csv.transit.newData.delete.length > 0) {
                                csv.transit.delete(csv.transit.newData.delete[csv.transit.deleteCounter]);
                            } else {
                                csv.transit.taskFinishCallback();
                            }
                        })
                    }
                })
            }
        },
        deleteCounter: 0,
        deleteRequest: null,
        deleteError: 0,
        deleteCallback: function(result) {
            var response = JSON.parse(result);
            if (response.deleteaccount == 'SUCCESS') {
                document.getElementById('icon-delete-'+this.deleteCounter).innerHTML = '<i class="f7-icons" style="color: green;">check_round</i>';
            } else if (response.deleteaccount == 'ERR_MOVE_HOME') {
                this.deleteError++;
                document.getElementById('icon-delete-'+this.deleteCounter).innerHTML = '<i class="f7-icons" style="color: red;">folder_fill</i>';
            } else if (response.deleteaccount == 'ERR_DELETE_OLD_FOLDER') {
                this.deleteError++;
                document.getElementById('icon-delete-'+this.deleteCounter).innerHTML = '<i class="f7-icons" style="color: red;">folder_fill</i>';
            } else if (response.deleteaccount == 'ERR_REMOVE_FROM_GROUP') {
                this.deleteError++;
                document.getElementById('icon-delete-'+this.deleteCounter).innerHTML = '<i class="f7-icons" style="color: red;">persons_fill</i>';
            } else if (response.deleteaccount == 'ERR_DELETE_OBJECT') {
                this.deleteError++;
                document.getElementById('icon-delete-'+this.deleteCounter).innerHTML = '<i class="f7-icons" style="color: red;">bolt_round</i>';
            } else {
                this.deleteError++;
                document.getElementById('icon-delete-'+this.deleteCounter).innerHTML = '<i class="f7-icons" style="color: red;">bolt_round</i>';
            }
            if (this.deleteCounter < (this.serverMultiArray.length - 1)) {
                this.deleteCounter++;
                this.delete(this.newData.delete[this.deleteCounter]);
            } else {
                this.taskFinishCallback();
            }
        },
        delete: function(data) {
            this.deleteRequest = getAjaxRequest();
            var url = '../api/api.php';
            var params = "request=" + encodeURIComponent(JSON.stringify({
                deleteaccount: {
                    user: data[2],
                    group: data[6],
                }
            }));
            this.deleteRequest.onreadystatechange = this.deleteGot.bind(this);
            this.deleteRequest.open("POST", url, true);
            this.deleteRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            this.deleteRequest.send(params);
        },
        deleteGot: function() {
            if (this.deleteRequest.readyState == 4) {
                this.deleteCallback(this.deleteRequest.responseText);
            }
        },
        convertCounter: 0,
        convertRequest: null,
        convertError: 0,
        convertCallback: function(result) {
            var response = JSON.parse(result);
            if (response.modifyaccount == 'SUCCESS') {
                document.getElementById('icon-convert-'+this.convertCounter).innerHTML = '<i class="f7-icons" style="color: green;">check_round</i>';
            } else {
                this.convertError++;
                document.getElementById('icon-convert-'+this.convertCounter).innerHTML = '<i class="f7-icons" style="color: red;">bolt_round</i>';
            }
            if (this.convertCounter < (this.newData.convert.length - 1)) {
                this.convertCounter++;
                this.convert(this.newData.convert[this.convertCounter]);
            } else {
                this.taskFinishCallback();
            }
        },
        convert: function(data) {
            this.convertRequest = getAjaxRequest();
            var url = '../api/api.php';
            var params = "request=" + encodeURIComponent(JSON.stringify({
                modifyaccount: {
                    user: data[1][2],
                    sn: data[0][0],
                    givenname: data[0][1],
                    email: data[0][4],
                    gebdat: data[0][7],
                    userclass: data[0][5],
                }
            }));
            this.convertRequest.onreadystatechange = this.convertGot.bind(this);
            this.convertRequest.open("POST", url, true);
            this.convertRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            this.convertRequest.send(params);
        },
        convertGot: function() {
            if (this.convertRequest.readyState == 4) {
                this.convertCallback(this.convertRequest.responseText);
            }
        },
        tasksFinished: 0,
        taskFinishCallback: function() {
            this.tasksFinished++;
            if (this.tasksFinished == 3) {
                if (this.deleteError == 0 && this.convertError == 0 && csv.import.error == 0) {
                    swal({
                        title: 'Änderungen erfolgreich übernommen.',
                        type: 'success',
                    }).then(function() {
                        window.location.href = 'index.php';
                    });
                } else {
                    swal({
                        title: 'Es sind Fehler aufgetreten.',
                        text: 'Bitte überprüfen Sie anhand der Icons die fehlerhaften Nutzer. Fehler beim Modifizieren: ' + this.convertError + ', Fehler beim Hinzufügen: ' + csv.import.error + ', Fehler beim Löschen: ' + this.deleteError,
                        type: 'error',
                    });
                }
            }
        },
    },
}
