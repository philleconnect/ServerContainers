<?php
    function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }
    function moveFolder($dir, $new) {
        if (!file_exists($dir)) {
            return true;
        }
        return rename($dir, $new);
    }
    function fixHomedir($dir, $uid, $gid) {
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        //chmod($dir,0777);
        shell_exec('sudo chmod 0777 '.$dir);
        shell_exec('sudo chown '.$uid.':'.$gid.' '.$dir);
        if (is_writeable($dir)) {
            return true;
        } else {
            return false;
        }
    }
?>
