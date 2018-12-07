<?php
    include 'updatesamba.php';
    $request = "UPDATE groupfolders SET name = '".mysqli_real_escape_string($database, $client_request->changegroupfolder->name)."', students = ".mysqli_real_escape_string($database, $client_request->changegroupfolder->students).", teachers = ".mysqli_real_escape_string($database, $client_request->changegroupfolder->teachers).", writeable = ".mysqli_real_escape_string($database, $client_request->changegroupfolder->writeable)."  WHERE id = ".mysqli_real_escape_string($database, $client_request->changegroupfolder->id);
    if (mysqli_query($database, $request)) {
        if (updateSambaServer() == true) {
            $client_response['changegroupfolder'] = 'SUCCESS';
        } else {
            $client_response['changegroupfolder'] = 'ERR_UPDATE_SAMBA';
        }
    } else {
        $client_response['changegroupfolder'] = 'ERR_DATABASE_ERROR';
    }
?>
