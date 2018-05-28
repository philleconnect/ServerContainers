<?php
    $ldapconn = ldap_connect(loadConfig('ldap', 'url'));
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    $r=ldap_bind($ldapconn);
    $entry = array();
    $entry['givenName'] = $client_request->saveaccount->givenname;
    $entry['sn'] = $client_request->saveaccount->sn;
    $entry['homeDirectory'] = $client_request->saveaccount->home;
    $entry['businessCategory'] = $client_request->saveaccount->userclass;
    $entry['description'] = $client_request->saveaccount->gebdat;
    $entry['mail'] = $client_request->saveaccount->email;
    if ($client_request->saveaccount->pwd !== '') {
		if ($client_request->saveaccount->pwd === $client_request->saveaccount->pwd2) {
            $salt = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 4)), 0, 4);
            $hash = '{SSHA}' . base64_encode(sha1($client_request->saveaccount->pwd.$salt, TRUE).$salt);
            $sambaHash = strtoupper(bin2hex(mhash(MHASH_MD4, iconv("UTF-8","UTF-16LE",$client_request->saveaccount->pwd))));
            $entry['userPassword'] = $hash;
            $entry['sambaNTPassword'] = $sambaHash;
		} else {
            $client_response['saveaccount'] = 'ERR_PASSWORDS_DIFFERENT';
            $stop = true;
		}
	}
    if (!$stop) {
        ldap_bind($ldapconn, loadConfig('ldap', 'admindn').','.loadConfig('ldap', 'basedn'), loadConfig('ldap', 'password'));
        if (ldap_modify($ldapconn, "uid=".$client_request->saveaccount->user.", ".loadConfig('ldap', 'usersdn').','.loadConfig('ldap', 'basedn'), $entry)) {
            $client_response['saveaccount'] = 'SUCCESS';
        } else {
            $client_response['saveaccount'] = 'ERR_UPDATE_FAILED';
        }
    }
?>
