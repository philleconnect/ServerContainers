<?php
    session_start();
    if ($_SESSION['user'] == null || $_SESSION['user'] == '') {
        echo 'error';
    } else {
        include "dbconnect.php";
        include "accessConfig.php";
        if (ldap_add($ldapconn, "uid=".$_POST['cn'].", ".loadConfig('ldap', 'usersdn').','.loadConfig('ldap', 'basedn'), $entry)) {
            $newUidRequest = "UPDATE site SET data = '".$uid."' WHERE value = 'lastUid'";
            if (mysqli_query($database, $newUidRequest)) {
                $groupentry = array();
                $groupentry['memberUid'] = $_POST['cn'];
                if (ldap_mod_add($ldapconn, 'cn='.$_POST['group'].', '.loadConfig('ldap', 'groupsdn').','.loadConfig('ldap', 'basedn'), $groupentry)) {
                    echo 'success';
                } else {
                    echo 'group_error';
                }
            } else {
                echo 'update_error';
            }
        } else {
            echo 'add_error';
        }
    }
?>