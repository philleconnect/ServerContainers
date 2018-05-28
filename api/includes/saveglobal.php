<?php
    if ($_SESSION['type'] != '1') {
        $client_response['saveglobal'] = 'ERR_ACCESS_DENIED';
    } else {
        if ($client_request->saveglobal->prefix == 'ldap') {
            changeConfigValue('ldap', 'url', $client_request->saveglobal->url);
            changeConfigValue('ldap', 'password', $client_request->saveglobal->password);
            changeConfigValue('ldap', 'basedn', $client_request->saveglobal->basedn);
            changeConfigValue('ldap', 'admindn', $client_request->saveglobal->admindn);
            changeConfigValue('ldap', 'usersdn', $client_request->saveglobal->usersdn);
            changeConfigValue('ldap', 'groupsdn', $client_request->saveglobal->groupsdn);
            changeConfigValue('ldap', 'studentscn', $client_request->saveglobal->studentscn);
            changeConfigValue('ldap', 'teacherscn', $client_request->saveglobal->teacherscn);
            changeConfigValue('ldap', 'sambahostname', $client_request->saveglobal->sambahostname);
        } elseif ($client_request->saveglobal->prefix == 'ipfire') {
            changeConfigValue('ipfire', 'url', $client_request->saveglobal->url);
            changeConfigValue('ipfire', 'port', $client_request->saveglobal->port);
            changeConfigValue('ipfire', 'pubkey', $client_request->saveglobal->pubkey);
            changeConfigValue('ipfire', 'rsafile', $client_request->saveglobal->rsafile);
        } elseif ($client_request->saveglobal->prefix == 'globalPw') {
            changeConfigValue('globalPw', null, $client_request->saveglobal->globalPw);
        }
        $client_response['saveglobal'] = 'SUCCESS';
    }
?>
