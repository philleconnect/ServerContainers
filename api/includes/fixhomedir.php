<?php
    $ldapconn = ldap_connect(loadConfig('ldap', 'url'));
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_bind($ldapconn, loadConfig('ldap', 'admindn').','.loadConfig('ldap', 'basedn'), loadConfig('ldap', 'password'));
    $entry = array();
    $salt = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 4)), 0, 4);
    $hash = '{SSHA}' . base64_encode(sha1($client_request->addaccount->passwd.$salt, TRUE).$salt);
    $sambaHash = strtoupper(bin2hex(mhash(MHASH_MD4, iconv("UTF-8","UTF-16LE",$client_request->addaccount->passwd))));
    $now = time();
    $uid = (loadConfig('ldap', 'lastuid') + 1);
    $search = ldap_search($ldapconn, loadConfig('ldap', 'basedn'), "sambaDomainName=".loadConfig('ldap', 'sambahostname'));
    $result = ldap_get_entries($ldapconn, $search);
    $entry['description'] = $client_request->addaccount->gebdat;
    $entry['businessCategory'] = $client_request->addaccount->userclass;
    $entry['cn'] = $client_request->addaccount->cn;
    $entry['displayName'] = $client_request->addaccount->givenname.' '.$client_request->addaccount->sn;
    $entry['gecos'] = 'System User';
    $entry['gidNumber'] = '513';
    $entry['givenName'] = $client_request->addaccount->givenname;
    $entry['homeDirectory'] = $client_request->addaccount->home;
    $entry['loginShell'] = '/bin/bash';
    $entry['mail'] = $client_request->addaccount->email;
    $entry['objectClass'] = array('top', 'person', 'organizationalPerson', 'posixAccount', 'shadowAccount', 'inetOrgPerson', 'sambaSamAccount');
    $entry['userPassword'] = $hash;
    $entry['sambaAcctFlags'] = '[U]';
    $entry['sambaHomePath'] = '\\\\\\'.$client_request->addaccount->cn;
    $entry['sambaKickoffTime'] = '2147483647';
    $entry['sambaLogoffTime'] = '2147483647';
    $entry['sambaLogonTime'] = '0';
    $entry['sambaNTPassword'] = $sambaHash;
    $entry['sambaPrimaryGroupSID'] = $result[0]['sambasid'][0].'-513';
    $entry['sambaProfilePath'] = '\\\\\\profiles\\'.$client_request->addaccount->cn;
    $entry['sambaPwdCanChange'] = '0';
    $entry['sambaPwdLastSet'] = $now;
    $entry['sambaPwdMustChange'] = '86401501080402';
    $entry['sambaSID'] = $result[0]['sambasid'][0].'-'.$uid;
    $entry['shadowLastChange'] = $now;
    $entry['shadowMax'] = '999999999';
    $entry['sn'] = $client_request->addaccount->sn;
    $entry['uidNumber'] = $uid;
    $entry['uid'] = $client_request->addaccount->cn;
    if (ldap_add($ldapconn, "uid=".$client_request->addaccount->cn.", ".loadConfig('ldap', 'usersdn').','.loadConfig('ldap', 'basedn'), $entry)) {
        if (changeConfigValue('ldap', 'lastuid', $uid)) {
            $groupentry = array();
            $groupentry['memberUid'] = $client_request->addaccount->cn;
            if (ldap_mod_add($ldapconn, 'cn='.$client_request->addaccount->group.', '.loadConfig('ldap', 'groupsdn').','.loadConfig('ldap', 'basedn'), $groupentry)) {
                if ($client_request->addaccount->createhome == '1') {
                    if (mkdir($client_request->addaccount->home, 0777)) {
                        $gidSearch = ldap_search($ldapconn, loadConfig('ldap', 'groupsdn').','.loadConfig('ldap', 'basedn'), "cn=".$client_request->addaccount->group);
                        $gidResult = ldap_get_entries($ldapconn, $gidSearch);
                        /*if (*/shell_exec('sudo chown '.$uid.':'.$gidResult[0]['gidnumber'][0].' '.$client_request->addaccount->home); //!= '') {
                            /*if (*/shell_exec('sudo chmod 777 '.$client_request->addaccount->home); //!= '') {
                                $client_response['addaccount'] = 'SUCCESS';
                            /*} else {
                                $client_response['addaccount'] = 'ERR_HOME_USER';
                            }
                        } else {
                            $client_response['addaccount'] = 'ERR_HOME_GROUP';
                        }*/
                    } else {
                        $client_response['addaccount'] = 'ERR_CREATE_HOME';
                    }
                } else {
                    $client_response['addaccount'] = 'SUCCESS';
                }
            } else {
                $client_response['addaccount'] = 'ERR_ADD_TO_GROUP';
            }
        } else {
            $client_response['addaccount'] = 'ERR_UPDATE_UID';
        }
    } else {
        $client_response['addaccount'] = 'ERR_ADD_OBJECT';
    }
?>
