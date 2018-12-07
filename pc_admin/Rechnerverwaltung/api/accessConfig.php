<?php
    function changeConfigValue($group, $value, $newValue) {
        if (file_exists('config/config.txt')) {
            $path = 'config/config.txt';
        } else {
            $path = '../config/config.txt';
        }
        $data = file_get_contents($path);
        if ($data == 'empty') {
            include "versioncode.php";
            $config = [
                "database" => [
                    "url" => "",
                    "user" => "",
                    "password" => "",
                    "name" => "",
                    "finish" => "false",
                ],
                "ldap" => [
                    "url" => "",
                    "password" => "",
                    "basedn" => "",
                    "admindn" => "",
                    "usersdn" => "",
                    "groupsdn" => "",
                    "teacherscn" => "",
                    "studentscn" => "",
                    "sambahostname" => "",
                    "lastuid" => "19999",
                    "finish" => "false",
                ],
                "ipfire" => [
                    "url" => "",
                    "port" => "",
                    "password" => "",
                    "finish" => "false",
                ],
                "globalPw" => "",
                "config" => "",
                "versioncode" => $versioncode,
            ];
        } else {
            $config = unserialize($data);
        }
        if (is_array($config[$group])) {
            $config[$group][$value] = $newValue;
        } else {
            $config[$group] = $newValue;
        }
        if (file_put_contents($path, serialize($config)) != false) {
            return true;
        } else {
            return false;
        }
    }
    function loadConfig($group, $value) {
        if (file_exists('config/config.txt')) {
            $data = file_get_contents('config/config.txt');
        } else {
            $data = file_get_contents('../config/config.txt');
        }
        if ($data != 'empty') {
            $config = unserialize($data);
            if (is_array($config[$group])) {
                return $config[$group][$value];
            } else {
                return $config[$group];
            }
        } else {
            return false;
        }
    }
?>
