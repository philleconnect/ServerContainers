<?php
    include 'updatesamba.php';
    if ($client_request->addgroupfolder->writeable) {
        $writeable = '1';
    } else {
        $writeable = '0';
    }
    if ($client_request->addgroupfolder->students) {
        $students = '1';
    } else {
        $students = '0';
    }
    if ($client_request->addgroupfolder->teachers) {
        $teachers = '1';
    } else {
        $teachers = '0';
    }
    if ($client_request->addgroupfolder->roomexchange) {
        $roomexchange = '1';
    } else {
        $roomexchange = '0';
    }
    $request = "INSERT INTO groupfolders SET name = '".mysqli_real_escape_string($database, $client_request->addgroupfolder->name)."', students = ".$students.", teachers = ".$teachers.", roomexchange = ".$roomexchange.", writeable = ".$writeable.", path = '".mysqli_real_escape_string($database, $client_request->addgroupfolder->path)."'";
    $query = mysqli_query($database, $request);
    if ($query) {
        if (updateSambaServer() == true) {
            if ($client_request->addgroupfolder->createfolder) {
                if (mkdir($client_request->addgroupfolder->path, 0777)) {
                    shell_exec('sudo chown 1000:1000 '.$client_request->addgroupfolder->path);
                    shell_exec('sudo chmod 777 '.$client_request->addgroupfolder->path);
                    $client_response['addgroupfolder'] = 'SUCCESS';
                } else {
                    $client_response['addgroupfolder'] = 'ERR_CREATE_FOLDER';
                }
            } else {
                $client_response['addgroupfolder'] = 'SUCCESS';
            }
        } else {
            $client_response['addgroupfolder'] = 'ERR_UPDATE_SAMBA';
        }
    } else {
        $client_response['addgroupfolder'] = 'ERR_DATABASE_ERROR';
    }
?>
