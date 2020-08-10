-- SchoolConnect main database initial content
-- © 2019 Johannes Kreutz.

-- create students, teachers and wifi groups
INSERT INTO groups (name, info, type) VALUES ("root", "ROOT-Gruppe (Administratoren mit allen Rechten). Kann nicht gelöscht werden.", 1);
INSERT INTO groups (name, info, type) VALUES ("students", "Beispielgruppe für alle Schüler.", 0);
INSERT INTO groups (name, info, type) VALUES ("teachers", "Beispielgruppe für alle Lehrer.", 0);
INSERT INTO groups (name, info, type) VALUES ("wifi", "Beispielgruppe für alle Nutzer, die auf das WLAN zugreifen dürfen sollen.", 0);

-- create shares
INSERT INTO shares (name, path) VALUES ("SchulTausch", "/home/shares/schoolExchange");
INSERT INTO shares (name, path) VALUES ("SchulVorlagen", "/home/shares/schoolTemplate");
INSERT INTO shares (name, path) VALUES ("LehrerTausch", "/home/shares/teacherExchange");
INSERT INTO shares (name, path) VALUES ("LehrerVorlagen", "/home/shares/teacherTemplate");

-- create permissions
INSERT INTO permission (name, info, detail) VALUES ("Schuelercomputer-Login", "Diese Berechtigung erlaubt es den Gruppenmitgliedern, sich an Schülercomputern anzumelden.", "studelgn");
INSERT INTO permission (name, info, detail) VALUES ("Lehrercomputer-Login", "Diese Berechtigung erlaubt es den Gruppenmitgliedern, sich an Lehrercomputern anzumelden.", "teachlgn");
INSERT INTO permission (name, info, detail) VALUES ("Nutzerverwaltung", "Zugriff auf die Nutzerverwaltungs-Funktionen der Administrator-GUI.", "usermgmt");
INSERT INTO permission (name, info, detail) VALUES ("Geräteverwaltung", "Zugriff auf die Geräteverwaltungs-Funktionen der Administrator-GUI.", "devimgmt");
INSERT INTO permission (name, info, detail) VALUES ("Serververwaltung", "Zugriff auf die Serververwaltungs-Funktionen der Administrator-GUI.", "servmgmt");
INSERT INTO permission (name, info, detail) VALUES ("Passwort zurücksetzen", "Lehrer dürfen Passwörter dieser Nutzer zurücksetzen", "pwalwrst");

-- connect permissions with groups
INSERT INTO groups_has_permission (group_id, permission_id) VALUES (1, 1);
INSERT INTO groups_has_permission (group_id, permission_id) VALUES (1, 2);
INSERT INTO groups_has_permission (group_id, permission_id) VALUES (1, 3);
INSERT INTO groups_has_permission (group_id, permission_id) VALUES (1, 4);
INSERT INTO groups_has_permission (group_id, permission_id) VALUES (1, 5);
INSERT INTO groups_has_permission (group_id, permission_id) VALUES (2, 1);
INSERT INTO groups_has_permission (group_id, permission_id) VALUES (2, 6);
INSERT INTO groups_has_permission (group_id, permission_id) VALUES (3, 1);
INSERT INTO groups_has_permission (group_id, permission_id) VALUES (3, 2);

-- connect shares with groups
INSERT INTO groups_has_shares (shares_id, group_id, permission) VALUES (1, 2, 1);
INSERT INTO groups_has_shares (shares_id, group_id, permission) VALUES (1, 3, 1);
INSERT INTO groups_has_shares (shares_id, group_id, permission) VALUES (2, 2, 0);
INSERT INTO groups_has_shares (shares_id, group_id, permission) VALUES (2, 3, 1);
INSERT INTO groups_has_shares (shares_id, group_id, permission) VALUES (3, 3, 1);
INSERT INTO groups_has_shares (shares_id, group_id, permission) VALUES (4, 3, 1);

-- example device profiles
INSERT INTO devprofile (name, comment, networklockDefault, allowVNC) VALUES ("Beispielprofil Schülercomputer", "Dies ist ein automatisch generiertes Beispielprofil für Schülercomputer.", 0, 1);
INSERT INTO devprofile (name, comment, networklockDefault, allowVNC) VALUES ("Beispielprofil Lehrercomputer", "Dies ist ein automatisch generiertes Beispielprofil für Lehrercomputer.", 1, 1);

-- connect example device profiles to groups
INSERT INTO devprofile_has_groups (devprofile_id, group_id) VALUES (1, 2);
INSERT INTO devprofile_has_groups (devprofile_id, group_id) VALUES (1, 3);
INSERT INTO devprofile_has_groups (devprofile_id, group_id) VALUES (2, 3);
