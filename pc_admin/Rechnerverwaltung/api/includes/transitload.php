<?php
    $ldapconn = ldap_connect(loadConfig('ldap', 'url'));
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    $r=ldap_bind($ldapconn);
    $allusers=ldap_search($ldapconn, loadConfig('ldap', 'usersdn').','.loadConfig('ldap', 'basedn'), "uid=*");
    $users = ldap_get_entries($ldapconn, $allusers);
    if ($client_request->transitload->group == 'teachers') {
        $group=ldap_search($ldapconn, loadConfig('ldap', 'groupsdn').','.loadConfig('ldap', 'basedn'), loadConfig('ldap', 'teacherscn'));
    } else {
        $group=ldap_search($ldapconn, loadConfig('ldap', 'groupsdn').','.loadConfig('ldap', 'basedn'), loadConfig('ldap', 'studentscn'));
    }
    $groupcontent = ldap_get_entries($ldapconn, $group);
    $data = array();
    for ($i=0; $i<$users['count']; $i++) {
        if (in_array($users[$i]['cn'][0], $groupcontent[0]['memberuid'])) {
            $thisuser = array($users[$i]['sn'][0], $users[$i]['givenname'][0], $users[$i]['cn'][0], $users[$i]['homedirectory'][0], $users[$i]['mail'][0], $users[$i]['businesscategory'][0], $client_request->transitload->group, $users[$i]['description'][0]);
            array_push($data, $thisuser);
        }
    }
    $client_response['transitload'] = json_encode($data);
?>