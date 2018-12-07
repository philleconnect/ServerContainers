<?php
    $ldapconn = ldap_connect(loadConfig('ldap', 'url'));
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    $r=ldap_bind($ldapconn);
    $allusers=ldap_search($ldapconn, loadConfig('ldap', 'usersdn').','.loadConfig('ldap', 'basedn'), "uid=".$client_request->accountexists->user);
    $users = ldap_get_entries($ldapconn, $allusers);
    if ($users['count'] < 1) {
        $client_response['accountexists'] = 'ERR_NOT_FOUND';
    } else {
        $client_response['accountexists'] = 'EXISTS';
    }
?>
