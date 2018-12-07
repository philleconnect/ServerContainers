<?php
    include 'directoryFunctions.php';
    $ldapconn = ldap_connect(loadConfig('ldap', 'url'));
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_bind($ldapconn, loadConfig('ldap', 'admindn').','.loadConfig('ldap', 'basedn'), loadConfig('ldap', 'password'));
    if (ldap_delete($ldapconn, "uid=".$client_request->deleteaccount->user.", ".loadConfig('ldap', 'usersdn').','.loadConfig('ldap', 'basedn'))) {
        $entry = array();
        $entry['memberUid'] = $client_request->deleteaccount->user;
        if (ldap_mod_del($ldapconn, 'cn='.$client_request->deleteaccount->group.', '.loadConfig('ldap', 'groupsdn').','.loadConfig('ldap', 'basedn'), $entry)) {
            if (is_dir('/home/deleted/'.$client_request->deleteaccount->user)) {
                if (deleteDirectory('/home/deleted/'.$client_request->deleteaccount->user)) {
                    if (moveFolder('/home/'.$client_request->deleteaccount->group.'/'.$client_request->deleteaccount->user, '/home/deleted/'.$client_request->deleteaccount->user)) {
                        $client_response['deleteaccount'] = 'SUCCESS';
                    } else {
                        $client_response['deleteaccount'] = 'ERR_MOVE_HOME';
                    }
                } else {
                    $client_response['deleteaccount'] = 'ERR_DELETE_OLD_FOLDER';
                }
            } else {
                if (moveFolder('/home/'.$client_request->deleteaccount->group.'/'.$client_request->deleteaccount->user, '/home/deleted/'.$client_request->deleteaccount->user)) {
                    $client_response['deleteaccount'] = 'SUCCESS';
                } else {
                    $client_response['deleteaccount'] = 'ERR_MOVE_HOME';
                }
            }
        } else {
            $client_response['deleteaccount'] = 'ERR_REMOVE_FROM_GROUP';
        }
    } else {
        $client_response['deleteaccount'] = 'ERR_DELETE_OBJECT';
    }
?>
