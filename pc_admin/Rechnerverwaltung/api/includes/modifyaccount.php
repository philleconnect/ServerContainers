<?php
    $ldapconn = ldap_connect(loadConfig('ldap', 'url'));
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_bind($ldapconn, loadConfig('ldap', 'admindn').','.loadConfig('ldap', 'basedn'), loadConfig('ldap', 'password'));
    $entry = array();
    $entry['sn'] = $client_request->modifyaccount->sn;
    $entry['givenName'] = $client_request->modifyaccount->givenname;
    $entry['displayName'] = $client_request->modifyaccount->givenname.' '.$client_request->modifyaccount->sn;
    $entry['description'] = $client_request->modifyaccount->gebdat;
    $entry['businessCategory'] = $client_request->modifyaccount->userclass;
    $entry['mail'] = $client_request->modifyaccount->email;
    if (ldap_modify($ldapconn, "uid=".$client_request->modifyaccount->user.", ".loadConfig('ldap', 'usersdn').','.loadConfig('ldap', 'basedn'), $entry)) {
        $client_response['modifyaccount'] = 'SUCCESS';
    } else {
        $client_response['modifyaccount'] = 'ERR_MODIFY_OBJECT';
    }
?>